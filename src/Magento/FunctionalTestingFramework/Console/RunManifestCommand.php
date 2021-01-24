<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;

class RunManifestCommand extends Command
{
    /**
     * The return code. Determined by all tests that run.
     *
     * @var integer
     */
    private $returnCode = 0;

    /**
     * A list of tests that failed.
     * Eg: "tests/functional/tests/MFTF/_generated/default/AdminLoginTestCest.php:AdminLoginTest"
     *
     * @var string[]
     */
    private $failedTests = [];

    /**
     * Path for a failed test
     *
     * @var string
     */
    private $testsFailedFile;

    /**
     * Configure the run:manifest command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName("run:manifest")
            ->setDescription("runs a manifest file")
            ->addArgument("path", InputArgument::REQUIRED, "path to a manifest file");
    }

    /**
     * Executes the run:manifest command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws TestFrameworkException
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $testsOutputDir = FilePathFormatter::format(TESTS_BP) .
            "tests" .
            DIRECTORY_SEPARATOR .
            "_output" .
            DIRECTORY_SEPARATOR;

        $this->testsFailedFile = $testsOutputDir . "failed";

        $path = $input->getArgument("path");

        if (!file_exists($path)) {
            throw new TestFrameworkException("Could not find file $path. Check the path and try again.");
        }

        $manifestFile = file($path, FILE_IGNORE_NEW_LINES);

        // Delete the Codeception failed file just in case it exists from any previous test runs
        $this->deleteFailedFile();

        for ($line = 0; $line < count($manifestFile); $line++) {
            if (empty($manifestFile[$line])) {
                continue;
            }

            if ($line == count($manifestFile) - 1) {
                $this->runManifestLine($manifestFile[$line], $output, true);
            } else {
                $this->runManifestLine($manifestFile[$line], $output);
            }

            $this->aggregateFailed();
        }

        if (!empty($this->failedTests)) {
            $this->deleteFailedFile();
            $this->writeFailedFile();
        }

        return $this->returnCode;
    }

    /**
     * Runs a test (or group) line from the manifest file
     *
     * @param string          $manifestLine
     * @param OutputInterface $output
     * @param boolean         $exit
     * @return void
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Need this because of the unused $type variable in the closure
     */
    private function runManifestLine($manifestLine, $output, $exit = false)
    {
        if (getenv('ENABLE_PAUSE') === 'true') {
            $codeceptionCommand = BaseGenerateCommand::CODECEPT_RUN_FUNCTIONAL
                . '--verbose --steps --debug ';
            if (!$exit) {
                $codeceptionCommand .= BaseGenerateCommand::CODECEPT_RUN_OPTION_NO_EXIT;
            }
            $codeceptionCommand .= $manifestLine;
            $input = new StringInput($codeceptionCommand);
            $command = $this->getApplication()->find(BaseGenerateCommand::CODECEPT_RUN);
            $subReturnCode = $command->run($input, $output);
        } else {
            $codeceptionCommand = realpath(PROJECT_ROOT . "/vendor/bin/codecept")
                . " run functional --verbose --steps " . $manifestLine;

            // run the codecept command in a sub process
            $process = new Process($codeceptionCommand);
            $process->setWorkingDirectory(TESTS_BP);
            $process->setIdleTimeout(600);
            $process->setTimeout(0);
            $subReturnCode = $process->run(function ($type, $buffer) use ($output) {
                $output->write($buffer);
            });
        }

        $this->returnCode = max($this->returnCode, $subReturnCode);
    }

    /**
     * Keeps track of any tests that failed while running the manifest file.
     *
     * Each codecept command executions overwrites the failed file. Since we are running multiple codecept commands,
     * we need to hold on to any failures in order to write a final failed file containing all tests.
     *
     * @return void
     */
    private function aggregateFailed()
    {
        if (file_exists($this->testsFailedFile)) {
            $currentFile = file($this->testsFailedFile, FILE_IGNORE_NEW_LINES);
            $this->failedTests = array_merge(
                $this->failedTests,
                $currentFile
            );
        }
    }

    /**
     * Delete the Codeception failed file.
     *
     * @return void
     */
    private function deleteFailedFile()
    {
        if (file_exists($this->testsFailedFile)) {
            unlink($this->testsFailedFile);
        }
    }

    /**
     * Writes any tests that failed to the Codeception failed file.
     *
     * @return void
     */
    private function writeFailedFile()
    {
        foreach ($this->failedTests as $test) {
            file_put_contents($this->testsFailedFile, $test . "\n", FILE_APPEND);
        }
    }
}
