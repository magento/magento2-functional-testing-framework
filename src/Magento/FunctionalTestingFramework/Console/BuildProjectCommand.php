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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Magento\FunctionalTestingFramework\Util\Env\EnvProcessor;

class BuildProjectCommand extends Command
{
    /**
     * Env processor manages .env files.
     *
     * @var \Magento\FunctionalTestingFramework\Util\Env\EnvProcessor
     */
    private $envProcessor;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('build:project');
        $this->setDescription('Generate configuration files for the project. Build the Codeception project.');
        $this->envProcessor = new EnvProcessor(BP . DIRECTORY_SEPARATOR . '.env');
        $env = $this->envProcessor->getEnv();
        foreach ($env as $key => $value) {
            $this->addOption($key, null, InputOption::VALUE_REQUIRED, '', $value);
        }
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileSystem = new Filesystem();
        $fileSystem->copy(
            BP . DIRECTORY_SEPARATOR . 'codeception.dist.yml',
            BP . DIRECTORY_SEPARATOR . 'codeception.yml'
        );
        $output->writeln("codeception.yml configuration successfully applied.\n");
        $fileSystem->copy(
            BP . DIRECTORY_SEPARATOR . 'dev' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR .
            'functional' . DIRECTORY_SEPARATOR . 'MFTF.suite.dist.yml',
            BP . DIRECTORY_SEPARATOR . 'dev' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR .
            'functional' . DIRECTORY_SEPARATOR . 'MFTF.suite.yml'
        );
        $output->writeln("MFTF.suite.yml configuration successfully applied.\n");

        $setupEnvCommand = new SetupEnvCommand();
        $commandInput = [];
        $options = $input->getOptions();
        $env = array_keys($this->envProcessor->getEnv());
        foreach ($options as $key => $value) {
            if (in_array($key, $env)) {
                $commandInput['--' . $key] = $value;
            }
        }
        $commandInput = new ArrayInput($commandInput);
        $setupEnvCommand->run($commandInput, $output);

        $process = new Process('vendor/bin/codecept build');
        $process->run();
        if ($process->isSuccessful()) {
            $output->writeln("Codeception build run successfully.\n");
        }

        $output->writeln('<info>The project built successfully.</info>');
    }
}
