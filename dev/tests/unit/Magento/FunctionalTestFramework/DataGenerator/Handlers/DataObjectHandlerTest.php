<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\DataGenerator\Handlers;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\DataProfileSchemaParser;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;

/**
 * Class DataObjectHandlerTest
 */
class DataObjectHandlerTest extends MagentoTestCase
{
    // All tests share this array, feel free to add but be careful modifying or removing
    const PARSER_OUTPUT = [
        'entity' => [
            'EntityOne' => [
                'type' => 'testType',
                'data' => [
                    0 => [
                        'key' => 'testKey',
                        'value' => 'testValue'
                    ]
                ]
            ]
        ]
    ];

    /**
     * Set up everything required to mock DataObjectHander::getInstance()
     * The first call to getInstance() uses these mocks to emulate the parser, initializing internal state
     * according to the PARSER_OUTPUT value
     */
    public static function setUpBeforeClass()
    {
        $mockDataProfileSchemaParser = AspectMock::double(DataProfileSchemaParser::class, [
            'readDataProfiles' => self::PARSER_OUTPUT
        ])->make();

        $mockObjectManager = AspectMock::double(ObjectManager::class, [
            'create' => $mockDataProfileSchemaParser
        ])->make();

        AspectMock::double(ObjectManagerFactory::class, [
            'getObjectManager' => $mockObjectManager
        ]);
    }

    /**
     * getAllObjects should contain the expected data object
     */
    public function testGetAllObjects()
    {
        // Call the method under test

        $actual = DataObjectHandler::getInstance()->getAllObjects();

        // Assert

        $expected = new EntityDataObject('EntityOne', 'testType', ['testkey' => 'testValue'], [], null, []);
        $this->assertArrayHasKey('EntityOne', $actual);
        $this->assertEquals($expected, $actual['EntityOne']);
    }

    /**
     * getObject should return the expected data object if it exists
     */
    public function testGetObject()
    {
        // Call the method under test

        $actual = DataObjectHandler::getInstance()->getObject('EntityOne');

        // Assert

        $expected = new EntityDataObject('EntityOne', 'testType', ['testkey' => 'testValue'], [], null, []);
        $this->assertEquals($expected, $actual);
    }

    /**
     * getObject should return null if the data object does not exist
     */
    public function testGetObjectNull()
    {
        $actual = DataObjectHandler::getInstance()->getObject('h953u789h0g73t521'); // doesnt exist
        $this->assertNull($actual);
    }
}
