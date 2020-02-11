<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

class RunTestCommand extends BaseGenerateCommand
{
    /**
     * The return code. Determined by all tests that run.
     *
     * @var integer
     */
    private $returnCode = 0;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName("run:test")
            ->setDescription("generation and execution of test(s) defined in xml")
            ->addArgument(
                'name',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                "name of tests to generate and execute"
            )->addOption(
                'skip-generate',
                'k',
                InputOption::VALUE_NONE,
                "skip generation and execute existing test"
            );

        parent::configure();
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return integer
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tests = $input->getArgument('name');
        $skipGeneration = $input->getOption('skip-generate');
        $force = $input->getOption('force');
        $remove = $input->getOption('remove');
        $debug = $input->getOption('debug') ?? MftfApplicationConfig::LEVEL_DEVELOPER; // for backward compatibility
        $allowSkipped = $input->getOption('allow-skipped');
        $verbose = $output->isVerbose();

        if ($skipGeneration and $remove) {
            // "skip-generate" and "remove" options cannot be used at the same time
            throw new TestFrameworkException(
                "\"skip-generate\" and \"remove\" options can not be used at the same time."
            );
        }

        // Set application configuration so we can references the user options in our framework
        MftfApplicationConfig::create(
            $force,
            MftfApplicationConfig::EXECUTION_PHASE,
            $verbose,
            $debug,
            $allowSkipped
        );

        $this->setOutputStyle($input, $output);
        $this->showMftfNotices($output);

        $testConfiguration = $this->getTestAndSuiteConfiguration($tests);

        if (!$skipGeneration) {
            $command = $this->getApplication()->find('generate:tests');
            $args = [
                '--tests' => $testConfiguration,
                '--force' => $force,
                '--remove' => $remove,
                '--debug' => $debug,
                '--allow-skipped' => $allowSkipped,
                '-v' => $verbose
            ];
            $command->run(new ArrayInput($args), $output);
        }

        $testConfigArray = json_decode($testConfiguration, true);

        if (isset($testConfigArray['tests'])) {
            $this->runTests($testConfigArray['tests'], $output);
        }

        if (isset($testConfigArray['suites'])) {
            $this->runTestsInSuite($testConfigArray['suites'], $output);
        }

        return $this->returnCode;
    }

    /**
     * Run tests not referenced in suites
     *
     * @param array           $tests
     * @param OutputInterface $output
     * @return void
     * @throws TestFrameworkException
     */
    private function runTests(array $tests, OutputInterface $output)
    {
        $codeceptionCommand = realpath(PROJECT_ROOT . '/vendor/bin/codecept') . ' run functional ';
        $testsDirectory = FilePathFormatter::format(TESTS_MODULE_PATH) .
            TestGenerator::GENERATED_DIR .
            DIRECTORY_SEPARATOR .
            TestGenerator::DEFAULT_DIR .
            DIRECTORY_SEPARATOR ;

        foreach ($tests as $test) {
            $testName = $test . 'Cest.php';
            if (!realpath($testsDirectory . $testName)) {
                throw new TestFrameworkException(
                    $testName . " is not available under " . $testsDirectory
                );
            }
            $fullCommand = $codeceptionCommand . $testsDirectory . $testName . ' --verbose --steps';
            $this->returnCode = max($this->returnCode, $this->executeTestCommand($fullCommand, $output));
        }
    }

    /**
     * Run tests referenced in suites within suites' context.
     *
     * @param array           $suitesConfig
     * @param OutputInterface $output
     * @return void
     */
    private function runTestsInSuite(array $suitesConfig, OutputInterface $output)
    {
        $codeceptionCommand = realpath(PROJECT_ROOT . '/vendor/bin/codecept') . ' run functional --verbose --steps ';
        //for tests in suites, run them as a group to run before and after block
        foreach (array_keys($suitesConfig) as $suite) {
            $fullCommand = $codeceptionCommand . " -g {$suite}";
            $this->returnCode = max($this->returnCode, $this->executeTestCommand($fullCommand, $output));
        }
    }

    /**
     * Runs the codeception test command and returns exit code
     *
     * @param string          $command
     * @param OutputInterface $output
     * @return integer
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function executeTestCommand(string $command, OutputInterface $output)
    {
        $process = new Process($command);
        $process->setWorkingDirectory(TESTS_BP);
        $process->setIdleTimeout(600);
        $process->setTimeout(0);
        return $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });
    }
}
