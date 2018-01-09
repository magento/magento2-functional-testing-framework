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
use Magento\FunctionalTestingFramework\Suite\Util\SuiteObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Util\TestHookObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\ObjectExtractor;
use Magento\Ui\Test\Unit\Component\PagingTest;

/**
 * Class SuiteObjectHandler
 */
class SuiteObjectHandler implements ObjectHandlerInterface
{
    /**
     * Singleton instance of suite object handler.
     *
     * @var SuiteObjectHandler
     */
    private static $SUITE_OBJECT_HANLDER_INSTANCE;

    /**
     * Array of suite objects keyed by suite name.
     *
     * @var SuiteObject[]
     */
    private $suiteObjects;

    /**
     * SuiteObjectHandler constructor.
     */
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

    /**
     * Method to parse all suite data xml into objects.
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initSuiteData()
    {
        $suiteDataParser = ObjectManagerFactory::getObjectManager()->create(SuiteDataParser::class);
        $suiteObjectExtractor = new SuiteObjectExtractor();
        $this->suiteObjects = $suiteObjectExtractor->parseSuiteDataIntoObjects($suiteDataParser->readSuiteData());
    }
}
