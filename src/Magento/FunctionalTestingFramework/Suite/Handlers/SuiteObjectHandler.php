<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Suite\Handlers;

use Exception;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Suite\Parsers\SuiteDataParser;
use Magento\FunctionalTestingFramework\Test\Handlers\CestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\CestObject;
use Magento\Ui\Test\Unit\Component\PagingTest;

/**
 * Class SuiteObjectHandler
 */
class SuiteObjectHandler implements ObjectHandlerInterface
{
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
     * Singleton instance of suite object handler.
     *
     * @var SuiteObjectHandler
     */
    private static $SUITE_OBJECT_HANLDER_INSTANCE;

    /**
     * Array of suite objects keyed by suite name.
     *
     * @var array
     */
    private $suiteObjects;

    private function __construct()
    {
        // empty constructor
    }

    /**
     * Function to enforce singleton design pattern
     *
     * @return ObjectHandlerInterface
     */
    public static function getInstance()
    {
        if (self::$SUITE_OBJECT_HANLDER_INSTANCE == null) {
            self::$SUITE_OBJECT_HANLDER_INSTANCE = new SuiteObjectHandler();
            self::$SUITE_OBJECT_HANLDER_INSTANCE->initSuiteData();
        }

        return self::$SUITE_OBJECT_HANLDER_INSTANCE;
    }

    /**
     * Function to return a single suite object by name
     *
     * @param string $objectName
     * @return SuiteObject
     */
    public function getObject($objectName)
    {
        if (!array_key_exists($objectName, $this->suiteObjects)) {
            trigger_error("Suite ${objectName} is not defined.", E_USER_ERROR);
        }
        return $this->suiteObjects[$objectName];
    }

    /**
     * Function to return all objects the handler is responsible for
     *
     * @return array
     */
    public function getAllObjects()
    {
        return $this->suiteObjects;
    }

    private function initSuiteData()
    {
        $suiteDataParser = ObjectManagerFactory::getObjectManager()->create(SuiteDataParser::class);
        $this->suiteObjects = $this->parseSuiteDataIntoObjects($suiteDataParser->readSuiteData());
    }

    private function parseSuiteDataIntoObjects($parsedSuiteData)
    {
        $suiteObjects = [];
        foreach ($parsedSuiteData[self::SUITE_TAG_NAME] as $parsedSuiteName => $parsedSuite) {
            $includeCests = [];
            $excludeCests = [];

            $groupCestsToInclude = $parsedSuite[self::INCLUDE_TAG_NAME][0] ?? [];
            $groupCestsToExclude = $parsedSuite[self::EXCLUDE_TAG_NAME][0] ?? [];

            if (array_key_exists(self::CEST_TAG_NAME, $groupCestsToInclude)) {
                $includeCests = $includeCests + $this->extractRelevantTests($groupCestsToInclude[self::CEST_TAG_NAME]);
            }

            if (array_key_exists(self::CEST_TAG_NAME, $groupCestsToExclude)) {
                $excludeCests = $excludeCests + $this->extractRelevantTests($groupCestsToExclude[self::CEST_TAG_NAME]);
            }

            $includeCests = $includeCests + $this->extractGroups($groupCestsToInclude);
            $excludeCests = $excludeCests + $this->extractGroups($groupCestsToExclude);


            // get tests by path (dir or file)
            if (array_key_exists(self::MODULE_TAG_NAME, $groupCestsToInclude)) {
                $includeCests = array_merge(
                    $includeCests,
                    $this->extractModuleAndFiles($groupCestsToInclude[self::MODULE_TAG_NAME])
                );
            }

            if (array_key_exists(self::MODULE_TAG_NAME, $groupCestsToExclude)) {
                $excludeCests = array_merge(
                    $excludeCests,
                    $this->extractModuleAndFiles($groupCestsToExclude[self::MODULE_TAG_NAME])
                );
            }

            // add all cests if include cests is completely empty
            if (empty($includeCests)) {
                $includeCests = CestObjectHandler::getInstance()->getAllObjects();
            }

            $suiteObjects[$parsedSuiteName] = new SuiteObject($parsedSuiteName, $includeCests, $excludeCests);
        }

        return $suiteObjects;
    }

    private function extractRelevantTests($suiteTestData)
    {
        $relevantCests = [];
        foreach ($suiteTestData as $cestName => $cestInfo) {
            $relevantCest = CestObjectHandler::getInstance()->getObject($cestName);

            if (array_key_exists(self::CEST_TAG_TEST_ATTRIBUTE, $cestInfo)) {
                $relevantTest = $relevantCest->getTests()[$cestInfo[self::CEST_TAG_TEST_ATTRIBUTE]] ?? null;

                if (!$relevantTest) {
                    trigger_error(
                        "Test " .
                        $cestInfo[self::CEST_TAG_NAME_ATTRIBUTE] .
                        " does not exist.",
                        E_USER_NOTICE
                    );
                    continue;
                }

                $relevantCests[$cestName] = new CestObject(
                    $relevantCest->getName(),
                    $relevantCest->getAnnotations(),
                    [$relevantTest->getName() => $relevantTest],
                    $relevantCest->getHooks()
                );
            } else {
                $relevantCests[$cestName] = $relevantCest;
            }
        }

        return $relevantCests;
    }

    private function extractGroups($suiteData)
    {
        $cestsByGroup = [];
        // get tests by group
        if (array_key_exists(self::GROUP_TAG_NAME, $suiteData)) {
            //loop groups and add to the groupCests
            foreach ($suiteData[self::GROUP_TAG_NAME] as $groupName => $groupVal) {
                $cestsByGroup = $cestsByGroup + CestObjectHandler::getInstance()->getCestsByGroup($groupName);
            }
        }

        return $cestsByGroup;
    }

    private function extractModuleAndFiles($suitePathData)
    {
        $cestsByModule = [];
        foreach ($suitePathData as $moduleName => $fileInfo) {
            if (empty($fileInfo)) {
                $cestsByModule = array_merge($cestsByModule, $this->resolveModulePathCestNames($moduleName));
            } else {
                $cestObj = $this->resolveFilePathCestName($fileInfo[self::MODULE_TAG_FILE_ATTRIBUTE], $moduleName);
                $cestsByModule[$cestObj->getName()] = $cestObj;
            }
        }

        return $cestsByModule;
    }

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