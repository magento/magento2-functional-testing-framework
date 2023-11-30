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
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Suite\SuiteGenerator;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Util\Manifest\ParallelByTimeTestManifest;
use Magento\FunctionalTestingFramework\Util\Manifest\TestManifestFactory;
use Magento\FunctionalTestingFramework\Util\Script\ScriptUtil;
use Magento\FunctionalTestingFramework\Util\Script\TestDependencyUtil;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

/**
 * @SuppressWarnings(PHPMD)
 */
class GenerateTestsCommand extends BaseGenerateCommand
{
    const PARALLEL_DEFAULT_TIME = 10;
    const EXTENDS_REGEX_PATTERN = '/extends=["\']([^\'"]*)/';
    const ACTIONGROUP_REGEX_PATTERN = '/ref=["\']([^\'"]*)/';
    const TEST_DEPENDENCY_FILE_LOCATION_STANDALONE = 'dev/tests/_output/test-dependencies.json';
    const TEST_DEPENDENCY_FILE_LOCATION_EMBEDDED = 'dev/tests/acceptance/tests/_output/test-dependencies.json';

    /**
     * @var ScriptUtil
     */
    private $scriptUtil;

    /**
     * @var TestDependencyUtil
     */
    private $testDependencyUtil;

    /**
     * @var array
     */
    private $moduleNameToPath;

    /**
     * @var array
     */
    private $moduleNameToComposerName;

    /**
     * @var array
     */
    private $flattenedDependencies;

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
                'A parameter accepting a JSON string or JSON file path used to determine the test configuration'
            )->addOption(
                'filter',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Option to filter tests to be generated.' . PHP_EOL
                . '<info>Template:</info> <filterName>:<filterValue>' . PHP_EOL
                . '<info>Existing filter types:</info> severity.' . PHP_EOL
                . '<info>Existing severity values:</info> BLOCKER, CRITICAL, MAJOR, AVERAGE, MINOR.' . PHP_EOL
                . '<info>Example:</info> --filter=severity:CRITICAL'
                . ' --filter=includeGroup:customer --filter=excludeGroup:customerAnalytics' . PHP_EOL
            )->addOption(
                'path',
                'p',
                InputOption::VALUE_REQUIRED,
                'path to a test names file.',
            )->addOption(
                'log',
                'l',
                InputOption::VALUE_REQUIRED,
                'Generate metadata files during test generation.',
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
     * @throws XmlException
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
        $log = $input->getOption('log');
        $filters = $input->getOption('filter');
        foreach ($filters as $filter) {
            list($filterType, $filterValue) = explode(':', $filter);
            $filterList[$filterType][] = $filterValue;
        }

        $path = $input->getOption('path');
        // check filepath is given for generate test file
        if (!empty($path)) {
            $tests = $this->generateTestFileFromPath($path);
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

        if ($json !== null && is_file($json)) {
            $json = file_get_contents($json);
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

            SuiteGenerator::getInstance()->generateTestgroupmembership($testManifest);
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

        // check test dependencies log command
        if (!empty($log)) {
            if ($log === "testEntityJson") {
                $this->getTestEntityJson($filterList ??[], $tests);
                $testDependencyFileLocation = self::TEST_DEPENDENCY_FILE_LOCATION_EMBEDDED;
                if (isset($_ENV['MAGENTO_BP'])) {
                    $testDependencyFileLocation = self::TEST_DEPENDENCY_FILE_LOCATION_STANDALONE;
                }
                $output->writeln(
                    "Test dependencies file created, Located in: " . $testDependencyFileLocation
                );
            } else {
                $output->writeln(
                    "Wrong parameter for log (-l) option, accepted parameter are: testEntityJson" . PHP_EOL
                );
            }
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
                        && MftfApplicationConfig::getConfig()->getPhase() === MftfApplicationConfig::GENERATION_PHASE
                    ) {
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

    /**
     * console command options --log and create test dependencies in json file
     * @return void
     * @throws TestFrameworkException
     * @throws XmlException|FastFailException
     */
    private function getTestEntityJson(array $filterList, array $tests = [])
    {
        $testDependencies = $this->getTestDependencies($filterList, $tests);
        $this->array2Json($testDependencies);
    }

    /**
     * Function responsible for getting test dependencies in array
     * @param array $filterList
     * @param array $tests
     * @return array
     * @throws FastFailException
     * @throws TestFrameworkException
     * @throws XmlException
     */
    public function getTestDependencies(array $filterList, array $tests = []): array
    {
        $this->scriptUtil = new ScriptUtil();
        $this->testDependencyUtil = new TestDependencyUtil();
        $allModules = $this->scriptUtil->getAllModulePaths();

        if (!class_exists('\Magento\Framework\Component\ComponentRegistrar')) {
            throw new TestFrameworkException(
                "TEST DEPENDENCY CHECK ABORTED: MFTF must be attached or pointing to Magento codebase."
            );
        }
        $registrar = new \Magento\Framework\Component\ComponentRegistrar();
        $this->moduleNameToPath = $registrar->getPaths(\Magento\Framework\Component\ComponentRegistrar::MODULE);
        $this->moduleNameToComposerName = $this->testDependencyUtil->buildModuleNameToComposerName(
            $this->moduleNameToPath
        );
        $this->flattenedDependencies = $this->testDependencyUtil->buildComposerDependencyList(
            $this->moduleNameToPath,
            $this->moduleNameToComposerName
        );

        if (!empty($tests)) {
            # specific test dependencies will be generate.
            $testXmlFiles = $this->scriptUtil->getModuleXmlFilesByTestNames($tests);
        } else {
            $filePaths = [
                DIRECTORY_SEPARATOR . 'Test' . DIRECTORY_SEPARATOR
            ];
            // These files can contain references to other modules.
            $testXmlFiles = $this->scriptUtil->getModuleXmlFilesByScope($allModules, $filePaths[0]);
        }

        list($testDependencies, $extendedTestMapping) = $this->findTestDependentModule($testXmlFiles);
        return $this->testDependencyUtil->mergeDependenciesForExtendingTests(
            $testDependencies,
            $filterList,
            $extendedTestMapping
        );
    }

    /**
     * Finds all test dependencies in given set of files
     * @param Finder $files
     * @return array
     * @throws FastFailException
     * @throws XmlException
     */
    private function findTestDependentModule(Finder $files): array
    {
        $testDependencies = [];
        $extendedTests = [];
        $extendedTestMapping = [];
        foreach ($files as $filePath) {
            $allEntities = [];
            $filePath = $filePath->getPathname();
            $moduleName = $this->testDependencyUtil->getModuleName($filePath, $this->moduleNameToPath);
            // Not a module, is either dev/tests/acceptance or loose folder with test materials
            if ($moduleName == null) {
                continue;
            }

            $contents = file_get_contents($filePath);
            preg_match_all(ActionObject::ACTION_ATTRIBUTE_VARIABLE_REGEX_PATTERN, $contents, $braceReferences);
            preg_match_all(self::ACTIONGROUP_REGEX_PATTERN, $contents, $actionGroupReferences);
            preg_match_all(self::EXTENDS_REGEX_PATTERN, $contents, $extendReferences);

            // Remove Duplicates
            $braceReferences[0] = array_unique($braceReferences[0]);
            $actionGroupReferences[1] = array_unique($actionGroupReferences[1]);
            $braceReferences[1] = array_unique($braceReferences[1]);
            $braceReferences[2] = array_filter(array_unique($braceReferences[2]));

            // resolve entity references
            $allEntities = array_merge(
                $allEntities,
                $this->scriptUtil->resolveEntityReferences($braceReferences[0], $contents)
            );

            // resolve parameterized references
            $allEntities = array_merge(
                $allEntities,
                $this->scriptUtil->resolveParametrizedReferences($braceReferences[2], $contents)
            );

            // resolve entity by names
            $allEntities = array_merge(
                $allEntities,
                $this->scriptUtil->resolveEntityByNames($actionGroupReferences[1])
            );

            // resolve entity by names
            $allEntities = array_merge(
                $allEntities,
                $this->scriptUtil->resolveEntityByNames($extendReferences[1])
            );
            $modulesReferencedInTest = $this->testDependencyUtil->getModuleDependenciesFromReferences(
                $allEntities,
                $this->moduleNameToComposerName,
                $this->moduleNameToPath
            );
            if (! empty($modulesReferencedInTest)) {
                $document = new \DOMDocument();
                $document->loadXML($contents);
                $test_file = $document->getElementsByTagName('test')->item(0);
                $test_name = $test_file->getAttribute('name');

                # check any test extends on with this test.
                $extended_test = $test_file->getAttribute('extends') ?? "";
                if (!empty($extended_test)) {
                    $extendedTests[] = $extended_test;
                    $extendedTestMapping[] = ["child_test_name" =>$test_name, "parent_test_name" =>$extended_test];
                }

                $flattenedDependencyMap = array_values(
                    array_unique(call_user_func_array('array_merge', array_values($modulesReferencedInTest)))
                );
                $suite_name = $this->getSuiteName($test_name);
                $full_name = "Magento\AcceptanceTest\_". $suite_name. "\Backend\\".$test_name."Cest.".$test_name;
                $dependencyMap = [
                    "file_path" => $filePath,
                    "full_name" => $full_name,
                    "test_name" => $test_name,
                    "test_modules" => $flattenedDependencyMap,
                ];
                $testDependencies[] = $dependencyMap;
            }
        }

        if (!empty($extendedTests)) {
            list($extendedDependencies, $tempExtendedTestMapping) = $this->getExtendedTestDependencies($extendedTests);
            $testDependencies = array_merge($testDependencies, $extendedDependencies);
            $extendedTestMapping = array_merge($extendedTestMapping, $tempExtendedTestMapping);
        }

        return [$testDependencies, $extendedTestMapping];
    }

    /**
     * Finds all extended test dependencies in given set of files
     * @param array $extendedTests
     * @return array
     * @throws FastFailException
     * @throws XmlException
     */
    private function getExtendedTestDependencies(array $extendedTests): array
    {
        $testXmlFiles = $this->scriptUtil->getModuleXmlFilesByTestNames($extendedTests);
        return $this->findTestDependentModule($testXmlFiles);
    }

    /**
     * Create json file of test dependencies
     * @param array $array
     * @return void
     */
    private function array2Json(array $array)
    {
        $testDependencyFileLocation = self::TEST_DEPENDENCY_FILE_LOCATION_EMBEDDED;
        if (isset($_ENV['MAGENTO_BP'])) {
            $testDependencyFileLocation = self::TEST_DEPENDENCY_FILE_LOCATION_STANDALONE;
        }
        $testDependencyFileLocationDir = dirname($testDependencyFileLocation);
        if (!is_dir($testDependencyFileLocationDir)) {
            mkdir($testDependencyFileLocationDir, 0777, true);
        }
        $file = fopen($testDependencyFileLocation, 'w');
        $json = json_encode($array, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        fwrite($file, $json);
        fclose($file);
    }

    /**
     * Get suite name.
     * @param string $test_name
     * @return integer|mixed|string
     * @throws FastFailException
     */
    private function getSuiteName(string $test_name)
    {
        $suite_name = json_decode($this->getTestAndSuiteConfiguration([$test_name]), true)["suites"] ?? "default";
        if (is_array($suite_name)) {
            $suite_name = array_keys($suite_name)[0];
        }
        return $suite_name;
    }

    /**
     * @param string $path
     * @return array
     * @throws TestFrameworkException
     */
    private function generateTestFileFromPath(string $path): array
    {
        if (!file_exists($path)) {
            throw new TestFrameworkException("Could not find file $path. Check the path and try again.");
        }

        $test_names = file($path, FILE_IGNORE_NEW_LINES);
        $tests = [];
        foreach ($test_names as $test_name) {
            if (empty(trim($test_name))) {
                continue;
            }
            $test_name_array = explode(' ', trim($test_name));
            $tests = array_merge($tests, $test_name_array);
        }
        return $tests;
    }
}
