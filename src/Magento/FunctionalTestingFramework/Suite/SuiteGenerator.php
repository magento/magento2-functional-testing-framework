<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Suite;

use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Suite\Generators\GroupClassGenerator;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Util\Manifest\BaseTestManifest;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Symfony\Component\Yaml\Yaml;

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
     * @throws \Exception
     */
    public function generateAllSuites($testManifest)
    {
        $suites = $testManifest->getSuiteConfig();

        foreach ($suites as $suiteName => $suiteContent) {
            $firstElement = array_values($suiteContent)[0];

            // if the first element is a string we know that we simply have an array of tests
            if (is_string($firstElement)) {
                $this->generateSuiteFromTest($suiteName, $suiteContent);
            }

            // if our first element is an array we know that we have split the suites
            if (is_array($firstElement)) {
                $this->generateSplitSuiteFromTest($suiteName, $suiteContent);
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
     * Function which takes a suite name and a set of test names. The function then generates all relevant supporting
     * files and classes for the suite. The function takes an optional argument for suites which are split by a parallel
     * run so that any pre/post conditions can be duplicated.
     *
     * @param string $suiteName
     * @param array  $tests
     * @param string $originalSuiteName
     * @return void
     * @throws TestReferenceException
     * @throws XmlException
     */
    private function generateSuiteFromTest($suiteName, $tests = [], $originalSuiteName = null)
    {
        $relativePath = TestGenerator::GENERATED_DIR . DIRECTORY_SEPARATOR . $suiteName;
        $fullPath = TESTS_MODULE_PATH . DIRECTORY_SEPARATOR . $relativePath . DIRECTORY_SEPARATOR;

        DirSetupUtil::createGroupDir($fullPath);

        $relevantTests = [];
        if (!empty($tests)) {
            $this->validateTestsReferencedInSuite($suiteName, $tests, $originalSuiteName);
            foreach ($tests as $testName) {
                $relevantTests[$testName] = TestObjectHandler::getInstance()->getObject($testName);
            }
        } else {
            $relevantTests = SuiteObjectHandler::getInstance()->getObject($suiteName)->getTests();
        }

        $this->generateRelevantGroupTests($suiteName, $relevantTests);
        $groupNamespace = $this->generateGroupFile($suiteName, $relevantTests, $originalSuiteName);

        $this->appendEntriesToConfig($suiteName, $fullPath, $groupNamespace);
        LoggingUtil::getInstance()->getLogger(SuiteGenerator::class)->info(
            "suite generated",
            ['suite' => $suiteName, 'relative_path' => $relativePath]
        );
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
        $errorMsg = "Cannot reference tests whcih are not declared as part of suite.";

        $invalidTestRef = array_diff($testsReferenced, array_keys($possibleTestRef));

        if (!empty($invalidTestRef)) {
            throw new TestReferenceException($errorMsg, ['suite' => $suiteRef, 'test' => $invalidTestRef]);
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
            $this->generateSuiteFromTest($suiteSplitName, $tests, $suiteName);
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
            if (count($suiteObject->getTests()) != count($tests)) {
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
        $relativeSuitePath = substr($suitePath, strlen(TESTS_BP));
        $relativeSuitePath = ltrim($relativeSuitePath, DIRECTORY_SEPARATOR);

        $ymlArray = self::getYamlFileContents();
        if (!array_key_exists(self::YAML_GROUPS_TAG, $ymlArray)) {
            $ymlArray[self::YAML_GROUPS_TAG]= [];
        }

        if ($groupNamespace) {
            $ymlArray[self::YAML_EXTENSIONS_TAG][self::YAML_ENABLED_TAG][] = $groupNamespace;
        }
        $ymlArray[self::YAML_GROUPS_TAG][$suiteName] = [$relativeSuitePath];

        $ymlText = self::YAML_COPYRIGHT_TEXT . Yaml::dump($ymlArray, 10);
        file_put_contents(self::getYamlConfigFilePath() . self::YAML_CODECEPTION_CONFIG_FILENAME, $ymlText);
    }

    /**
     * Function which takes the current config.yml array and clears any previous configuration for suite group object
     * files.
     *
     * @return void
     */
    private static function clearPreviousSessionConfigEntries()
    {
        $ymlArray = self::getYamlFileContents();
        $newYmlArray = $ymlArray;
        // if the yaml entries haven't already been cleared
        if (array_key_exists(self::YAML_EXTENSIONS_TAG, $ymlArray)) {
            foreach ($ymlArray[self::YAML_EXTENSIONS_TAG][self::YAML_ENABLED_TAG] as $key => $entry) {
                if (preg_match('/(Group\\\\.*)/', $entry)) {
                    unset($newYmlArray[self::YAML_EXTENSIONS_TAG][self::YAML_ENABLED_TAG][$key]);
                }
            }

            // needed for proper yml file generation based on indices
            $newYmlArray[self::YAML_EXTENSIONS_TAG][self::YAML_ENABLED_TAG] =
                array_values($newYmlArray[self::YAML_EXTENSIONS_TAG][self::YAML_ENABLED_TAG]);
        }

        if (array_key_exists(self::YAML_GROUPS_TAG, $newYmlArray)) {
            unset($newYmlArray[self::YAML_GROUPS_TAG]);
        }

        $ymlText = self::YAML_COPYRIGHT_TEXT . Yaml::dump($newYmlArray, 10);
        file_put_contents(self::getYamlConfigFilePath() . self::YAML_CODECEPTION_CONFIG_FILENAME, $ymlText);
    }

    /**
     * Function which takes a string which is the desired output directory (under _generated) and an array of tests
     * relevant to the suite to be generated. The function takes this information and creates a new instance of the test
     * generator which is then called to create all the test files for the suite.
     *
     * @param string $path
     * @param array  $tests
     * @return void
     * @throws TestReferenceException
     */
    private function generateRelevantGroupTests($path, $tests)
    {
        $testGenerator = TestGenerator::getInstance($path, $tests);
        $testGenerator->createAllTestFiles(null, []);
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
     * Function to return contents of codeception.yml file for config changes.
     *
     * @return array
     */
    private static function getYamlFileContents()
    {
        $configYmlFile = self::getYamlConfigFilePath() . self::YAML_CODECEPTION_CONFIG_FILENAME;
        $defaultConfigYmlFile = self::getYamlConfigFilePath() . self::YAML_CODECEPTION_DIST_FILENAME;

        $ymlContents = null;
        if (file_exists($configYmlFile)) {
            $ymlContents = file_get_contents($configYmlFile);
        } else {
            $ymlContents = file_get_contents($defaultConfigYmlFile);
        }

        return Yaml::parse($ymlContents) ?? [];
    }

    /**
     * Static getter for the Config yml filepath (as path cannot be stored in a const)
     *
     * @return string
     */
    private static function getYamlConfigFilePath()
    {
        return TESTS_BP . DIRECTORY_SEPARATOR;
    }
}
