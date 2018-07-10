<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Process\Process;

class RunTestCommand extends Command
{
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
            )->addOption('skip-generate', 'k', InputOption::VALUE_NONE, "skip generation and execute existing test")
            ->addOption(
                "force",
                'f',
                InputOption::VALUE_NONE,
                'force generation of tests regardless of Magento Instance Configuration'
            );
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
        $tests = $input->getArgument('name');
        $skipGeneration = $input->getOption('skip-generate') ?? false;
        $force = $input->getOption('force') ?? false;

        if (!$skipGeneration) {
            $command = $this->getApplication()->find('generate:tests');
            $args = [
                '--tests' => json_encode([
                    'tests' => $tests,
                    'suites' => null
                ])
            ];
            if ($force) {
                $args['--force'] = true;
            }

            $command->run(new ArrayInput($args), $output);
        }

        // we only generate relevant tests here so we can execute "all tests"
        $codeceptionCommand = realpath(PROJECT_ROOT . '/vendor/bin/codecept') . " run functional --verbose --steps";

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
}
