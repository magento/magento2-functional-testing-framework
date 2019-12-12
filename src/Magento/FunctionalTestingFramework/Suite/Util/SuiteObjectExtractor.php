<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Suite\Util;

use Exception;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Util\BaseObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\TestHookObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;
use Magento\FunctionalTestingFramework\Util\Validation\NameValidationUtil;

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
     * TestHookObjectExtractor initialized in constructor.
     *
     * @var TestHookObjectExtractor
     */
    private $testHookObjectExtractor;

    /**
     * SuiteObjectExtractor constructor
     */
    public function __construct()
    {
        $this->testHookObjectExtractor = new TestHookObjectExtractor();
    }

    /**
     * Takes an array of parsed xml and converts into an array of suite objects.
     *
     * @param array $parsedSuiteData
     * @return array
     * @throws XmlException
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function parseSuiteDataIntoObjects($parsedSuiteData)
    {
        $suiteObjects = [];

        // make sure there are suites defined before trying to parse as objects.
        if (!array_key_exists(self::SUITE_ROOT_TAG, $parsedSuiteData)) {
            return $suiteObjects;
        }

        foreach ($parsedSuiteData[self::SUITE_ROOT_TAG] as $parsedSuite) {
            if (!is_array($parsedSuite)) {
                // skip non array items parsed from suite (suite objects will always be arrays)
                continue;
            }

            $this->validateSuiteName($parsedSuite);

            //extract include and exclude references
            $groupTestsToInclude = $parsedSuite[self::INCLUDE_TAG_NAME] ?? [];
            $groupTestsToExclude = $parsedSuite[self::EXCLUDE_TAG_NAME] ?? [];

            //resolve references as test objects
            $includeTests = $this->extractTestObjectsFromSuiteRef($groupTestsToInclude);
            $excludeTests = $this->extractTestObjectsFromSuiteRef($groupTestsToExclude);

            // parse any object hooks
            $suiteHooks = $this->parseObjectHooks($parsedSuite);

            //throw an exception if suite is empty
            if ($this->isSuiteEmpty($suiteHooks, $includeTests, $excludeTests)) {
                throw new XmlException(sprintf(
                    "Suites must not be empty. Suite: \"%s\"",
                    $parsedSuite[self::NAME]
                ));
            };

            // add all test if include tests is completely empty
            if (empty($includeTests)) {
                $includeTests = TestObjectHandler::getInstance()->getAllObjects();
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
     * Throws exception for suite names meeting the below conditions:
     * 1. the name used is using special char or the "default" reserved name
     * 2. collisions between suite name and existing group name
     *
     * @param array $parsedSuite
     * @return void
     * @throws XmlException
     */
    private function validateSuiteName($parsedSuite)
    {
        //check if name used is using special char or the "default" reserved name
        NameValidationUtil::validateName($parsedSuite[self::NAME], 'Suite');
        if ($parsedSuite[self::NAME] == 'default') {
            throw new XmlException("A Suite can not have the name \"default\"");
        }

        $suiteName = $parsedSuite[self::NAME];
        //check for collisions between suite and existing group names
        $testGroupConflicts = TestObjectHandler::getInstance()->getTestsByGroup($suiteName);
        if (!empty($testGroupConflicts)) {
            $testGroupConflictsFileNames = "";
            foreach ($testGroupConflicts as $test) {
                $testGroupConflictsFileNames .= $test->getFilename() . "\n";
            }
            $exceptionmessage = "\"Suite names and Group names can not have the same value. \t\n" .
                "Suite: \"{$suiteName}\" also exists as a group annotation in: \n{$testGroupConflictsFileNames}";
            throw new XmlException($exceptionmessage);
        }
    }

    /**
     * Parse object hooks
     *
     * @param array $parsedSuite
     * @return array
     * @throws XmlException
     */
    private function parseObjectHooks($parsedSuite)
    {
        $suiteHooks = [];

        if (array_key_exists(TestObjectExtractor::TEST_BEFORE_HOOK, $parsedSuite)) {
            $suiteHooks[TestObjectExtractor::TEST_BEFORE_HOOK] = $this->testHookObjectExtractor->extractHook(
                $parsedSuite[self::NAME],
                TestObjectExtractor::TEST_BEFORE_HOOK,
                $parsedSuite[TestObjectExtractor::TEST_BEFORE_HOOK]
            );
        }
        if (array_key_exists(TestObjectExtractor::TEST_AFTER_HOOK, $parsedSuite)) {
            $suiteHooks[TestObjectExtractor::TEST_AFTER_HOOK] = $this->testHookObjectExtractor->extractHook(
                $parsedSuite[self::NAME],
                TestObjectExtractor::TEST_AFTER_HOOK,
                $parsedSuite[TestObjectExtractor::TEST_AFTER_HOOK]
            );
        }

        if (count($suiteHooks) == 1) {
            throw new XmlException(sprintf(
                "Suites that contain hooks must contain both a 'before' and an 'after' hook. Suite: \"%s\"",
                $parsedSuite[self::NAME]
            ));
        }
        return $suiteHooks;
    }

    /**
     * Check if suite hooks are empty/not included and there are no included tests/groups/modules
     *
     * @param array $suiteHooks
     * @param array $includeTests
     * @param array $excludeTests
     * @return boolean
     */
    private function isSuiteEmpty($suiteHooks, $includeTests, $excludeTests)
    {

        $noHooks = count($suiteHooks) == 0 ||
            (
                empty($suiteHooks['before']->getActions()) &&
                empty($suiteHooks['after']->getActions())
            );

        if ($noHooks && empty($includeTests) && empty($excludeTests)) {
            return true;
        }
        return false;
    }

    /**
     * Wrapper method for resolving suite reference data, checks type of suite reference and calls corresponding
     * resolver for each suite reference.
     *
     * @param array $suiteReferences
     * @return array
     * @throws \Exception
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
                        $suiteRefData[self::MODULE_TAG_FILE_ATTRIBUTE] ?? null
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
     * @throws \Exception
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
     * @param null   $moduleName
     * @return TestObject[]
     * @throws Exception
     */
    private function resolveFilePathTestNames($filename, $moduleName = null)
    {
        $filepath = $filename;
        if (!strstr($filepath, DIRECTORY_SEPARATOR)) {
            $filepath = FilePathFormatter::format(TESTS_MODULE_PATH) .
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
     * @throws \Exception
     */
    private function resolveModulePathTestNames($moduleName)
    {
        $testObjects = [];
        $xmlFiles = glob(
            FilePathFormatter::format(TESTS_MODULE_PATH) .
            $moduleName .
            DIRECTORY_SEPARATOR .
            'Test' .
            DIRECTORY_SEPARATOR .
            '*.xml'
        );

        foreach ($xmlFiles as $xmlFile) {
            $testObjs = $this->resolveFilePathTestNames($xmlFile);

            foreach ($testObjs as $testObj) {
                $testObjects[$testObj->getName()] = $testObj;
            }
        }

        return $testObjects;
    }
}
