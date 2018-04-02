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
     * Boolean to track whether we have already cleared the yaml file.
     *
     * @var bool
     */
    private $ymlFileCleared = false;

    /**
     * Group Class Generator initialized in constructor.
     *
     * @var GroupClassGenerator
     */
    private $groupClassGenerator;

    /**
     * SuiteGenerator constructor.
     */
    private function __construct()
    {
        $this->groupClassGenerator = new GroupClassGenerator();
    }

    /**
     * Singleton method which is used to retrieve the instance of the suite generator.
     *
     * @return SuiteGenerator
     */
    public static function getInstance()
    {
        if (!self::$SUITE_GENERATOR_INSTANCE) {
            self::$SUITE_GENERATOR_INSTANCE = new SuiteGenerator();
        }

        return self::$SUITE_GENERATOR_INSTANCE;
    }

    /**
     * Function which takes all suite configurations and generates to appropriate directory, updating yml configuration
     * as needed. Returns an array of all tests generated keyed by test name.
     *
     * @param string $config
     * @return array
     */
    public function generateAllSuites($config)
    {
        $testsReferencedInSuites = [];
        $suites = SuiteObjectHandler::getInstance()->getAllObjects();
        foreach ($suites as $suite) {
            /** @var SuiteObject $suite */
            $testsReferencedInSuites = array_merge($testsReferencedInSuites, $suite->getTests());
            $this->generateSuite($suite->getName(), $config);
        }

        return $testsReferencedInSuites;
    }

    /**
     * Function which takes a suite name and generates corresponding dir, test files, group class, and updates
     * yml configuration for group run.
     *
     * @param string $suiteName
     * @param string $config
     * @return void
     */
    public function generateSuite($suiteName, $config = null)
    {
        /**@var SuiteObject $suite **/
        $suite = SuiteObjectHandler::getInstance()->getObject($suiteName);
        $relativePath = TestGenerator::GENERATED_DIR . DIRECTORY_SEPARATOR . $suiteName;
        $fullPath = TESTS_MODULE_PATH . DIRECTORY_SEPARATOR . $relativePath;
        $groupNamespace = null;

        DirSetupUtil::createGroupDir($fullPath);
        $this->generateRelevantGroupTests($suiteName, $suite->getTests(), $config);

        if ($suite->requiresGroupFile()) {
            // if the suite requires a group file, generate it and set the namespace
            $groupNamespace = $this->groupClassGenerator->generateGroupClass($suite);
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
        $configYmlPath = dirname(dirname(TESTS_BP)) . DIRECTORY_SEPARATOR;
        $configYmlFile = $configYmlPath . self::YAML_CODECEPTION_CONFIG_FILENAME;
        $defaultConfigYmlFile = $configYmlPath . self::YAML_CODECEPTION_DIST_FILENAME;
        $relativeSuitePath = substr($suitePath, strlen(dirname(dirname(TESTS_BP))) + 1);

        $ymlContents = null;
        if (file_exists($configYmlFile)) {
            $ymlContents = file_get_contents($configYmlFile);
        } else {
            $ymlContents = file_get_contents($defaultConfigYmlFile);
        }

        $ymlArray = Yaml::parse($ymlContents) ?? [];
        if (!array_key_exists(self::YAML_GROUPS_TAG, $ymlArray)) {
            $ymlArray[self::YAML_GROUPS_TAG]= [];
        }

        $ymlArray = $this->clearPreviousSessionConfigEntries($ymlArray);

        if ($groupNamespace) {
            $ymlArray[self::YAML_EXTENSIONS_TAG][self::YAML_ENABLED_TAG][] = $groupNamespace;
        }
        $ymlArray[self::YAML_GROUPS_TAG][$suiteName] = [$relativeSuitePath];

        $ymlText = self::YAML_COPYRIGHT_TEXT . Yaml::dump($ymlArray, 10);
        file_put_contents($configYmlFile, $ymlText);
    }

    /**
     * Function which takes the current config.yml array and clears any previous configuration for suite group object
     * files.
     *
     * @param array $ymlArray
     * @return array
     */
    private function clearPreviousSessionConfigEntries($ymlArray)
    {
        if ($this->ymlFileCleared) {
            return $ymlArray;
        }

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

        $this->ymlFileCleared = true;

        return $newYmlArray;
    }

    /**
     * Function which takes a string which is the desired output directory (under _generated) and an array of tests
     * relevant to the suite to be generated. The function takes this information and creates a new instance of the test
     * generator which is then called to create all the test files for the suite.
     *
     * @param string $path
     * @param array $tests
     * @param string $config
     * @return void
     */
    private function generateRelevantGroupTests($path, $tests, $config)
    {
        $testGenerator = TestGenerator::getInstance($path, $tests);
        $testGenerator->createAllTestFiles($config);
    }
}
