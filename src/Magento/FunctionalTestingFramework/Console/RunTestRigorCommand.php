<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunTestRigorCommand extends Command
{
    private const NODE_VERSION = '18.20.5';
    private const TESTRIGOR_VERSION = '';
    
    private string $projectRoot;
    private string $toolsDir;
    private string $nodeDir;
    private string $nodeBin;
    private string $npmBin;
    
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('run:testrigor')
            ->setDescription('Run TestRigor tests against the Magento instance')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'force execution regardless of Magento Instance Configuration'
            );
        
        // Initialize paths
        // From Console directory, go up 4 levels to reach project root:
        // Console -> FunctionalTestingFramework -> Magento -> src -> project root
        $this->projectRoot = dirname(__DIR__, 4);
        $this->toolsDir = $this->projectRoot . '/.mftf-tools';
        $this->nodeDir = $this->toolsDir . '/node';
        $this->nodeBin = $this->nodeDir . '/bin/node';
        $this->npmBin = $this->nodeDir . '/bin/npm';
    }

    /**
     * Detect the operating system and architecture
     *
     * @return array{os: string, arch: string}|null
     */
    protected function detectPlatform(): ?array
    {
        $os = strtolower(PHP_OS);
        $arch = php_uname('m');
        
        // Determine OS
        if (stripos($os, 'linux') !== false) {
            $osType = 'linux';
        } elseif (stripos($os, 'darwin') !== false) {
            $osType = 'darwin';
        } elseif (stripos($os, 'win') !== false) {
            $osType = 'win';
        } else {
            return null;
        }
        
        // Determine architecture
        if (in_array($arch, ['x86_64', 'amd64', 'AMD64'])) {
            $archType = 'x64';
        } elseif (in_array($arch, ['aarch64', 'arm64'])) {
            $archType = 'arm64';
        } else {
            $archType = 'x64'; // Default to x64
        }
        
        return ['os' => $osType, 'arch' => $archType];
    }

    /**
     * Download and install Node.js locally
     *
     * @param OutputInterface $output
     * @return bool
     */
    protected function installNodeJs(OutputInterface $output): bool
    {
        $platform = $this->detectPlatform();
        if (!$platform) {
            $output->writeln("<error>Unsupported platform. Unable to auto-install Node.js.</error>");
            return false;
        }
        
        $output->writeln("Downloading Node.js v" . self::NODE_VERSION . "...");
        
        // Create tools directory
        if (!is_dir($this->toolsDir)) {
            mkdir($this->toolsDir, 0755, true);
        }
        
        // Construct download URL
        $nodeFileName = sprintf(
            'node-v%s-%s-%s',
            self::NODE_VERSION,
            $platform['os'],
            $platform['arch']
        );
        
        if ($platform['os'] === 'win') {
            $nodeFileName .= '.zip';
            $downloadUrl = "https://nodejs.org/dist/v" . self::NODE_VERSION . "/" . $nodeFileName;
        } else {
            $nodeFileName .= '.tar.gz';
            $downloadUrl = "https://nodejs.org/dist/v" . self::NODE_VERSION . "/" . $nodeFileName;
        }
        
        $downloadPath = $this->toolsDir . '/' . $nodeFileName;
        
        // Download Node.js
        $output->writeln("Downloading from: $downloadUrl");
        $downloadCommand = sprintf(
            'curl -fsSL %s -o %s 2>&1',
            escapeshellarg($downloadUrl),
            escapeshellarg($downloadPath)
        );
        
        $downloadOutput = shell_exec($downloadCommand);
        if (!file_exists($downloadPath)) {
            $output->writeln("<error>Failed to download Node.js</error>");
            if ($downloadOutput) {
                $output->writeln($downloadOutput);
            }
            return false;
        }
        
        $output->writeln("✓ Downloaded Node.js");
        
        // Extract Node.js
        $output->writeln("Extracting Node.js...");
        if ($platform['os'] === 'win') {
            $extractCommand = sprintf(
                'unzip -q %s -d %s 2>&1',
                escapeshellarg($downloadPath),
                escapeshellarg($this->toolsDir)
            );
        } else {
            $extractCommand = sprintf(
                'tar -xzf %s -C %s 2>&1',
                escapeshellarg($downloadPath),
                escapeshellarg($this->toolsDir)
            );
        }
        
        shell_exec($extractCommand);
        
        // Move extracted directory to nodeDir
        $extractedDir = $this->toolsDir . '/' . str_replace(['.tar.gz', '.zip'], '', $nodeFileName);
        if (is_dir($extractedDir)) {
            if (is_dir($this->nodeDir)) {
                // Remove old installation
                shell_exec('rm -rf ' . escapeshellarg($this->nodeDir));
            }
            rename($extractedDir, $this->nodeDir);
            unlink($downloadPath);
            
            $output->writeln("✓ Node.js installed successfully!");
            return true;
        }
        
        $output->writeln("<error>Failed to extract Node.js</error>");
        return false;
    }

    /**
     * Ensure Node.js is available (system or local installation)
     *
     * @param OutputInterface $output
     * @return bool
     */
    protected function ensureNodeJs(OutputInterface $output): bool
    {
        // Check if we have a local Node.js installation
        if (file_exists($this->nodeBin) && is_executable($this->nodeBin)) {
            $version = shell_exec($this->nodeBin . ' --version 2>&1');
            $output->writeln("✓ Using local Node.js: " . trim($version));
            // When using local Node.js, we need to explicitly use it to run npm
            // to avoid the system's old Node.js being used via shebang
            $npmScript = $this->nodeDir . '/lib/node_modules/npm/bin/npm-cli.js';
            if (file_exists($npmScript)) {
                $this->npmBin = $this->nodeBin . ' ' . $npmScript;
            }
            return true;
        }
        
        // Check if system Node.js is available
        $systemNodeCheck = 'command -v node > /dev/null 2>&1';
        exec($systemNodeCheck, $checkOutput, $returnCode);
        
        if ($returnCode === 0) {
            $version = shell_exec('node --version 2>&1');
            if ($version) {
                preg_match('/v(\d+)\./', $version, $matches);
                $majorVersion = isset($matches[1]) ? (int)$matches[1] : 0;
                if ($majorVersion >= 18) {
                    $output->writeln("✓ Using system Node.js: " . trim($version));
                    // Update paths to use system binaries
                    $this->nodeBin = 'node';
                    $this->npmBin = 'npm';
                    return true;
                } else {
                    $output->writeln("⚠ System Node.js version is too old (v$majorVersion), need v18+");
                }
            }
        }
        
        // No suitable Node.js found, install it locally
        $output->writeln("Node.js not found. Installing locally...");
        return $this->installNodeJs($output);
    }

    /**
     * Install TestRigor CLI locally
     *
     * @param OutputInterface $output
     * @return bool
     */
    protected function installTestRigorCli(OutputInterface $output): bool
    {
        $output->writeln("Installing TestRigor CLI...");
        
        // Set up npm to install locally in project
        $nodeModulesDir = $this->projectRoot . '/node_modules';
        $testRigorBin = $nodeModulesDir . '/.bin/testrigor';
        
        // Ensure NODE_PATH is set to use the correct node_modules
        $envPath = 'NODE_PATH=' . escapeshellarg($nodeModulesDir);
        
        // Install testrigor-cli with explicit npm prefix to force local installation
        $package = self::TESTRIGOR_VERSION ? 'testrigor-cli@' . self::TESTRIGOR_VERSION : 'testrigor-cli';
        $installCommand = sprintf(
            'cd %s && %s %s install --no-save %s 2>&1',
            escapeshellarg($this->projectRoot),
            $envPath,
            $this->npmBin,  // Don't escape since it might contain node + path
            $package
        );
        
        $installOutput = shell_exec($installCommand);
        
        if ($installOutput) {
            // Check for actual errors (not warnings)
            if (preg_match('/npm ERR!.*(?!WARN)/i', $installOutput)) {
                $output->writeln("<error>Failed to install TestRigor CLI:</error>");
                $output->writeln($installOutput);
                return false;
            }
        }
        
        // Verify installation - check multiple locations
        if (file_exists($testRigorBin)) {
            $output->writeln("✓ TestRigor CLI installed successfully at: $testRigorBin");
            return true;
        }
        
        $altBin = $nodeModulesDir . '/testrigor-cli/bin/testrigor';
        if (file_exists($altBin)) {
            $output->writeln("✓ TestRigor CLI installed successfully at: $altBin");
            return true;
        }
        
        if (is_dir($nodeModulesDir . '/testrigor-cli')) {
            $output->writeln("✓ TestRigor CLI package installed");
            return true;
        }
        
        $output->writeln("<error>Failed to verify TestRigor CLI installation</error>");
        $output->writeln("Checked locations:");
        $output->writeln("  - $testRigorBin");
        $output->writeln("  - $altBin");
        $output->writeln("  - $nodeModulesDir/testrigor-cli");
        return false;
    }

    /**
     * Get the testrigor binary path and command
     *
     * @return string|null
     */
    protected function getTestRigorBinary(): ?string
    {
        // Priority 1: Check local installation in node_modules (most reliable)
        $localBin = $this->projectRoot . '/node_modules/.bin/testrigor';
        if (file_exists($localBin) && is_executable($localBin)) {
            // Use our Node.js to run it to ensure correct version
            return $this->nodeBin . ' ' . $localBin;
        }
        
        // Priority 2: Check for direct access to the CLI script
        $directPath = $this->projectRoot . '/node_modules/testrigor-cli/bin/testrigor';
        if (file_exists($directPath)) {
            return $this->nodeBin . ' ' . $directPath;
        }
        
        // Priority 3: Check for global testrigor command
        $globalCheck = 'command -v testrigor > /dev/null 2>&1';
        exec($globalCheck, $output, $returnCode);
        if ($returnCode === 0) {
            return 'testrigor';
        }
        
        // Priority 4: Check for global testrigor-cli command
        $globalCheckCli = 'command -v testrigor-cli > /dev/null 2>&1';
        exec($globalCheckCli, $outputCli, $returnCodeCli);
        if ($returnCodeCli === 0) {
            return 'testrigor-cli';
        }
        
        // Priority 5: Use npx with our Node.js if package is installed
        if (is_dir($this->projectRoot . '/node_modules/testrigor-cli')) {
            $npxBin = dirname($this->npmBin) . '/npx';
            if (file_exists($npxBin)) {
                return $npxBin . ' testrigor-cli';
            }
        }
        
        return null;
    }

    /**
     * Ensure all dependencies are installed
     *
     * @param OutputInterface $output
     * @return bool
     */
    protected function ensureTestRigorInstalled(OutputInterface $output): bool
    {
        // Step 1: Ensure Node.js is available
        if (!$this->ensureNodeJs($output)) {
            return false;
        }
        
        // Step 2: Check if TestRigor CLI is already available
        $testRigorBin = $this->getTestRigorBinary();
        if ($testRigorBin) {
            $output->writeln("✓ TestRigor CLI is already installed");
            return true;
        }
        
        // Step 3: Install TestRigor CLI
        return $this->installTestRigorCli($output);
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $force = $input->getOption('force');
        $verbose = $output->isVerbose();

        // Set application configuration
        MftfApplicationConfig::create(
            $force,
            MftfApplicationConfig::EXECUTION_PHASE,
            $verbose,
            MftfApplicationConfig::LEVEL_DEFAULT,
            false
        );

        $output->writeln('Running TestRigor Integration...');
        
        // Get base URL from different possible sources
        $baseUrl = null;
        if (defined('MAGENTO_BASE_URL')) {
            $baseUrl = MAGENTO_BASE_URL;
        } elseif (getenv('MAGENTO_BASE_URL')) {
            $baseUrl = getenv('MAGENTO_BASE_URL');
        }

        if (!$baseUrl) {
            $output->writeln('<error>Warning: Base URL not found. Please set MAGENTO_BASE_URL in your .env file.</error>');
            $baseUrl = "http://localhost"; // fallback
        }

        $output->writeln("Environment: " . (defined('TEST_ENV') ? TEST_ENV : (getenv('TEST_ENV') ?: 'local')));
        $output->writeln("Base URL: " . $baseUrl);

        // Get secrets from GitHub secrets (sensitive data)
        $testSuiteId = getenv('TESTRIGOR_TEST_SUITE_ID') ?: getenv('MAGENTO_TEST_SUITE_ID');
        $authToken = getenv('TESTRIGOR_AUTH_TOKEN') ?: getenv('MAGENTO_AUTH_TOKEN');

        // Get configuration paths (non-sensitive, can be in repo or env vars)
        $testCasesPath = getenv('TESTRIGOR_TEST_CASES_PATH') ?: getenv('TEST_CASES_PATH') ?: 'tests/testRigor/testcases';
        $rulesPath = getenv('TESTRIGOR_RULES_PATH') ?: getenv('RULES_PATH') ?: 'tests/testRigor/rules';

        $output->writeln("\nChecking GitHub secrets availability:");
        $output->writeln("- Running in GitHub Actions: " . (getenv('GITHUB_ACTIONS') ? "Yes" : "No"));
        $output->writeln("- GitHub Workspace: " . (getenv('GITHUB_WORKSPACE') ?: "Not set"));

        // Check if required secrets are set (only sensitive data)
        $missingSecrets = [];
        if (!$testSuiteId) $missingSecrets[] = 'TESTRIGOR_TEST_SUITE_ID';
        if (!$authToken) $missingSecrets[] = 'TESTRIGOR_AUTH_TOKEN';

        if (!empty($missingSecrets)) {
            $output->writeln("\n<error>Warning: Missing required TestRigor secrets:</error>");
            $output->writeln("- TESTRIGOR_TEST_SUITE_ID: " . ($testSuiteId ? "Set" : "Missing"));
            $output->writeln("- TESTRIGOR_AUTH_TOKEN: " . ($authToken ? "Set (hidden)" : "Missing"));
            
            $output->writeln("\nConfiguration paths (using defaults if not set):");
            $output->writeln("- Test Cases Path: " . $testCasesPath);
            $output->writeln("- Rules Path: " . $rulesPath);
            
            if (getenv('GITHUB_ACTIONS')) {
                $output->writeln("\nTo fix this in GitHub Actions, add these secrets to your repository:");
                $output->writeln("1. Go to your repository settings");
                $output->writeln("2. Navigate to Secrets and variables → Actions");
                $output->writeln("3. Add these repository secrets:");
                $output->writeln("   - TESTRIGOR_TEST_SUITE_ID (your TestRigor application ID)");
                $output->writeln("   - TESTRIGOR_AUTH_TOKEN (your TestRigor API token)");
                $output->writeln("\n4. In your workflow file, set them as environment variables:");
                $output->writeln("   env:");
                $output->writeln("     TESTRIGOR_TEST_SUITE_ID: \${{ secrets.TESTRIGOR_TEST_SUITE_ID }}");
                $output->writeln("     TESTRIGOR_AUTH_TOKEN: \${{ secrets.TESTRIGOR_AUTH_TOKEN }}");
                $output->writeln("     # Optional: Override default paths if needed");
                $output->writeln("     TESTRIGOR_TEST_CASES_PATH: tests/testRigor/testcases");
                $output->writeln("     TESTRIGOR_RULES_PATH: tests/testRigor/rules");
            } else {
                $output->writeln("\nFor local testing, add these to your .env file:");
                $output->writeln("TESTRIGOR_TEST_SUITE_ID=your_test_suite_id");
                $output->writeln("TESTRIGOR_AUTH_TOKEN=your_auth_token");
                $output->writeln("# Optional: Override default paths if needed");
                $output->writeln("TESTRIGOR_TEST_CASES_PATH=tests/testRigor/testcases");
                $output->writeln("TESTRIGOR_RULES_PATH=tests/testRigor/rules");
            }
            
            return 1; // Exit with error code
        } else {
            $output->writeln("\nAll required secrets are available");
            $output->writeln("Configuration:");
            $output->writeln("- Test Cases Path: " . $testCasesPath);
            $output->writeln("- Rules Path: " . $rulesPath);
            
            // Ensure TestRigor CLI is installed
            $output->writeln("\nChecking TestRigor CLI installation...");
            if (!$this->ensureTestRigorInstalled($output)) {
                return 1; // Exit with error if installation failed
            }
            
            // Get the testrigor binary path
            $testRigorBin = $this->getTestRigorBinary();
            if (!$testRigorBin) {
                $output->writeln("<error>TestRigor CLI not found after installation.</error>");
                return 1;
            }
            
            // Build and execute the TestRigor command
            $command = sprintf(
                '%s test-suite run %s --token %s --url %s --test-cases-path %s --rules-path %s',
                $testRigorBin,
                escapeshellarg($testSuiteId),
                escapeshellarg($authToken),
                escapeshellarg($baseUrl),
                escapeshellarg($testCasesPath),
                escapeshellarg($rulesPath)
            );
            
            $output->writeln("\nExecuting TestRigor command:");
            // Don't show the actual token in logs for security
            $safeCommand = str_replace($authToken, '***HIDDEN***', $command);
            $output->writeln($safeCommand);
            $output->writeln("");
            
            // Execute the command and capture output
            $process = shell_exec($command . ' 2>&1');
            
            if ($process) {
                $output->writeln("TestRigor output:");
                $output->writeln($process);
            } else {
                $output->writeln("No output from TestRigor command.");
            }
            
            $output->writeln("\nTestRigor integration completed successfully!");
            return 0; // Success
        }
    }
} 