<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Suite;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Exceptions\FastFailException;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Suite\Generators\GroupClassGenerator;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Suite\Service\SuiteGeneratorService;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Util\Manifest\BaseTestManifest;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;

/**
 * Class SuiteGenerator
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SuiteGenerator
{
    const YAML_CODECEPTION_DIST_FILENAME = 'codeception.dist.yml';
    const YAML_CODECEPTION_CONFIG_FILENAME = 'codeception.yml';
    const YAML_GROUPS_TAG = 'groups';
    const YAML_EXTENSIONS_TAG = 'extensions';
    const YAML_ENABLED_TAG = 'enabled';
    const YAML_COPYRIGHT_TEXT =
        "# Copyright © Magento, Inc. All rights reserved.\n# See COPYING.txt for license details.\n";

    /**
     * Singelton Variable Instance.
     *
     * @var SuiteGenerator
     */
    private static $instance;

    /**
     * Group Class Generator initialized in constructor.
     *
     * @var GroupClassGenerator
     */
    private $groupClassGenerator;

    /**
     * Avoids instantiation of LoggingUtil by new.
     * @return void
     */
    private function __construct()
    {
        $this->groupClassGenerator = new GroupClassGenerator();
    }

    /**
     * Avoids instantiation of SuiteGenerator by clone.
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Singleton method which is used to retrieve the instance of the suite generator.
     *
     * @return SuiteGenerator
     */
    public static function getInstance(): SuiteGenerator
    {
        if (!self::$instance) {
            // clear any previous configurations before any generation occurs.
            self::clearPreviousGroupPreconditions();
            self::clearPreviousSessionConfigEntries();
            self::$instance = new SuiteGenerator();
        }

        return self::$instance;
    }

    /**
     * Function which takes all suite configurations and generates to appropriate directory, updating yml configuration
     * as needed. Returns an array of all tests generated keyed by test name.
     *
     * @param BaseTestManifest $testManifest
     * @return void
     * @throws FastFailException
     */
    public function generateAllSuites($testManifest)
    {
        $this->generateTestgroupmembership($testManifest);
        $suites = $testManifest->getSuiteConfig();

        foreach ($suites as $suiteName => $suiteContent) {
            try {
                if (empty($suiteContent)) {
                    LoggingUtil::getInstance()->getLogger(self::class)->notification(
                        "Suite '" . $suiteName . "' contains no tests and won't be generated.",
                        [],
                        true
                    );
                    continue;
                }
                $firstElement = array_values($suiteContent)[0];

                // if the first element is a string we know that we simply have an array of tests
                if (is_string($firstElement)) {
                    $this->generateSuiteFromTest($suiteName, $suiteContent);
                }

                // if our first element is an array we know that we have split the suites
                if (is_array($firstElement)) {
                    $this->generateSplitSuiteFromTest($suiteName, $suiteContent);
                }
            } catch (FastFailException $e) {
                throw $e;
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * Function which takes a suite name and generates corresponding dir, test files, group class, and updates
     * yml configuration for group run.
     *
     * @param string $suiteName
     * @return void
     * @throws \Exception
     */
    public function generateSuite($suiteName)
    {
        /**@var SuiteObject $suite **/
        $this->generateSuiteFromTest($suiteName, []);
    }

    /**
     * Function which generate Testgroupmembership file.
     *
     * @param object $testManifest
     * @return void
     * @throws \Exception
     */
    public function generateTestgroupmembership($testManifest): void
    {
        $suites = $this->getSuitesDetails($testManifest);

        // Path to groups folder
        $baseDir = FilePathFormatter::format(TESTS_MODULE_PATH);
        $path = $baseDir .'_generated/groups';

        $allGroupsContent = $this->readAllGroupFiles($path);

        // Output file path
        $memberShipFilePath = $baseDir.'_generated/testgroupmembership.txt';
        $testCaseNumber = 0;

        if (!empty($allGroupsContent)) {
            foreach ($allGroupsContent as $groupId => $groupInfo) {
                foreach ($groupInfo as $testName) {
                    // If file has -g then it is test suite
                    if (str_contains($testName, '-g')) {
                        $suitename = explode(" ", $testName);
                        $suitename[1] = trim($suitename[1]);

                        if (!empty($suites[$suitename[1]])) {
                            foreach ($suites[$suitename[1]] as $key => $test) {
                                $suiteTest = sprintf('%s:%s:%s:%s', $groupId, $key, $suitename[1], $test);
                                file_put_contents($memberShipFilePath, $suiteTest . PHP_EOL, FILE_APPEND);
                            }
                        }
                    } else {
                        $defaultSuiteTest = sprintf('%s:%s:%s', $groupId, $testCaseNumber, $testName);
                        file_put_contents($memberShipFilePath, $defaultSuiteTest, FILE_APPEND);
                    }
                    $testCaseNumber++;
                }
                $testCaseNumber = 0;
            }
        }
    }

    /**
     * Function to format suites details
     *
     * @param object $testManifest
     * @return array $suites
     */
    private function getSuitesDetails($testManifest): array
    {
        // Get suits and subsuites data array
        $suites = $testManifest->getSuiteConfig();

        // Add subsuites array[2nd dimension] to main array[1st dimension] to access it directly later
        if (!empty($suites)) {
            foreach ($suites as $subSuites) {
                if (!empty($subSuites)) {
                    foreach ($subSuites as $subSuiteName => $suiteTestNames) {
                        if (!is_numeric($subSuiteName)) {
                            $suites[$subSuiteName] = $suiteTestNames;
                        } else {
                            continue;
                        }
                    }
                }
            }
        }
        return $suites;
    }

  /**
   * Function to read all group* text files inside /groups folder
   *
   * @param object $path
   * @return array $allGroupsContent
   */
    private function readAllGroupFiles($path): array
    {
        // Read all group files
        if (is_dir($path)) {
            $groupFiles = glob("$path/group*.txt");
            if ($groupFiles === false) {
                throw new RuntimeException("glob(): error with '$path'");
            }
            sort($groupFiles, SORT_NATURAL);
        }

        // Read each file in the reverse order and form an array with groupId as key
        $groupNumber = 0;
        $allGroupsContent = [];
        while (!empty($groupFiles)) {
            $group = array_pop($groupFiles);
            $allGroupsContent[$groupNumber] = file($group);
            $groupNumber++;
        }
        return $allGroupsContent;
    }
    
    /**
     * Function which takes a suite name and a set of test names. The function then generates all relevant supporting
     * files and classes for the suite. The function takes an optional argument for suites which are split by a parallel
     * run so that any pre/post conditions can be duplicated.
     *
     * @param string $suiteName
     * @param array  $tests
     * @param string $originalSuiteName
     * @return void
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function generateSuiteFromTest($suiteName, $tests = [], $originalSuiteName = null)
    {
        $relativePath = TestGenerator::GENERATED_DIR . DIRECTORY_SEPARATOR . $suiteName;
        $fullPath = FilePathFormatter::format(TESTS_MODULE_PATH) . $relativePath . DIRECTORY_SEPARATOR;

        DirSetupUtil::createGroupDir($fullPath);
        $exceptionCollector = new ExceptionCollector();
        try {
            $relevantTests = [];
            if (!empty($tests)) {
                $this->validateTestsReferencedInSuite($suiteName, $tests, $originalSuiteName);
                foreach ($tests as $testName) {
                    try {
                        $relevantTests[$testName] = TestObjectHandler::getInstance()->getObject($testName);
                    } catch (FastFailException $e) {
                        throw $e;
                    } catch (\Exception $e) {
                        $exceptionCollector->addError(
                            self::class,
                            "Unable to find relevant test \"{$testName}\" for suite \"{$suiteName}\""
                        );
                    }
                }
            } else {
                $relevantTests = SuiteObjectHandler::getInstance()->getObject($suiteName)->getTests();
            }

            if (empty($relevantTests)) {
                $exceptionCollector->reset();
                // There are suites that include no test on purpose for certain Magento edition.
                // To keep backward compatibility, we will return with no error.
                // This might inevitably hide some suite errors that are resulted by real broken tests.
                if (file_exists($fullPath)) {
                    DirSetupUtil::rmdirRecursive($fullPath);
                }
                return;
            }

            try {
                $this->generateRelevantGroupTests($suiteName, $relevantTests);
            } catch (FastFailException $e) {
                throw $e;
            } catch (\Exception $e) {
                $exceptionCollector->addError(
                    self::class,
                    "Failed to generate tests for suite \"{$suiteName}\""
                );
            }

            $groupNamespace = $this->generateGroupFile($suiteName, $relevantTests, $originalSuiteName);

            $this->appendEntriesToConfig($suiteName, $fullPath, $groupNamespace);

            if (MftfApplicationConfig::getConfig()->verboseEnabled()
                && MftfApplicationConfig::getConfig()->getPhase() === MftfApplicationConfig::GENERATION_PHASE) {
                print("suite {$suiteName} generated\n");
            }
            LoggingUtil::getInstance()->getLogger(self::class)->info(
                "suite generated",
                ['suite' => $suiteName, 'relative_path' => $relativePath]
            );
        } catch (FastFailException $e) {
            throw $e;
        } catch (\Exception $e) {
            if (file_exists($fullPath)) {
                DirSetupUtil::rmdirRecursive($fullPath);
            }
            $exceptionCollector->addError(self::class, $e->getMessage());
            GenerationErrorHandler::getInstance()->addError('suite', $suiteName, self::class . ': ' . $e->getMessage());
        }

        $this->throwCollectedExceptions($exceptionCollector);
    }

    /**
     * Function which validates tests passed in as custom configuration against the configuration defined by the user to
     * prevent possible invalid test configurations from executing.
     *
     * @param string $suiteName
     * @param array  $testsReferenced
     * @param string $originalSuiteName
     * @return void
     * @throws TestReferenceException
     * @throws XmlException
     */
    private function validateTestsReferencedInSuite($suiteName, $testsReferenced, $originalSuiteName)
    {
        $suiteRef = $originalSuiteName ?? $suiteName;
        $possibleTestRef = SuiteObjectHandler::getInstance()->getObject($suiteRef)->getTests();
        $errorMsg = "Cannot reference tests which are not declared as part of suite";

        $invalidTestRef = array_diff($testsReferenced, array_keys($possibleTestRef));

        if (!empty($invalidTestRef)) {
            $testList = implode("\", \"", $invalidTestRef);
            $fullError = $errorMsg . " (Suite: \"{$suiteRef}\" Tests: \"{$testList}\")";
            throw new TestReferenceException($fullError, ['suite' => $suiteRef, 'test' => $invalidTestRef]);
        }
    }

    /**
     * Function for generating split groups of tests (following a parallel execution). Takes a paralle suite config
     * and generates applicable suites.
     *
     * @param string $suiteName
     * @param array  $suiteContent
     * @return void
     * @throws \Exception
     */
    private function generateSplitSuiteFromTest($suiteName, $suiteContent)
    {
        foreach ($suiteContent as $suiteSplitName => $tests) {
            try {
                $this->generateSuiteFromTest($suiteSplitName, $tests, $suiteName);
            } catch (FastFailException $e) {
                throw $e;
            } catch (\Exception $e) {
                // There are suites that include tests that reference tests from other Magento editions
                // To keep backward compatibility, we will catch such exceptions with no error.
                // This might inevitably hide some suite errors that are resulted by tests with broken references
                //TODO MQE-2484
            }
        }
    }

    /**
     * Function which takes a suite name, array of tests, and an original suite name. The function takes these args
     * and generates a group file which captures suite level preconditions.
     *
     * @param string $suiteName
     * @param array  $tests
     * @param string $originalSuiteName
     * @return null|string
     * @throws XmlException
     * @throws TestReferenceException
     */
    private function generateGroupFile($suiteName, $tests, $originalSuiteName)
    {
        // if there's an original suite name we know that this test came from a split group.
        if ($originalSuiteName) {
            // create the new suite object
            /** @var SuiteObject $originalSuite */
            $originalSuite = SuiteObjectHandler::getInstance()->getObject($originalSuiteName);
            $suiteObject = new SuiteObject(
                $suiteName,
                $tests,
                [],
                $originalSuite->getHooks()
            );
        } else {
            $suiteObject = SuiteObjectHandler::getInstance()->getObject($suiteName);
            // we have to handle the case when there is a custom configuration for an existing suite.
            if (count($suiteObject->getTests()) !== count($tests)) {
                return $this->generateGroupFile($suiteName, $tests, $suiteName);
            }
        }

        if (!$suiteObject->requiresGroupFile()) {
            // if we do not require a group file we don't need a namespace
            return null;
        }

        // if the suite requires a group file, generate it and set the namespace
        return $this->groupClassGenerator->generateGroupClass($suiteObject);
    }

    /**
     * Function which accepts a suite name and suite path and appends a new group entry to the codeception.yml.dist
     * file in order to register the set of tests as a new group. Also appends group object location if required
     * by suite.
     *
     * @param string $suiteName
     * @param string $suitePath
     * @param string $groupNamespace
     * @return void
     */
    private function appendEntriesToConfig($suiteName, $suitePath, $groupNamespace)
    {
        SuiteGeneratorService::getInstance()->appendEntriesToConfig($suiteName, $suitePath, $groupNamespace);
    }

    /**
     * Function which takes the current config.yml array and clears any previous configuration for suite group object
     * files.
     *
     * @return void
     */
    private static function clearPreviousSessionConfigEntries()
    {
        SuiteGeneratorService::getInstance()->clearPreviousSessionConfigEntries();
    }

    /**
     * Function which takes a string which is the desired output directory (under _generated) and an array of tests
     * relevant to the suite to be generated. The function takes this information and creates a new instance of the
     * test generator which is then called to create all the test files for the suite.
     *
     * @param string $path
     * @param array  $tests
     *
     * @return void
     * @throws TestReferenceException
     */
    private function generateRelevantGroupTests($path, $tests)
    {
        SuiteGeneratorService::getInstance()->generateRelevantGroupTests($path, $tests);
    }

    /**
     * Function which on first execution deletes all generate php in the MFTF Group directory
     *
     * @return void
     */
    private static function clearPreviousGroupPreconditions()
    {
        $groupFilePath = GroupClassGenerator::getGroupDirPath();
        array_map('unlink', glob("$groupFilePath*.php"));
    }

    /**
     * Log error and throw collected exceptions
     *
     * @param ExceptionCollector $exceptionCollector
     * @return void
     * @throws \Exception
     */
    private function throwCollectedExceptions($exceptionCollector)
    {
        if (!empty($exceptionCollector->getErrors())) {
            foreach ($exceptionCollector->getErrors() as $file => $errorMessage) {
                if (is_array($errorMessage)) {
                    foreach (array_unique($errorMessage) as $message) {
                        LoggingUtil::getInstance()->getLogger(self::class)->error(trim($message));
                    }
                } else {
                    LoggingUtil::getInstance()->getLogger(self::class)->error(trim($errorMessage));
                }
            }
            $exceptionCollector->throwException();
        }
    }
}
