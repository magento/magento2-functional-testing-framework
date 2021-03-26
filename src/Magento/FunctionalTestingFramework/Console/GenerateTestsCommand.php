<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\FastFailException;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Suite\SuiteGenerator;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Util\Manifest\ParallelByTimeTestManifest;
use Magento\FunctionalTestingFramework\Util\Manifest\TestManifestFactory;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @SuppressWarnings(PHPMD)
 */
class GenerateTestsCommand extends BaseGenerateCommand
{
    const PARALLEL_DEFAULT_TIME = 10;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('generate:tests')
            ->setDescription('Run validation and generate all test files and suites based on xml declarations')
            ->addUsage('AdminLoginTest')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'name(s) of specific tests to generate'
            )->addOption(
                "config",
                'c',
                InputOption::VALUE_REQUIRED,
                'default, singleRun, or parallel',
                'default'
            )->addOption(
                'time',
                'i',
                InputOption::VALUE_REQUIRED,
                'Used in combination with a parallel configuration, determines desired group size (in minutes)'
                . PHP_EOL . 'Option "--time" will be the default and the default value is '
                . self::PARALLEL_DEFAULT_TIME
                . ' when neither "--time" nor "--groups" is specified'
            )->addOption(
                'groups',
                'g',
                InputOption::VALUE_REQUIRED,
                'Used in combination with a parallel configuration, determines desired number of groups'
                . PHP_EOL . 'Options "--time" and "--groups" are mutually exclusive and only one should be used'
            )->addOption(
                'tests',
                't',
                InputOption::VALUE_REQUIRED,
                'A parameter accepting a JSON string used to determine the test configuration'
            )->addOption(
                'filter',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Option to filter tests to be generated.' . PHP_EOL
                . '<info>Template:</info> <filterName>:<filterValue>' . PHP_EOL
                . '<info>Existing filter types:</info> severity.' . PHP_EOL
                . '<info>Existing severity values:</info> BLOCKER, CRITICAL, MAJOR, AVERAGE, MINOR.' . PHP_EOL
                . '<info>Example:</info> --filter=severity:CRITICAL' . PHP_EOL
            );

        parent::configure();
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void|integer
     * @throws TestFrameworkException
     * @throws FastFailException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setIOStyle($input, $output);
        $tests = $input->getArgument('name');
        $config = $input->getOption('config');
        $json = $input->getOption('tests'); // for backward compatibility
        $force = $input->getOption('force');
        $time = $input->getOption('time');
        //$time = $input->getOption('time') * 60 * 1000; // convert from minutes to milliseconds
        $groups = $input->getOption('groups');
        $debug = $input->getOption('debug') ?? MftfApplicationConfig::LEVEL_DEVELOPER; // for backward compatibility
        $remove = $input->getOption('remove');
        $verbose = $output->isVerbose();
        $allowSkipped = $input->getOption('allow-skipped');
        $filters = $input->getOption('filter');
        foreach ($filters as $filter) {
            list($filterType, $filterValue) = explode(':', $filter);
            $filterList[$filterType][] = $filterValue;
        }
        // Set application configuration so we can references the user options in our framework
        try {
            MftfApplicationConfig::create(
                $force,
                MftfApplicationConfig::GENERATION_PHASE,
                $verbose,
                $debug,
                $allowSkipped,
                $filterList ?? []
            );
        } catch (\Exception $exception) {
            $this->ioStyle->error("Test generation failed." . PHP_EOL . $exception->getMessage());
            return 1;
        }

        if (!empty($tests)) {
            $json = $this->getTestAndSuiteConfiguration($tests);
        }

        if ($json !== null && !json_decode($json)) {
            // stop execution if we have failed to properly parse any json passed in by the user
            throw new TestFrameworkException("JSON could not be parsed: " . json_last_error_msg());
        }

        if ($config === 'parallel') {
            list($config, $configNumber) = $this->parseConfigParallelOptions($time, $groups);
        }

        // Remove previous GENERATED_DIR if --remove option is used
        if ($remove) {
            $this->removeGeneratedDirectory($output, $verbose);
        }

        try {
            $testConfiguration = $this->createTestConfiguration($json, $tests);

            // create our manifest file here
            $testManifest = TestManifestFactory::makeManifest($config, $testConfiguration['suites']);

            try {
                if (empty($tests) || !empty($testConfiguration['tests'])) {
                    // $testConfiguration['tests'] cannot be empty if $tests is not empty
                    TestGenerator::getInstance(null, $testConfiguration['tests'])->createAllTestFiles($testManifest);
                } elseif (empty($testConfiguration['suites'])) {
                    throw new FastFailException(
                        !empty(GenerationErrorHandler::getInstance()->getAllErrors())
                            ?
                            GenerationErrorHandler::getInstance()->getAllErrorMessages()
                            :
                            'Invalid input'
                    );
                }
            } catch (FastFailException $e) {
                throw $e;
            } catch (\Exception $e) {
            }

            if (strpos($config, 'parallel') !== false) {
                $testManifest->createTestGroups($configNumber);
            }

            SuiteGenerator::getInstance()->generateAllSuites($testManifest);

            $testManifest->generate();
        } catch (\Exception $e) {
            if (!empty(GenerationErrorHandler::getInstance()->getAllErrors())) {
                GenerationErrorHandler::getInstance()->printErrorSummary();
            }
            $message = $e->getMessage() . PHP_EOL;
            $message .= !empty($filters) ? 'Filter(s): ' . implode(', ', $filters) . PHP_EOL : '';
            $message .= !empty($tests) ? 'Test name(s): ' . implode(', ', $tests) . PHP_EOL : '';
            $message .= !empty($json) && empty($tests) ? 'Test configuration: ' . $json . PHP_EOL : '';
            $this->ioStyle->note($message);

            return 1;
        }

        if (empty(GenerationErrorHandler::getInstance()->getAllErrors())) {
            $output->writeln("Generate Tests Command Run" . PHP_EOL);
            return 0;
        } else {
            GenerationErrorHandler::getInstance()->printErrorSummary();
            $output->writeln("Generate Tests Command Run (with errors)" . PHP_EOL);
            return 1;
        }
    }

    /**
     * Function which builds up a configuration including test and suites for consumption of Magento generation methods.
     *
     * @param string $json
     * @param array  $tests
     * @return array
     * @throws FastFailException
     * @throws TestFrameworkException
     */
    private function createTestConfiguration(
        $json,
        array $tests
    ) {
        $testConfiguration = [];
        $testConfiguration['tests'] = $tests;
        $testConfiguration['suites'] = [];

        $testConfiguration = $this->parseTestsConfigJson($json, $testConfiguration);

        // if we have references to specific tests, we resolve the test objects and pass them to the config
        if (!empty($testConfiguration['tests'])) {
            $testObjects = [];

            foreach ($testConfiguration['tests'] as $test) {
                try {
                    $testObjects[$test] = TestObjectHandler::getInstance()->getObject($test);
                } catch (FastFailException $e) {
                    throw $e;
                } catch (\Exception $e) {
                    $message = "Unable to create test object {$test} from test configuration. " . $e->getMessage();
                    LoggingUtil::getInstance()->getLogger(self::class)->error($message);
                    if (MftfApplicationConfig::getConfig()->verboseEnabled()
                        && MftfApplicationConfig::getConfig()->getPhase() == MftfApplicationConfig::GENERATION_PHASE) {
                        print($message);
                    }
                    GenerationErrorHandler::getInstance()->addError('test', $test, $message);
                }
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

    /**
     * Parse console command options --time and/or --groups and return config type and config number in an array
     *
     * @param mixed $time
     * @param mixed $groups
     * @return array
     * @throws FastFailException
     */
    private function parseConfigParallelOptions($time, $groups)
    {
        $config = null;
        $configNumber = null;
        if ($time !== null && $groups !== null) {
            throw new FastFailException(
                "'time' and 'groups' options are mutually exclusive. "
                . "Only one can be specified for 'config parallel'"
            );
        } elseif ($time === null && $groups === null) {
            $config = 'parallelByTime';
            $configNumber = self::PARALLEL_DEFAULT_TIME * 60 * 1000; // convert from minutes to milliseconds
        } elseif ($time !== null && is_numeric($time)) {
            $time = $time * 60 * 1000; // convert from minutes to milliseconds
            if (is_int($time) && $time > 0) {
                $config = 'parallelByTime';
                $configNumber = $time;
            }
        } elseif ($groups !== null && is_numeric($groups)) {
            $groups = $groups * 1;
            if (is_int($groups) && $groups > 0) {
                $config = 'parallelByGroup';
                $configNumber = $groups;
            }
        }

        if ($config && $configNumber) {
            return [$config, $configNumber];
        } elseif ($time !== null) {
            throw new FastFailException("'time' option must be an integer and greater than 0");
        } else {
            throw new FastFailException("'groups' option must be an integer and greater than 0");
        }
    }
}
