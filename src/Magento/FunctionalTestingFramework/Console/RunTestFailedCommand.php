<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

class RunTestFailedCommand extends BaseGenerateCommand
{
    /**
     * Default Test group to signify not in suite
     */
    const DEFAULT_TEST_GROUP = 'default';

    const TESTS_OUTPUT_DIR = TESTS_BP .
    DIRECTORY_SEPARATOR .
    "tests" .
    DIRECTORY_SEPARATOR .
    "_output" .
    DIRECTORY_SEPARATOR;

    const TESTS_FAILED_FILE = self::TESTS_OUTPUT_DIR . "failed";
    const TESTS_RERUN_FILE = self::TESTS_OUTPUT_DIR . "rerun_tests";

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('run:failed')
            ->setDescription('Execute a set of tests referenced via failed file');

        parent::configure();
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return integer|null|void
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Create Mftf Configuration
        MftfApplicationConfig::create(
            false,
            MftfApplicationConfig::GENERATION_PHASE,
            false,
            false
        );

        $testConfiguration = $this->getFailedTestList();

        if ($testConfiguration === null) {
            return null;
        }

        $command = $this->getApplication()->find('generate:tests');
        $args = ['--tests' => $testConfiguration, '--remove' => true];

        $command->run(new ArrayInput($args), $output);

        $codeceptionCommand = realpath(PROJECT_ROOT . '/vendor/bin/codecept') . ' run functional --verbose --steps';

        $process = new Process($codeceptionCommand);
        $process->setWorkingDirectory(TESTS_BP);
        $process->setIdleTimeout(600);
        $process->setTimeout(0);
        $process->run(
            function ($type, $buffer) use ($output) {
                $output->write($buffer);
            }
        );
    }

    /**
     * Returns a json string of tests that failed on the last run
     *
     * @return string
     */
    private function getFailedTestList()
    {
        $failedTestPath = TESTS_BP .
            DIRECTORY_SEPARATOR .
            "tests" .
            DIRECTORY_SEPARATOR .
            "_output" .
            DIRECTORY_SEPARATOR .
            "failed";

        $failedTestDetails = ['tests' => [], 'suites' => []];

        if (realpath($failedTestPath)) {
            $testList = $this->readFailedTestFile($failedTestPath);

            foreach ($testList as $test) {
                $this->writeFailedTestToFile($test);
                $testInfo = explode(DIRECTORY_SEPARATOR, $test);
                $testName = explode(":", $testInfo[count($testInfo) - 1])[1];
                $suiteName = $testInfo[count($testInfo) - 2];

                if ($suiteName == self::DEFAULT_TEST_GROUP) {
                    array_push($failedTestDetails['tests'], $testName);
                } else {
                    $failedTestDetails['suites'] = array_merge_recursive(
                        $failedTestDetails['suites'],
                        [$suiteName => [$testName]]
                    );
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
     * Returns an array of tests read from the failed test file in _output
     *
     * @param string $filePath
     * @return array|boolean
     */
    private function readFailedTestFile($filePath)
    {
        return file($filePath, FILE_IGNORE_NEW_LINES);
    }

    /**
     * Writes the test name to a file if it does not already exist
     *
     * @param string $test
     * @return void
     */
    private function writeFailedTestToFile($test)
    {
        if (realpath(self::TESTS_RERUN_FILE)) {
            if (strpos(file_get_contents(self::TESTS_RERUN_FILE), $test) == false) {
                file_put_contents(self::TESTS_RERUN_FILE, $test . "\n", FILE_APPEND);
            }
        } else {
            file_put_contents(self::TESTS_RERUN_FILE, $test . "\n");
        }
    }
}
