<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Suite\Util;

use Exception;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Util\BaseObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\TestHookObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;

class SuiteObjectExtractor extends BaseObjectExtractor
{
    const SUITE_ROOT_TAG = 'suites';
    const SUITE_TAG_NAME = 'suite';
    const INCLUDE_TAG_NAME = 'include';
    const EXCLUDE_TAG_NAME = 'exclude';
    const MODULE_TAG_NAME = 'module';
    const MODULE_TAG_FILE_ATTRIBUTE = 'file';
    const TEST_TAG_NAME = 'test';
    const GROUP_TAG_NAME = 'group';

    /**
     * SuiteObjectExtractor constructor
     */
    public function __construct()
    {
        // empty constructor
    }

    /**
     * Takes an array of parsed xml and converts into an array of suite objects.
     *
     * @param array $parsedSuiteData
     * @return array
     */
    public function parseSuiteDataIntoObjects($parsedSuiteData)
    {
        $suiteObjects = [];
        $testHookObjectExtractor = new TestHookObjectExtractor();
        foreach ($parsedSuiteData[self::SUITE_ROOT_TAG] as $parsedSuite) {
            if (!is_array($parsedSuite)) {
                // skip non array items parsed from suite (suite objects will always be arrays)
                continue;
            }

            $suiteHooks = [];

            //extract include and exclude references
            $groupTestsToInclude = $parsedSuite[self::INCLUDE_TAG_NAME] ?? [];
            $groupTestsToExclude = $parsedSuite[self::EXCLUDE_TAG_NAME] ?? [];

            // resolve references as test objects
            $includeTests = $this->extractTestObjectsFromSuiteRef($groupTestsToInclude);
            $excludeTests = $this->extractTestObjectsFromSuiteRef($groupTestsToExclude);

            // add all test if include tests is completely empty
            if (empty($includeTests)) {
                $includeTests = TestObjectHandler::getInstance()->getAllObjects();
            }

            // parse any object hooks
            if (array_key_exists(TestObjectExtractor::TEST_BEFORE_HOOK, $parsedSuite)) {
                $suiteHooks[TestObjectExtractor::TEST_BEFORE_HOOK] = $testHookObjectExtractor->extractHook(
                    $parsedSuite[self::NAME],
                    TestObjectExtractor::TEST_BEFORE_HOOK,
                    $parsedSuite[TestObjectExtractor::TEST_BEFORE_HOOK]
                );
            }
            if (array_key_exists(TestObjectExtractor::TEST_AFTER_HOOK, $parsedSuite)) {
                $suiteHooks[TestObjectExtractor::TEST_AFTER_HOOK] = $testHookObjectExtractor->extractHook(
                    $parsedSuite[self::NAME],
                    TestObjectExtractor::TEST_AFTER_HOOK,
                    $parsedSuite[TestObjectExtractor::TEST_AFTER_HOOK]
                );
            }

            // create the new suite object
            $suiteObjects[$parsedSuite[self::NAME]] = new SuiteObject(
                $parsedSuite[self::NAME],
                $includeTests,
                $excludeTests,
                $suiteHooks
            );
        }

        return $suiteObjects;
    }

    /**
     * Wrapper method for resolving suite reference data, checks type of suite reference and calls corresponding
     * resolver for each suite reference.
     *
     * @param array $suiteReferences
     * @return array
     */
    private function extractTestObjectsFromSuiteRef($suiteReferences)
    {
        $testObjectList = [];
        foreach ($suiteReferences as $suiteRefName => $suiteRefData) {
            if (!is_array($suiteRefData)) {
                continue;
            }

            switch ($suiteRefData[self::NODE_NAME]) {
                case self::TEST_TAG_NAME:
                    $testObject = TestObjectHandler::getInstance()->getObject($suiteRefData[self::NAME]);
                    $testObjectList[$testObject->getName()] = $testObject;
                    break;
                case self::GROUP_TAG_NAME:
                    $testObjectList = $testObjectList +
                        TestObjectHandler::getInstance()->getTestsByGroup($suiteRefData[self::NAME]);
                    break;
                case self::MODULE_TAG_NAME:
                    $testObjectList = array_merge($testObjectList, $this->extractModuleAndFiles(
                        $suiteRefData[self::NAME],
                        $suiteRefData[self::MODULE_TAG_FILE_ATTRIBUTE ?? null]
                    ));
                    break;
            }
        }

        return $testObjectList;
    }

    /**
     * Takes an array of modules/files and resolves to an array of test objects.
     *
     * @param string $moduleName
     * @param string $moduleFilePath
     * @return array
     */
    private function extractModuleAndFiles($moduleName, $moduleFilePath)
    {
        if (empty($moduleFilePath)) {
            return $this->resolveModulePathTestNames($moduleName);
        }

        return $this->resolveFilePathTestNames($moduleFilePath, $moduleName);
    }

    /**
     * Takes a filepath (and optionally a module name) and resolves to a test object.
     *
     * @param string $filename
     * @param null $moduleName
     * @return TestObject[]
     * @throws Exception
     */
    private function resolveFilePathTestNames($filename, $moduleName = null)
    {
        $filepath = $filename;
        if (!strstr($filepath, DIRECTORY_SEPARATOR)) {
            $filepath = TESTS_MODULE_PATH .
                DIRECTORY_SEPARATOR .
                $moduleName .
                DIRECTORY_SEPARATOR .
                'Test' .
                DIRECTORY_SEPARATOR .
                $filename;
        }

        if (!file_exists($filepath)) {
            throw new Exception("Could not find file ${filename}");
        }

        $testObjects = [];
        $xml = simplexml_load_file($filepath);
        for ($i = 0; $i < $xml->count(); $i++) {
            $testName = (string)$xml->test[$i]->attributes()->name;
            $testObjects[$testName] = TestObjectHandler::getInstance()->getObject($testName);
        }

        return $testObjects;
    }

    /**
     * Takes a single module name and resolves to an array of tests contained within specified module.
     *
     * @param string $moduleName
     * @return array
     */
    private function resolveModulePathTestNames($moduleName)
    {
        $testObjects = [];
        $xmlFiles = glob(
            TESTS_MODULE_PATH .
            DIRECTORY_SEPARATOR .
            $moduleName .
            DIRECTORY_SEPARATOR .
            'Test' .
            DIRECTORY_SEPARATOR .
            '*.xml'
        );

        foreach ($xmlFiles as $xmlFile) {
            $testObj = $this->resolveFilePathTestNames($xmlFile);
            $testObjects[$testObj->getName()] = $testObj;
        }

        return $testObjects;
    }
}
