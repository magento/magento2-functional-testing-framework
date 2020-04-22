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

class RunTestFailedCommand extends BaseGenerateCommand
{
    /**
     * Default Test group to signify not in suite
     */
    const DEFAULT_TEST_GROUP = 'default';

    /**
     * @var string
     */
    private $testsFailedFile;

    /**
     * @var string
     */
    private $testsReRunFile;

    /**
     * @var string
     */
    private $testsManifestFile;

    /**
     * @var array
     */
    private $failedList = [];

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
     * @return integer
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $testsOutputDir = FilePathFormatter::format(TESTS_BP) .
            "tests" .
            DIRECTORY_SEPARATOR .
            "_output" .
            DIRECTORY_SEPARATOR;

        $this->testsFailedFile = $testsOutputDir . "failed";
        $this->testsReRunFile = $testsOutputDir . "rerun_tests";
        $this->testsManifestFile= FilePathFormatter::format(TESTS_MODULE_PATH) .
            "_generated" .
            DIRECTORY_SEPARATOR .
            "testManifest.txt";

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

        $testConfiguration = $this->getFailedTestList();

        if ($testConfiguration === null) {
            // no failed tests found, run is a success
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

        $testManifestList = $this->readTestManifestFile();
        $returnCode = 0;
        foreach ($testManifestList as $testCommand) {
            $codeceptionCommand = realpath(PROJECT_ROOT . '/vendor/bin/codecept') . ' run functional ';
            $codeceptionCommand .= $testCommand;

            $process = new Process($codeceptionCommand);
            $process->setWorkingDirectory(TESTS_BP);
            $process->setIdleTimeout(600);
            $process->setTimeout(0);
            $returnCode = max($returnCode, $process->run(
                function ($type, $buffer) use ($output) {
                    $output->write($buffer);
                }
            ));
            if (file_exists($this->testsFailedFile)) {
                $this->failedList = array_merge(
                    $this->failedList,
                    $this->readFailedTestFile($this->testsFailedFile)
                );
            }
        }
        foreach ($this->failedList as $test) {
            $this->writeFailedTestToFile($test, $this->testsFailedFile);
        }

        return $returnCode;
    }

    /**
     * Returns a json string of tests that failed on the last run
     *
     * @return string
     */
    private function getFailedTestList()
    {
        $failedTestDetails = ['tests' => [], 'suites' => []];

        if (realpath($this->testsFailedFile)) {
            $testList = $this->readFailedTestFile($this->testsFailedFile);

            foreach ($testList as $test) {
                if (!empty($test)) {
                    $this->writeFailedTestToFile($test, $this->testsReRunFile);
                    $testInfo = explode(DIRECTORY_SEPARATOR, $test);
                    $testName = explode(":", $testInfo[count($testInfo) - 1])[1];
                    $suiteName = $testInfo[count($testInfo) - 2];

                    if ($suiteName == self::DEFAULT_TEST_GROUP) {
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
        if (array_pop($suiteNameArray) == 'G') {
            if (is_numeric(array_pop($suiteNameArray))) {
                $suiteName = implode("_", $suiteNameArray);
            }
        }
        return $suiteName;
    }

    /**
     * Returns an array of run commands read from the manifest file created post generation
     *
     * @return array|boolean
     */
    private function readTestManifestFile()
    {
        return file($this->testsManifestFile, FILE_IGNORE_NEW_LINES);
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
     * @param string $filePath
     * @return void
     */
    private function writeFailedTestToFile($test, $filePath)
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
