<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Magento\FunctionalTestingFramework\Util\Env\EnvProcessor;
use Symfony\Component\Yaml\Yaml;

/**
 * Class BuildProjectCommand
 * @package Magento\FunctionalTestingFramework\Console
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BuildProjectCommand extends Command
{
    const DEFAULT_YAML_INLINE_DEPTH = 10;
    const CREDENTIALS_FILE_PATH = TESTS_BP . DIRECTORY_SEPARATOR . '.credentials.example';

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
        $this->setName('build:project')
            ->setDescription('Generate configuration files for the project. Build the Codeception project.')
            ->addOption(
                "upgrade",
                'u',
                InputOption::VALUE_NONE,
                'upgrade existing MFTF tests according to last major release requiements'
            );
        $this->envProcessor = new EnvProcessor(TESTS_BP . DIRECTORY_SEPARATOR . '.env');
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
     * @return void
     * @throws \Symfony\Component\Console\Exception\LogicException
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resetCommand = new CleanProjectCommand();
        $resetOptions = new ArrayInput([]);
        $resetCommand->run($resetOptions, $output);

        $this->generateConfigFiles($output);

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

        // TODO can we just import the codecept symfony command?
        $codeceptBuildCommand = realpath(PROJECT_ROOT . '/vendor/bin/codecept') .  ' build';
        $process = new Process($codeceptBuildCommand);
        $process->setWorkingDirectory(TESTS_BP);
        $process->setIdleTimeout(600);
        $process->setTimeout(0);
        $process->run(
            function ($type, $buffer) use ($output) {
                if ($output->isVerbose()) {
                    $output->write($buffer);
                }
            }
        );

        if ($input->getOption('upgrade')) {
            $upgradeCommand = new UpgradeTestsCommand();
            $upgradeOptions = new ArrayInput(['path' => TESTS_MODULE_PATH]);
            $upgradeCommand->run($upgradeOptions, $output);
        }
    }

    /**
     * Generates needed codeception configuration files to the TEST_BP directory
     *
     * @param OutputInterface $output
     * @return void
     */
    private function generateConfigFiles(OutputInterface $output)
    {
        $fileSystem = new Filesystem();
        //Find travel path from codeception.yml to FW_BP
        $relativePath = $fileSystem->makePathRelative(FW_BP, TESTS_BP);

        if (!$fileSystem->exists(TESTS_BP . DIRECTORY_SEPARATOR . 'codeception.yml')) {
            // read in the codeception.yml file
            $configDistYml = Yaml::parse(file_get_contents(realpath(FW_BP . "/etc/config/codeception.dist.yml")));
            $configDistYml['paths']['support'] = $relativePath . 'src/Magento/FunctionalTestingFramework';
            $configDistYml['paths']['envs'] = $relativePath . 'etc/_envs';
            $configYmlText = Yaml::dump($configDistYml, self::DEFAULT_YAML_INLINE_DEPTH);

            // dump output to new codeception.yml file
            file_put_contents(TESTS_BP . DIRECTORY_SEPARATOR . 'codeception.yml', $configYmlText);
            $output->writeln("codeception.yml configuration successfully applied.");
        }

        if ($output->isVerbose()) {
            $output->writeln("codeception.yml applied to " . TESTS_BP . DIRECTORY_SEPARATOR . 'codeception.yml');
        }

        // copy the functional suite yml, will only copy if there are differences between the template the destination
        $fileSystem->copy(
            realpath(FW_BP . '/etc/config/functional.suite.dist.yml'),
            TESTS_BP . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'functional.suite.yml'
        );
        $output->writeln('functional.suite.yml configuration successfully applied.');

        if ($output->isVerbose()) {
            $output->writeln("functional.suite.yml applied to " .
                TESTS_BP . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'functional.suite.yml');
        }

        $fileSystem->copy(
            FW_BP . '/etc/config/.credentials.example',
            self::CREDENTIALS_FILE_PATH
        );

        // copy command.php into magento instance
        if (MAGENTO_BP === FW_BP) {
            $output->writeln('MFTF standalone detected, command.php copy not applied.');
        } else {
            $fileSystem->copy(
                realpath(FW_BP . '/etc/config/command.php'),
                TESTS_BP . DIRECTORY_SEPARATOR . "utils" . DIRECTORY_SEPARATOR .'command.php'
            );
            $output->writeln('command.php copied to ' .
                TESTS_BP . DIRECTORY_SEPARATOR . "utils" . DIRECTORY_SEPARATOR .'command.php');
        }

        // Remove and Create Log File
        $logPath = LoggingUtil::getInstance()->getLoggingPath();
        $fileSystem->remove($logPath);
        $fileSystem->touch($logPath);
        $fileSystem->chmod($logPath, 0777);

        $output->writeln('.credentials.example successfully applied.');
    }
}
