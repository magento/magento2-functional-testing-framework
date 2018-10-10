<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

class RunTestFailedCommand extends BaseGenerateCommand
{
    const DEFAULT_TEST_GROUP = 'default';

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('run:failed')
            ->setDescription('Execute a set of tests referenced via group annotations')
            ->addOption(
                'skip-generate',
                'k',
                InputOption::VALUE_NONE,
                "only execute a group of tests without generating from source xml"
            )->addOption(
                "force",
                'f',
                InputOption::VALUE_NONE,
                'force generation of tests regardless of Magento Instance Configuration'
            );

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
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $skipGeneration = $input->getOption('skip-generate');
        $force = $input->getOption('force');
//        $groups = $input->getArgument('groups');
        $remove = $input->getOption('remove');

        if ($skipGeneration and $remove) {
            // "skip-generate" and "remove" options cannot be used at the same time
            throw new TestFrameworkException(
                "\"skip-generate\" and \"remove\" options can not be used at the same time."
            );
        }

        // Create Mftf Configuration
        MftfApplicationConfig::create(
            $force,
            MftfApplicationConfig::GENERATION_PHASE,
            false,
            false
        );

        if (!$skipGeneration) {
            $testConfiguration = $this->getFailedTestList();
            $command = $this->getApplication()->find('generate:tests');
            $args = [
                '--tests' => $testConfiguration,
                '--force' => $force,
                '--remove' => $remove
            ];

            $command->run(new ArrayInput($args), $output);
        }

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
     * @return string[]
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

            $testList = file($failedTestPath,FILE_IGNORE_NEW_LINES);

            foreach ($testList as $test) {
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
        $testConfigurationJson = json_encode($failedTestDetails);
        return $testConfigurationJson;
    }
}
