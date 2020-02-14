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

class RunTestGroupCommand extends BaseGenerateCommand
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('run:group')
            ->setDescription('Execute a set of tests referenced via group annotations')
            ->addOption(
                'skip-generate',
                'k',
                InputOption::VALUE_NONE,
                "only execute a group of tests without generating from source xml"
            )->addArgument(
                'groups',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'group names to be executed via codeception'
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
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $skipGeneration = $input->getOption('skip-generate');
        $force = $input->getOption('force');
        $groups = $input->getArgument('groups');
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

        // Create Mftf Configuration
        MftfApplicationConfig::create(
            $force,
            MftfApplicationConfig::EXECUTION_PHASE,
            $verbose,
            $debug,
            $allowSkipped
        );

        $this->setOutputStyle($input, $output);
        $this->showMftfNotices($output);

        if (!$skipGeneration) {
            $testConfiguration = $this->getGroupAndSuiteConfiguration($groups);
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

        $commandString = realpath(PROJECT_ROOT . '/vendor/bin/codecept') . ' run functional --verbose --steps';

        $exitCode = -1;
        $returnCodes = [];
        foreach ($groups as $group) {
            $codeceptionCommandString = $commandString . " -g {$group}";

            $process = new Process($codeceptionCommandString);
            $process->setWorkingDirectory(TESTS_BP);
            $process->setIdleTimeout(600);
            $process->setTimeout(0);

            $returnCodes[] = $process->run(
                function ($type, $buffer) use ($output) {
                    $output->write($buffer);
                }
            );
        }

        foreach ($returnCodes as $returnCode) {
            if ($returnCode != 0) {
                return $returnCode;
            }
            $exitCode = 0;
        }
        return $exitCode;
    }
}
