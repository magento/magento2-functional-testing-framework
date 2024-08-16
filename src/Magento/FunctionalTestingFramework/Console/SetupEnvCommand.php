<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\FunctionalTestingFramework\Util\Env\EnvProcessor;

class SetupEnvCommand extends Command
{
    private const SUCCESS_EXIT_CODE = 0;

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
     * @throws TestFrameworkException
     */
    protected function configure()
    {
        $this->setName('setup:env')
            ->setDescription("Generate .env file.");
        $this->envProcessor = new EnvProcessor(FilePathFormatter::format(TESTS_BP) . '.env');
        $env = $this->envProcessor->getEnv();
        foreach ($env as $key => $value) {
            $this->addOption($key, null, InputOption::VALUE_REQUIRED, '', $value);
        }
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return integer
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $this->envProcessor->getEnv();
        $userEnv = [];
        foreach ($config as $key => $value) {
            if ($input->getOption($key) === '') {
                throw new InvalidOptionException(sprintf("Parameter $key cannot be empty.", $key));
            }
            $userEnv[$key] = $input->getOption($key);
        }
        $this->envProcessor->putEnvFile($userEnv);
        $output->writeln(".env configuration successfully applied.");

        return self::SUCCESS_EXIT_CODE;
    }
}
