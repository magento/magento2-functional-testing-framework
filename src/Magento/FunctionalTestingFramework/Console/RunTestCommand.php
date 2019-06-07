<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

class RunTestCommand extends BaseGenerateCommand
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
            )->addOption(
                'debug',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Run extra validation when running tests. Use option \'none\' to turn off debugging -- 
                 added for backward compatibility, will be removed in the next MAJOR release',
                MftfApplicationConfig::LEVEL_DEFAULT
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
        $tests = $input->getArgument('name');
        $skipGeneration = $input->getOption('skip-generate');
        $force = $input->getOption('force');
        $remove = $input->getOption('remove');
        $debug = $input->getOption('debug') ?? MftfApplicationConfig::LEVEL_DEVELOPER; // for backward compatibility

        if ($skipGeneration and $remove) {
            // "skip-generate" and "remove" options cannot be used at the same time
            throw new TestFrameworkException(
                "\"skip-generate\" and \"remove\" options can not be used at the same time."
            );
        }

        if (!$skipGeneration) {
            $command = $this->getApplication()->find('generate:tests');
            $args = [
                '--tests' => json_encode([
                    'tests' => $tests,
                    'suites' => null
                ]),
                '--force' => $force,
                '--remove' => $remove,
                '--debug' => $debug
            ];
            $command->run(new ArrayInput($args), $output);
        }

        // we only generate relevant tests here so we can execute "all tests"
        $codeceptionCommand = realpath(PROJECT_ROOT . '/vendor/bin/codecept') . " run functional --verbose --steps";

        $process = new Process($codeceptionCommand);
        $process->setWorkingDirectory(TESTS_BP);
        $process->setIdleTimeout(600);
        $process->setTimeout(0);

        return $process->run(
            function ($type, $buffer) use ($output) {
                $output->write($buffer);
            }
        );
    }
}
