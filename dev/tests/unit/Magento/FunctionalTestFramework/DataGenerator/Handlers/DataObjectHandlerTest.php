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
            ],
            'EntityTwo' => [
                'type' => 'testType',
                'extends' => 'EntityOne',
                'data' => [
                    0 => [
                        'key' => 'testKeyTwo',
                        'value' => 'testValueTwo'
                    ]
                ]
            ],
        ]
    ];

    const PARSER_OUTPUT_WITH_EXTEND = [
        'entity' => [
            'EntityOne' => [
                'name' => 'EntityOne',
                'type' => 'testType',
                'data' => [
                    0 => [
                        'key' => 'testKey',
                        'value' => 'testValue'
                    ]
                ]
            ],
            'EntityTwo' => [
                'name' => 'EntityTwo',
                'type' => 'testType',
                'extends' => 'EntityOne',
                'data' => [
                    0 => [
                        'key' => 'testKeyTwo',
                        'value' => 'testValueTwo'
                    ]
                ],
            ],
            'EntityThree' => [
                'name' => 'EntityThree',
                'type' => 'testType',
                'extends' => 'EntityOne',
                'data' => [
                    0 => [
                        'key' => 'testKeyThree',
                        'value' => 'testValueThree'
                    ]
                ],
            ]
        ]
    ];

    const PARSER_OUTPUT_WITH_EXTEND_INVALID = [
        'entity' => [
            'EntityOne' => [
                'name' => 'EntityOne',
                'type' => 'testType',
                'extends' => 'EntityOne',
                'data' => [
                    0 => [
                        'key' => 'testKey',
                        'value' => 'testValue'
                    ]
                ]
            ],
            'EntityTwo' => [
                'name' => 'EntityTwo',
                'type' => 'testType',
                'data' => [
                    0 => [
                        'key' => 'testKeyTwo',
                        'value' => 'testValueTwo'
                    ]
                ],
            ],
            'EntityThree' => [
                'name' => 'EntityThree',
                'type' => 'testType',
                'extends' => 'EntityThree',
                'data' => [
                    0 => [
                        'key' => 'testKeyThree',
                        'value' => 'testValueThree'
                    ]
                ],
            ]
        ]
    ];

    /**
     * getAllObjects should contain the expected data object
     */
    public function testGetAllObjects()
    {
        $this->setUpMockDataObjectHander(self::PARSER_OUTPUT);

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
        $this->setUpMockDataObjectHander(self::PARSER_OUTPUT);

        // Call the method under test
        $actual = DataObjectHandler::getInstance()->getObject('EntityOne');

        // Assert
        $expected = new EntityDataObject('EntityOne', 'testType', ['testkey' => 'testValue'], [], null, []);
        $this->assertEquals($expected, $actual);
    }

    /**
     * getAllObjects should return the expected data object if it exists
     */
    public function testGetObjectNull()
    {
        $this->setUpMockDataObjectHander(self::PARSER_OUTPUT);

        $actual = DataObjectHandler::getInstance()->getObject('h953u789h0g73t521'); // doesnt exist
        $this->assertNull($actual);
    }

    /**
     * getAllObjects should contain the expected data object with extends
     */
    public function testGetAllObjectsWithDataExtends()
    {
        $this->setUpMockDataObjectHander(self::PARSER_OUTPUT_WITH_EXTEND);

        // Call the method under test
        $actual = DataObjectHandler::getInstance()->getAllObjects();

        // Assert
        $expected = new EntityDataObject(
            'EntityTwo',
            'testType',
            ['testkey' => 'testValue', 'testkeytwo' => 'testValueTwo'],
            [],
            null,
            [],
            'EntityOne'
        );
        $this->assertArrayHasKey('EntityTwo', $actual);
        $this->assertEquals($expected, $actual['EntityTwo']);
    }

    /**
     * getObject should return the expected data object with extended data if it exists
     */
    public function testGetObjectWithDataExtends()
    {
        $this->setUpMockDataObjectHander(self::PARSER_OUTPUT_WITH_EXTEND);

        // Call the method under test
        $actual = DataObjectHandler::getInstance()->getObject('EntityTwo');

        // Assert
        $expected = new EntityDataObject(
            'EntityTwo',
            'testType',
            ['testkey' => 'testValue', 'testkeytwo' => 'testValueTwo'],
            [],
            null,
            [],
            'EntityOne'
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * getAllObjects should throw TestFrameworkException exception if some data extends itself
     */
    public function testGetAllObjectsWithDataExtendsItself()
    {
        $this->setUpMockDataObjectHander(self::PARSER_OUTPUT_WITH_EXTEND_INVALID);

        $this->expectException(\Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException::class);
        $this->expectExceptionMessage(
            "Mftf Data can not extend from itself: "
            . self::PARSER_OUTPUT_WITH_EXTEND_INVALID['entity']['EntityOne']['name']
        );

        // Call the method under test
        DataObjectHandler::getInstance()->getAllObjects();
    }

    /**
     * getObject should throw TestFrameworkException exception if requested data extends itself
     */
    public function testGetObjectWithDataExtendsItself()
    {
        $this->setUpMockDataObjectHander(self::PARSER_OUTPUT_WITH_EXTEND_INVALID);

        $this->expectException(\Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException::class);
        $this->expectExceptionMessage(
            "Mftf Data can not extend from itself: "
            . self::PARSER_OUTPUT_WITH_EXTEND_INVALID['entity']['EntityOne']['name']
        );

        // Call the method under test
        DataObjectHandler::getInstance()->getObject(
            self::PARSER_OUTPUT_WITH_EXTEND_INVALID['entity']['EntityOne']['name']
        );
    }

    /**
     * Set up everything required to mock DataObjectHander::getInstance()
     * The first call to getInstance() uses these mocks to emulate the parser, initializing internal state
     * according to the PARSER_OUTPUT value
     *
     * @param array $entityDataArray
     */
    private function setUpMockDataObjectHander($entityDataArray)
    {
        // Clear DataObjectHandler singleton if already set
        $property = new \ReflectionProperty(DataObjectHandler::class, "INSTANCE");
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataProfileSchemaParser = AspectMock::double(DataProfileSchemaParser::class, [
            'readDataProfiles' => $entityDataArray
        ])->make();

        $mockObjectManager = AspectMock::double(ObjectManager::class, [
            'create' => $mockDataProfileSchemaParser
        ])->make();

        AspectMock::double(ObjectManagerFactory::class, [
            'getObjectManager' => $mockObjectManager
        ]);
    }
}
