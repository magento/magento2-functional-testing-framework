<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Suite\SuiteGenerator;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\Manifest\ParallelTestManifest;
use Magento\FunctionalTestingFramework\Util\Manifest\TestManifestFactory;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateTestsCommand extends BaseGenerateCommand
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('generate:tests')
            ->setDescription('This command generates all test files and suites based on xml declarations')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'name(s) of specific tests to generate'
            )->addOption("config", 'c', InputOption::VALUE_REQUIRED, 'default, singleRun, or parallel', 'default')
            ->addOption(
                "force",
                'f',
                InputOption::VALUE_NONE,
                'force generation of tests regardless of Magento Instance Configuration'
            )->addOption(
                'time',
                'i',
                InputOption::VALUE_REQUIRED,
                'Used in combination with a parallel configuration, determines desired group size (in minutes)',
                10
            )->addOption(
                'tests',
                't',
                InputOption::VALUE_REQUIRED,
                'A parameter accepting a JSON string used to determine the test configuration'
            )->addOption(
                'debug',
                'd',
                InputOption::VALUE_NONE,
                'run extra validation when generating tests'
            );

        parent::configure();
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     * @throws TestFrameworkException
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     * @throws \Magento\FunctionalTestingFramework\Exceptions\XmlException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tests = $input->getArgument('name');
        $config = $input->getOption('config');
        $json = $input->getOption('tests');
        $force = $input->getOption('force');
        $time = $input->getOption('time') * 60 * 1000; // convert from minutes to milliseconds
        $debug = $input->getOption('debug');
        $remove = $input->getOption('remove');

        $verbose = $output->isVerbose();

        if ($json !== null && !json_decode($json)) {
            // stop execution if we have failed to properly parse any json passed in by the user
            throw new TestFrameworkException("JSON could not be parsed: " . json_last_error_msg());
        }

        if ($config === 'parallel' && $time <= 0) {
            // stop execution if the user has given us an invalid argument for time argument during parallel generation
            throw new TestFrameworkException("time option cannot be less than or equal to 0");
        }

        // Remove previous GENERATED_DIR if --remove option is used
        if ($remove) {
            $this->removeGeneratedDirectory($output, $verbose || $debug);
        }

        $testConfiguration = $this->createTestConfiguration($json, $tests, $force, $debug, $verbose);

        // create our manifest file here
        $testManifest = TestManifestFactory::makeManifest($config, $testConfiguration['suites']);
        TestGenerator::getInstance(null, $testConfiguration['tests'])->createAllTestFiles($testManifest);

        if ($config == 'parallel') {
            /** @var ParallelTestManifest $testManifest */
            $testManifest->createTestGroups($time);
        }

        if (empty($tests)) {
            SuiteGenerator::getInstance()->generateAllSuites($testManifest);
        }

        $testManifest->generate();

        $output->writeln("Generate Tests Command Run");
    }

    /**
     * Function which builds up a configuration including test and suites for consumption of Magento generation methods.
     *
     * @param string  $json
     * @param array   $tests
     * @param boolean $force
     * @param boolean $debug
     * @param boolean $verbose
     * @return array
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     * @throws \Magento\FunctionalTestingFramework\Exceptions\XmlException
     */
    private function createTestConfiguration($json, array $tests, bool $force, bool $debug, bool $verbose)
    {
        // set our application configuration so we can references the user options in our framework
        MftfApplicationConfig::create(
            $force,
            MftfApplicationConfig::GENERATION_PHASE,
            $verbose,
            $debug
        );

        $testConfiguration = [];
        $testConfiguration['tests'] = $tests;
        $testConfiguration['suites'] = [];

        $testConfiguration = $this->parseTestsConfigJson($json, $testConfiguration);

        // if we have references to specific tests, we resolve the test objects and pass them to the config
        if (!empty($testConfiguration['tests'])) {
            $testObjects = [];

            foreach ($testConfiguration['tests'] as $test) {
                $testObjects[$test] = TestObjectHandler::getInstance()->getObject($test);
            }

            $testConfiguration['tests'] = $testObjects;
        }

        return $testConfiguration;
    }

    /**
     * Function which takes a json string of potential custom configuration and parses/validates the resulting json
     * passed in by the user. The result is a testConfiguration array.
     *
     * @param string $json
     * @param array  $testConfiguration
     * @return array
     */
    private function parseTestsConfigJson($json, array $testConfiguration)
    {
        if ($json === null) {
            return $testConfiguration;
        }

        $jsonTestConfiguration = [];
        $testConfigArray = json_decode($json, true);

        $jsonTestConfiguration['tests'] = $testConfigArray['tests'] ?? null;
        ;
        $jsonTestConfiguration['suites'] = $testConfigArray['suites'] ?? null;
        return $jsonTestConfiguration;
    }
}
