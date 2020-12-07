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
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\ObjectHandlerUtil;
use tests\unit\Util\TestLoggingUtil;

/**
 * Class DataObjectHandlerTest
 */
class DataObjectHandlerTest extends MagentoTestCase
{
    /**
     * Setup method
     */
    public function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

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

    const PARSER_OUTPUT_DEPRECATED = [
        'entity' => [
            'EntityOne' => [
                'type' => 'testType',
                'data' => [
                    0 => [
                        'key' => 'testKey',
                        'value' => 'testValue'
                    ]
                ],
                'deprecated' => "deprecation message",
                'filename' => "filename.xml"
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
        ObjectHandlerUtil::mockDataObjectHandlerWithData(self::PARSER_OUTPUT);

        // Call the method under test
        $actual = DataObjectHandler::getInstance()->getAllObjects();

        // Assert
        $expected = new EntityDataObject('EntityOne', 'testType', ['testkey' => 'testValue'], [], null, []);
        $this->assertArrayHasKey('EntityOne', $actual);
        $this->assertEquals($expected, $actual['EntityOne']);
    }

    /**
     * test deprecated data object
     */
    public function testDeprecatedDataObject()
    {
        ObjectHandlerUtil::mockDataObjectHandlerWithData(self::PARSER_OUTPUT_DEPRECATED);

        // Call the method under test
        $actual = DataObjectHandler::getInstance()->getAllObjects();

        //validate deprecation warning
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'warning',
            "DEPRECATION: The data entity 'EntityOne' is deprecated.",
            ["fileName" => "filename.xml", "deprecatedMessage" => "deprecation message"]
        );
    }

    /**
     * getObject should return the expected data object if it exists
     */
    public function testGetObject()
    {
        ObjectHandlerUtil::mockDataObjectHandlerWithData(self::PARSER_OUTPUT);

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
        ObjectHandlerUtil::mockDataObjectHandlerWithData(self::PARSER_OUTPUT);

        $actual = DataObjectHandler::getInstance()->getObject('h953u789h0g73t521'); // doesnt exist
        $this->assertNull($actual);
    }

    /**
     * getAllObjects should contain the expected data object with extends
     */
    public function testGetAllObjectsWithDataExtends()
    {
        ObjectHandlerUtil::mockDataObjectHandlerWithData(self::PARSER_OUTPUT_WITH_EXTEND);

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
        ObjectHandlerUtil::mockDataObjectHandlerWithData(self::PARSER_OUTPUT_WITH_EXTEND);

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
        ObjectHandlerUtil::mockDataObjectHandlerWithData(self::PARSER_OUTPUT_WITH_EXTEND_INVALID);

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
        ObjectHandlerUtil::mockDataObjectHandlerWithData(self::PARSER_OUTPUT_WITH_EXTEND_INVALID);

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
     * clean up function runs after all tests
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
