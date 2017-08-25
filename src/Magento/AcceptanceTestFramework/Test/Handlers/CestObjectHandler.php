<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AcceptanceTestFramework\Test\Handlers;

use Magento\AcceptanceTestFramework\ObjectManager\ObjectHandlerInterface;
use Magento\AcceptanceTestFramework\ObjectManagerFactory;
use Magento\AcceptanceTestFramework\Test\Objects\CestObject;
use Magento\AcceptanceTestFramework\Test\Parsers\TestDataParser;
use Magento\AcceptanceTestFramework\Test\Util\CestObjectExtractor;

/**
 * Class CestObjectHandler
 */
class CestObjectHandler implements ObjectHandlerInterface
{
    const XML_ROOT = 'config';

    /**
     * Cest Object Handler
     *
     * @var CestObjectHandler
     */
    private static $cestObjectHandler;

    /**
     * Array contains all cest objects indexed by name
     *
     * @var array $cests
     */
    private $cests = [];

    /**
     * Singleton method to return CestObjectHandler.
     *
     * @return CestObjectHandler
     */
    public static function getInstance()
    {
        if (!self::$cestObjectHandler) {
            self::$cestObjectHandler = new CestObjectHandler();
            self::$cestObjectHandler->initCestData();
        }

        return self::$cestObjectHandler;
    }

    /**
     * CestObjectHandler constructor.
     */
    private function __construct()
    {
        // private constructor
    }

    /**
     * Takes a cest name and returns the corresponding cest.
     *
     * @param string $cestName
     * @return CestObject
     */
    public function getObject($cestName)
    {
        return $this->cests[$cestName];
    }

    /**
     * Returns all cests parsed from xml indexed by cestName.
     *
     * @return array
     */
    public function getAllObjects()
    {
        return $this->cests;
    }

    /**
     * This method reads all Cest.xml files into objects and stores them in an array for future access.
     *
     * @return void
     */
    private function initCestData()
    {
        $testDataParser = ObjectManagerFactory::getObjectManager()->create(TestDataParser::class);
        $parsedCestArray = $testDataParser->readTestData();

        $cestObjectExtractor = new CestObjectExtractor();

        foreach ($parsedCestArray[CestObjectHandler::XML_ROOT] as $cestName => $cestData) {
            if (!is_array($cestData)) {
                continue;
            }

            $this->cests[$cestName] = $cestObjectExtractor->extractCest($cestData);
        }
    }
}
