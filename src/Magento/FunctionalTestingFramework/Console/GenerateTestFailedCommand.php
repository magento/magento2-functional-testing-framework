<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Symfony\Component\Console\Input\InputOption;

class GenerateTestFailedCommand extends BaseGenerateCommand
{
    /**
     * Default Test group to signify not in suite
     */
    const DEFAULT_TEST_GROUP = 'default';

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('generate:failed')
            ->setDescription('Generate a set of tests failed');

        parent::configure();
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return integer
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $force = $input->getOption('force');
        $debug = $input->getOption('debug') ?? MftfApplicationConfig::LEVEL_DEVELOPER; // for backward compatibility
        $allowSkipped = $input->getOption('allow-skipped');
        $verbose = $output->isVerbose();

        // Create Mftf Configuration
        MftfApplicationConfig::create(
            $force,
            MftfApplicationConfig::EXECUTION_PHASE,
            $verbose,
            $debug,
            $allowSkipped
        );

        $testsFailedFile = $this->getTestsOutputDir() . self::FAILED_FILE;
        $testsReRunFile = $this->getTestsOutputDir() . "rerun_tests";
        $testConfiguration = $this->getFailedTestList($testsFailedFile, $testsReRunFile);

        if ($testConfiguration === null) {
            // No failed tests found, no tests generated
            $this->removeGeneratedDirectory($output, $verbose);
            return 0;
        }

        $command = $this->getApplication()->find('generate:tests');
        $args = [
            '--tests' => $testConfiguration,
            '--force' => $force,
            '--remove' => true,
            '--debug' => $debug,
            '--allow-skipped' => $allowSkipped,
            '-v' => $verbose
        ];
        $command->run(new ArrayInput($args), $output);
        $output->writeln("Test Failed Generated, now run:failed command");
        return 0;
    }

    /**
     * Returns a json string of tests that failed on the last run
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getFailedTestList($testsFailedFile, $testsReRunFile)
    {
        $failedTestDetails = ['tests' => [], 'suites' => []];

        $testList = $this->readFailedTestFile($testsFailedFile);

        if (!empty($testList)) {
            foreach ($testList as $test) {
                if (!empty($test)) {
                    $this->writeFailedTestToFile($test, $testsReRunFile);
                    $testInfo = explode(DIRECTORY_SEPARATOR, $test);
                    $testName = isset($testInfo[count($testInfo) - 1][1])
                        ? explode(":", $testInfo[count($testInfo) - 1])[1]
                        : [];
                    $suiteName = isset($testInfo[count($testInfo) - 2])
                        ?  $testInfo[count($testInfo) - 2]
                        : [];
                    if ($suiteName === self::DEFAULT_TEST_GROUP) {
                        array_push($failedTestDetails['tests'], $testName);
                    } else {
                        $suiteName = $this->sanitizeSuiteName($suiteName);
                        $failedTestDetails['suites'] = array_merge_recursive(
                            $failedTestDetails['suites'],
                            [$suiteName => [$testName]]
                        );
                    }
                }
            }
        }
        if (empty($failedTestDetails['tests']) & empty($failedTestDetails['suites'])) {
            return null;
        }
        if (empty($failedTestDetails['tests'])) {
            $failedTestDetails['tests'] = null;
        }
        if (empty($failedTestDetails['suites'])) {
            $failedTestDetails['suites'] = null;
        }
        $testConfigurationJson = json_encode($failedTestDetails);
        return $testConfigurationJson;
    }

    /**
     * Trim potential suite_parallel_0_G to suite_parallel
     *
     * @param string $suiteName
     * @return string
     */
    private function sanitizeSuiteName($suiteName)
    {
        $suiteNameArray = explode("_", $suiteName);
        if (array_pop($suiteNameArray) === 'G') {
            if (is_numeric(array_pop($suiteNameArray))) {
                $suiteName = implode("_", $suiteNameArray);
            }
        }
        return $suiteName;
    }

    /**
     * Returns an array of tests read from the failed test file in _output
     *
     * @param string $filePath
     * @return array|boolean
     */
    public function readFailedTestFile($filePath)
    {
        if (realpath($filePath)) {
            return file($filePath, FILE_IGNORE_NEW_LINES);
        }
        return "";
    }

    /**
     * Writes the test name to a file if it does not already exist
     *
     * @param string $test
     * @param string $filePath
     * @return void
     */
    public function writeFailedTestToFile($test, $filePath)
    {
        if (file_exists($filePath)) {
            if (strpos(file_get_contents($filePath), $test) === false) {
                file_put_contents($filePath, "\n" . $test, FILE_APPEND);
            }
        } else {
            file_put_contents($filePath, $test . "\n");
        }
    }
}
