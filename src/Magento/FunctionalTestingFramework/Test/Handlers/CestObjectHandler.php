<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Test\Handlers;

use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Test\Objects\CestObject;
use Magento\FunctionalTestingFramework\Test\Parsers\TestDataParser;
use Magento\FunctionalTestingFramework\Test\Util\CestObjectExtractor;

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
        if (!array_key_exists($cestName, $this->cests)) {
            trigger_error("Cest ${cestName} not defined in xml.", E_USER_ERROR);
            return null;
        }

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
     * Returns tests and cests tagged with the group name passed to the method.
     *
     * @param string $groupName
     * @return array
     */
    public function getCestsByGroup($groupName)
    {
        $relevantCests = [];
        foreach ($this->cests as $cest) {
            /** @var CestObject $cest */
            if (in_array($groupName, $cest->getAnnotationByName('group'))) {
                $relevantCests[$cest->getName()] = $cest;
                continue;
            }

            $relevantTests = [];
            // extract relevant tests here
            foreach ($cest->getTests() as $test) {
                if (in_array($groupName, $test->getAnnotationByName('group'))) {
                    $relevantTests[$test->getName()] = $test;
                    continue;
                }
            }

            if (!empty($relevantTests)) {
                $relevantCests[$cest->getName()] = new CestObject(
                    $cest->getName(),
                    $cest->getAnnotations(),
                    $relevantTests,
                    $cest->getHooks()
                );
            }
        }

        return $relevantCests;
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

        if (!$parsedCestArray) {
            trigger_error("Could not parse any data.xml.", E_USER_NOTICE);
            return;
        }

        foreach ($parsedCestArray[CestObjectHandler::XML_ROOT] as $cestName => $cestData) {
            if (!is_array($cestData)) {
                continue;
            }

            $this->cests[$cestName] = $cestObjectExtractor->extractCest($cestData);
        }
    }
}
