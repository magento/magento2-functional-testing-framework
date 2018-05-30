<?php
// @codingStandardsIgnoreFile
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
            ->addArgument('name', InputArgument::REQUIRED | InputArgument::IS_ARRAY, "name of tests to generate and execute")
            ->addOption('skip-generate', 'k', InputOption::VALUE_NONE, "skip generation and execute existing test");
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Symfony\Component\Console\Exception\LogicException
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tests = $input->getArgument('name');
        $skipGeneration = $input->getOption('skip-generate') ?? false;

        if (!$skipGeneration) {
            $command = $this->getApplication()->find('generate:tests');
            $args = [
                '--tests' => json_encode([
                    'tests' => $tests,
                    'suites' => null
                ])
            ];

            $command->run(new ArrayInput($args), $output);
        }

        // we only generate relevant tests here so we can execute "all tests"
        $codeceptionCommand = realpath(PROJECT_ROOT . '/vendor/bin/codecept') . " run functional --verbose --steps";

        $process = new Process($codeceptionCommand);
        $process->setWorkingDirectory(TESTS_BP);
        $process->run(
            function ($type, $buffer) use ($output) {
                $output->write($buffer);
            }
        );
    }

}
