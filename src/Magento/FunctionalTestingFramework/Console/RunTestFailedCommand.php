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
        $this->testsFailedFile = $this->getTestsOutputDir() . self::FAILED_FILE;
        $this->testsReRunFile = $this->getTestsOutputDir() . "rerun_tests";

        $this->testsManifestFile= FilePathFormatter::format(TESTS_MODULE_PATH) .
            "_generated" .
            DIRECTORY_SEPARATOR .
            "testManifest.txt";

        $testManifestList = $this->readTestManifestFile();
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

                $process = new Process($codeceptionCommand);
                $process->setWorkingDirectory(TESTS_BP);
                $process->setIdleTimeout(600);
                $process->setTimeout(0);
                $returnCode = max($returnCode, $process->run(
                    function ($type, $buffer) use ($output) {
                        $output->write($buffer);
                    }
                ));
                $process->__destruct();
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
