<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RunTestFailedCommand extends BaseGenerateCommand
{
    const DEFAULT_TEST_GROUP = 'default';

    /**
     * @var string
     */
    private $testsReRunFile = "rerun_tests";

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
        $this->testsFailedFile = $this->getTestsOutputDir() . self::FAILED_FILE;
        $this->testsReRunFile = $this->getTestsOutputDir() . "rerun_tests";

        $failedTests = $this->readFailedTestFile($this->testsFailedFile);
        $testManifestList = $this->filterTestsForExecution($failedTests);

        if (empty($testManifestList)) {
            // If there is no tests in manifest then we have nothing to execute.
            return 0;
        }
        $returnCode = 0;
        for ($i = 0; $i < count($testManifestList); $i++) {
            if ($this->pauseEnabled()) {
                $codeceptionCommand = self::CODECEPT_RUN_FUNCTIONAL . $testManifestList[$i] . ' --debug ';
                if ($i !== count($testManifestList) - 1) {
                    $codeceptionCommand .= self::CODECEPT_RUN_OPTION_NO_EXIT;
                }
                $returnCode = $this->codeceptRunTest($codeceptionCommand, $output);
            } else {
                $codeceptionCommand = realpath(PROJECT_ROOT . '/vendor/bin/codecept') . ' run functional ';
                $codeceptionCommand .= $testManifestList[$i];

                $process = Process::fromShellCommandline($codeceptionCommand);
                $process->setWorkingDirectory(TESTS_BP);
                $process->setIdleTimeout(600);
                $process->setTimeout(0);
                $returnCode = max($returnCode, $process->run(
                    function ($type, $buffer) use ($output) {
                        $output->write($buffer);
                    }
                ));
                $process->__destruct();
                unset($process);
            }

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
     * Returns a list of tests/suites which should have an additional run.
     *
     * @param array $failedTests
     * @return array
     */
    private function filterTestsForExecution(array $failedTests): array
    {
        $testsOrGroupsToRerun = [];

        foreach ($failedTests as $test) {
            if (!empty($test)) {
                $this->writeFailedTestToFile($test, $this->testsReRunFile);
                $testInfo = explode(DIRECTORY_SEPARATOR, $test);
                $suiteName = $testInfo[count($testInfo) - 2];
                list($testPath) = explode(":", $test);

                if ($suiteName === self::DEFAULT_TEST_GROUP) {
                    $testsOrGroupsToRerun[] = $testPath;
                } else {
                    $group = "-g " . $suiteName;
                    if (!in_array($group, $testsOrGroupsToRerun)) {
                        $testsOrGroupsToRerun[] = $group;
                    }
                }
            }
        }

        return $testsOrGroupsToRerun;
    }

    /**
     * Returns an array of tests read from the failed test file in _output
     *
     * @param string $filePath
     * @return array
     */
    private function readFailedTestFile(string $filePath): array
    {
        $data = [];
        if (file_exists($filePath)) {
            $file = file($filePath, FILE_IGNORE_NEW_LINES);
            $data = $file === false ? [] : $file;
        }
        return $data;
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
