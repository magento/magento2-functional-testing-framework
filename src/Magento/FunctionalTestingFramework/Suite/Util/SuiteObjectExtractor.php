<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Suite\Util;

use Exception;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Test\Handlers\CestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\CestObject;
use Magento\FunctionalTestingFramework\Test\Util\BaseCestObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\CestHookObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\CestObjectExtractor;

class SuiteObjectExtractor extends BaseCestObjectExtractor
{
    const SUITE_ROOT_TAG = 'suites';
    const SUITE_TAG_NAME = 'suite';
    const INCLUDE_TAG_NAME = 'include';
    const EXCLUDE_TAG_NAME = 'exclude';
    const MODULE_TAG_NAME = 'module';
    const MODULE_TAG_FILE_ATTRIBUTE = 'file';
    const CEST_TAG_NAME = 'cest';
    const CEST_TAG_NAME_ATTRIBUTE = 'name';
    const CEST_TAG_TEST_ATTRIBUTE = 'test';
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
        $cestObjectHookExtractor = new CestHookObjectExtractor();
        foreach ($parsedSuiteData[self::SUITE_ROOT_TAG] as $parsedSuite) {
            if (!is_array($parsedSuite)) {
                // skip non array items parsed from suite (suite objects will always be arrays)
                continue;
            }

            $suiteHooks = [];

            //extract include and exclude references
            $groupCestsToInclude = $parsedSuite[self::INCLUDE_TAG_NAME] ?? [];
            $groupCestsToExclude = $parsedSuite[self::EXCLUDE_TAG_NAME] ?? [];

            // resolve references as cest objects
            $includeCests = $this->extractCestObjectsFromSuiteRef($groupCestsToInclude);
            $excludeCests = $this->extractCestObjectsFromSuiteRef($groupCestsToExclude);

            // add all cests if include cests is completely empty
            if (empty($includeCests)) {
                $includeCests = CestObjectHandler::getInstance()->getAllObjects();
            }

            // parse any object hooks
            if (array_key_exists(CestObjectExtractor::CEST_BEFORE_HOOK, $parsedSuite)) {
                $suiteHooks[CestObjectExtractor::CEST_BEFORE_HOOK] = $cestObjectHookExtractor->extractHook(
                    $parsedSuite[self::NAME],
                    CestObjectExtractor::CEST_BEFORE_HOOK,
                    $parsedSuite[CestObjectExtractor::CEST_BEFORE_HOOK]
                );
            }
            if (array_key_exists(CestObjectExtractor::CEST_AFTER_HOOK, $parsedSuite)) {
                $suiteHooks[CestObjectExtractor::CEST_AFTER_HOOK] = $cestObjectHookExtractor->extractHook(
                    $parsedSuite[self::NAME],
                    CestObjectExtractor::CEST_AFTER_HOOK,
                    $parsedSuite[CestObjectExtractor::CEST_AFTER_HOOK]
                );
            }

            // create the new suite object
            $suiteObjects[$parsedSuite[self::NAME]] = new SuiteObject(
                $parsedSuite[self::NAME],
                $includeCests,
                $excludeCests,
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
    private function extractCestObjectsFromSuiteRef($suiteReferences)
    {
        $cestObjectList = [];
        foreach ($suiteReferences as $suiteRefName => $suiteRefData) {
            if (!is_array($suiteRefData)) {
                continue;
            }

            switch ($suiteRefData[self::NODE_NAME]) {
                case self::CEST_TAG_NAME:
                    $extractedCest = $this->extractRelevantCestObject($suiteRefData);
                    $cestObjectList[$extractedCest->getName()] = $extractedCest;
                    break;
                case self::GROUP_TAG_NAME:
                    $cestObjectList = $cestObjectList +
                        CestObjectHandler::getInstance()->getCestsByGroup($suiteRefData[self::NAME]);
                    break;
                case self::MODULE_TAG_NAME:
                    $cestObjectList = array_merge($cestObjectList, $this->extractModuleAndFiles(
                        $suiteRefData[self::NAME],
                        $suiteRefData[self::MODULE_TAG_FILE_ATTRIBUTE ?? null]
                    ));
                    break;
            }
        }

        return $cestObjectList;
    }

    /**
     * Takes an array of Cests/Tests and resolves names into corresponding Cest/Test objects.
     *
     * @param array $suiteTestData
     * @return CestObject|null
     */
    private function extractRelevantCestObject($suiteTestData)
    {
        $relevantCest = CestObjectHandler::getInstance()->getObject($suiteTestData[self::CEST_TAG_NAME_ATTRIBUTE]);

        if (array_key_exists(self::CEST_TAG_TEST_ATTRIBUTE, $suiteTestData)) {
            $relevantTest = $relevantCest->getTests()[$suiteTestData[self::CEST_TAG_TEST_ATTRIBUTE]] ?? null;

            if (!$relevantTest) {
                trigger_error(
                    "Test " .
                    $suiteTestData[self::CEST_TAG_NAME_ATTRIBUTE] .
                    " does not exist.",
                    E_USER_NOTICE
                );
                return null;
            }

            return new CestObject(
                $relevantCest->getName(),
                $relevantCest->getAnnotations(),
                [$relevantTest->getName() => $relevantTest],
                $relevantCest->getHooks()
            );
        }

        return $relevantCest;
    }

    /**
     * Takes an array of modules/files and resolves to an array of cest objects.
     *
     * @param string $moduleName
     * @param string $moduleFilePath
     * @return array
     */
    private function extractModuleAndFiles($moduleName, $moduleFilePath)
    {
        if (empty($moduleFilePath)) {
            return $this->resolveModulePathCestNames($moduleName);
        }

        $cestObj = $this->resolveFilePathCestName($moduleFilePath, $moduleName);
        return [$cestObj->getName() => $cestObj];
    }

    /**
     * Takes a filepath (and optionally a module name) and resolves to a cest object.
     *
     * @param string $filename
     * @param null $moduleName
     * @return CestObject
     * @throws Exception
     */
    private function resolveFilePathCestName($filename, $moduleName = null)
    {
        $filepath = $filename;
        if (!strstr($filepath, DIRECTORY_SEPARATOR)) {
            $filepath = TESTS_MODULE_PATH .
                DIRECTORY_SEPARATOR .
                $moduleName .
                DIRECTORY_SEPARATOR .
                'Cest' .
                DIRECTORY_SEPARATOR .
                $filename;
        }

        if (!file_exists($filepath)) {
            throw new Exception("Could not find file ${filename}");
        }
        $xml = simplexml_load_file($filepath);
        $cestName = (string)$xml->cest->attributes()->name;

        return CestObjectHandler::getInstance()->getObject($cestName);
    }

    /**
     * Takes a single module name and resolves to an array of cests contained within specified module.
     *
     * @param string $moduleName
     * @return array
     */
    private function resolveModulePathCestNames($moduleName)
    {
        $cestObjects = [];
        $xmlFiles = glob(
            TESTS_MODULE_PATH .
            DIRECTORY_SEPARATOR .
            $moduleName .
            DIRECTORY_SEPARATOR .
            'Cest' .
            DIRECTORY_SEPARATOR .
            '*.xml'
        );

        foreach ($xmlFiles as $xmlFile) {
            $cestObj = $this->resolveFilePathCestName($xmlFile);
            $cestObjects[$cestObj->getName()] = $cestObj;
        }

        return $cestObjects;
    }
}
