<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Suite;

use Magento\Framework\Phrase;
use Magento\Framework\Validator\Exception;
use Magento\FunctionalTestingFramework\Suite\Generators\GroupClassGenerator;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;
use Magento\FunctionalTestingFramework\Util\Manifest\BaseTestManifest;
use Magento\FunctionalTestingFramework\Util\Manifest\ParallelTestManifest;
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
    private static $SUITE_GENERATOR_INSTANCE;

    /**
     * Group Class Generator initialized in constructor.
     *
     * @var GroupClassGenerator
     */
    private $groupClassGenerator;

    /**
     * Multidimensional array which represents a custom suite configuration (e.g. certain tests run within a suite etc.)
     *
     * @var array
     */
    private $suiteReferences;

    /**
     * SuiteGenerator constructor.
     *
     * @param array $suiteReferences
     */
    private function __construct($suiteReferences)
    {
        $this->groupClassGenerator = new GroupClassGenerator();
        $this->suiteReferences = $suiteReferences;
    }

    /**
     * Singleton method which is used to retrieve the instance of the suite generator.
     *
     * @param array $suiteReferences
     * @return SuiteGenerator
     */
    public static function getInstance($suiteReferences = [])
    {
        if (!self::$SUITE_GENERATOR_INSTANCE) {
            // clear any previous configurations before any generation occurs.
            self::clearPreviousGroupPreconditions();
            self::clearPreviousSessionConfigEntries();
            self::$SUITE_GENERATOR_INSTANCE = new SuiteGenerator($suiteReferences);
        }

        return self::$SUITE_GENERATOR_INSTANCE;
    }

    /**
     * Function which takes all suite configurations and generates to appropriate directory, updating yml configuration
     * as needed. Returns an array of all tests generated keyed by test name.
     *
     * @param BaseTestManifest $testManifest
     * @return void
     */
    public function generateAllSuites($testManifest)
    {
        $suites = SuiteObjectHandler::getInstance()->getAllObjects();
        if (get_class($testManifest) == ParallelTestManifest::class) {
            /** @var  ParallelTestManifest $testManifest */
            $suites = $testManifest->getSorter()->getResultingSuites();
        } elseif (!empty($this->suiteReferences)) {
            $suites = array_intersect_key($suites, $this->suiteReferences);
        }

        foreach ($suites as $suite) {
            // during a parallel config run we must generate only after we have data around how a suite will be split
            $this->generateSuiteFromObject($suite);
        }
    }

    /**
     * Returns an array of tests contained within suites as keys pointed at the name of their corresponding suite.
     *
     * @return array
     */
    public function getTestsReferencedInSuites()
    {
        $testsReferencedInSuites = [];
        $suites = SuiteObjectHandler::getInstance()->getAllObjects();

        // see if we have a specific suite configuration.
        if (!empty($this->suiteReferences)) {
            $suites = array_intersect_key($suites, $this->suiteReferences);
        }

        foreach ($suites as $suite) {
            /** @var SuiteObject $suite */
            $test_keys = array_keys($suite->getTests());

            // see if we need to filter which tests we'll be generating.
            if (array_key_exists($suite->getName(), $this->suiteReferences)) {
                $test_keys = $this->suiteReferences[$suite->getName()] ?? $test_keys;
            }

            $testToSuiteName = array_fill_keys($test_keys, [$suite->getName()]);
            $testsReferencedInSuites = array_merge_recursive($testsReferencedInSuites, $testToSuiteName);
        }

        return $testsReferencedInSuites;
    }

    /**
     * Function which takes a suite name and generates corresponding dir, test files, group class, and updates
     * yml configuration for group run.
     *
     * @param string $suiteName
     * @return void
     */
    public function generateSuite($suiteName)
    {
        /**@var SuiteObject $suite **/
        $suite = SuiteObjectHandler::getInstance()->getObject($suiteName);
        $this->generateSuiteFromObject($suite);
    }

    /**
     * Function which takes a suite object and generates all relevant supporting files and classes.
     *
     * @param SuiteObject $suiteObject
     * @return void
     */
    public function generateSuiteFromObject($suiteObject)
    {
        $suiteName = $suiteObject->getName();
        $relativePath = TestGenerator::GENERATED_DIR . DIRECTORY_SEPARATOR . $suiteName;
        $fullPath = TESTS_MODULE_PATH . DIRECTORY_SEPARATOR . $relativePath;
        $groupNamespace = null;

        DirSetupUtil::createGroupDir($fullPath);

        $relevantTests = $suiteObject->getTests();
        if (array_key_exists($suiteName, $this->suiteReferences)) {
            $testReferences = $this->suiteReferences[$suiteName] ?? [];
            $tmpRelevantTests = null;
            array_walk($testReferences, function ($value) use (&$tmpRelevantTests, $relevantTests) {
                $tmpRelevantTests[$value] = $relevantTests[$value];
            });

            $relevantTests = $tmpRelevantTests ?? $relevantTests;
        }

        $this->generateRelevantGroupTests($suiteName, $relevantTests);

        if ($suiteObject->requiresGroupFile()) {
            // if the suite requires a group file, generate it and set the namespace
            $groupNamespace = $this->groupClassGenerator->generateGroupClass($suiteObject);
        }

        $this->appendEntriesToConfig($suiteName, $fullPath, $groupNamespace);
        print "Suite ${suiteName} generated to ${relativePath}.\n";
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
        $relativeSuitePath = substr($suitePath, strlen(dirname(dirname(TESTS_BP))) + 1);

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
     * @param array $tests
     * @return void
     */
    private function generateRelevantGroupTests($path, $tests)
    {
        $testGenerator = TestGenerator::getInstance($path, $tests);
        $testGenerator->createAllTestFiles('suite');
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
        return dirname(dirname(TESTS_BP)) . DIRECTORY_SEPARATOR;
    }
}
