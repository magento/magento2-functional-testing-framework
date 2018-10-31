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
            )->addOption(
                "force",
                'f',
                InputOption::VALUE_NONE,
                'force generation of tests regardless of Magento Instance Configuration'
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
     * @return integer|null|void
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $skipGeneration = $input->getOption('skip-generate');
        $force = $input->getOption('force');
        $groups = $input->getArgument('groups');
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
            $testConfiguration = $this->getGroupAndSuiteConfiguration($groups);
            $command = $this->getApplication()->find('generate:tests');
            $args = [
                '--tests' => $testConfiguration,
                '--force' => $force,
                '--remove' => $remove
            ];

            $command->run(new ArrayInput($args), $output);
        }

        $codeceptionCommand = realpath(PROJECT_ROOT . '/vendor/bin/codecept') . ' run functional --verbose --steps';

        foreach ($groups as $group) {
            $codeceptionCommand .= " -g {$group}";
        }

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
     * Returns a json string to be used as an argument for generation of a group or suite
     *
     * @param array $groups
     * @return string
     * @throws \Magento\FunctionalTestingFramework\Exceptions\XmlException
     */
    private function getGroupAndSuiteConfiguration(array $groups)
    {
        $testConfiguration['tests'] = [];
        $testConfiguration['suites'] = null;
        $availableSuites = SuiteObjectHandler::getInstance()->getAllObjects();

        foreach ($groups as $group) {
            if (array_key_exists($group, $availableSuites)) {
                $testConfiguration['suites'][$group] = [];
            }

            $testConfiguration['tests'] = array_merge(
                $testConfiguration['tests'],
                array_keys(TestObjectHandler::getInstance()->getTestsByGroup($group))
            );
        }

        $testConfigurationJson = json_encode($testConfiguration);
        return $testConfigurationJson;
    }
}
