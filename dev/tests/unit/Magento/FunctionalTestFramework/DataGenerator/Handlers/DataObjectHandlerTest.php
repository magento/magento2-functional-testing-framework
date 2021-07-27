<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\DataGenerator\Handlers;

use Exception;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\DataProfileSchemaParser;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\TestLoggingUtil;

/**
 * Class DataObjectHandlerTest
 */
class DataObjectHandlerTest extends MagentoTestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp(): void
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
     * Validate getAllObjects should contain the expected data object.
     *
     * @return void
     * @throws Exception
     */
    public function testGetAllObjects(): void
    {
        $this->mockDataObjectHandlerWithData(self::PARSER_OUTPUT);

        // Call the method under test
        $actual = DataObjectHandler::getInstance()->getAllObjects();

        // Assert
        $expected = new EntityDataObject('EntityOne', 'testType', ['testkey' => 'testValue'], [], null, []);
        $this->assertArrayHasKey('EntityOne', $actual);
        $this->assertEquals($expected, $actual['EntityOne']);
    }

    /**
     * Validate test deprecated data object.
     *
     * @return void
     * @throws Exception
     */
    public function testDeprecatedDataObject(): void
    {
        $this->mockDataObjectHandlerWithData(self::PARSER_OUTPUT_DEPRECATED);

        // Call the method under test
        DataObjectHandler::getInstance()->getAllObjects();

        //validate deprecation warning
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'warning',
            'DEPRECATION: The data entity \'EntityOne\' is deprecated.',
            ['fileName' => 'filename.xml', 'deprecatedMessage' => 'deprecation message']
        );
    }

    /**
     * Validate getObject should return the expected data object if it exists.
     *
     * @return void
     * @throws Exception
     */
    public function testGetObject(): void
    {
        $this->mockDataObjectHandlerWithData(self::PARSER_OUTPUT);

        // Call the method under test
        $actual = DataObjectHandler::getInstance()->getObject('EntityOne');

        // Assert
        $expected = new EntityDataObject('EntityOne', 'testType', ['testkey' => 'testValue'], [], null, []);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Validate getAllObjects should return the expected data object if it exists.
     *
     * @return void
     * @throws Exception
     */
    public function testGetObjectNull(): void
    {
        $this->mockDataObjectHandlerWithData(self::PARSER_OUTPUT);

        $actual = DataObjectHandler::getInstance()->getObject('h953u789h0g73t521'); // doesnt exist
        $this->assertNull($actual);
    }

    /**
     * Validate getAllObjects should contain the expected data object with extends.
     *
     * @return void
     * @throws Exception
     */
    public function testGetAllObjectsWithDataExtends(): void
    {
        $this->mockDataObjectHandlerWithData(self::PARSER_OUTPUT_WITH_EXTEND);

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
     * Validate getObject should return the expected data object with extended data if it exists.
     *
     * @return void
     * @throws Exception
     */
    public function testGetObjectWithDataExtends(): void
    {
        $this->mockDataObjectHandlerWithData(self::PARSER_OUTPUT_WITH_EXTEND);

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
     * Validate getAllObjects should throw TestFrameworkException exception if some data extends itself.
     *
     * @return void
     * @throws Exception
     */
    public function testGetAllObjectsWithDataExtendsItself(): void
    {
        $this->mockDataObjectHandlerWithData(self::PARSER_OUTPUT_WITH_EXTEND_INVALID);

        $this->expectException(TestFrameworkException::class);
        $this->expectExceptionMessage(
            'Mftf Data can not extend from itself: '
            . self::PARSER_OUTPUT_WITH_EXTEND_INVALID['entity']['EntityOne']['name']
        );

        // Call the method under test
        DataObjectHandler::getInstance()->getAllObjects();
    }

    /**
     * Validate getObject should throw TestFrameworkException exception if requested data extends itself.
     *
     * @return void
     * @throws Exception
     */
    public function testGetObjectWithDataExtendsItself(): void
    {
        $this->mockDataObjectHandlerWithData(self::PARSER_OUTPUT_WITH_EXTEND_INVALID);

        $this->expectException(TestFrameworkException::class);
        $this->expectExceptionMessage(
            'Mftf Data can not extend from itself: '
            . self::PARSER_OUTPUT_WITH_EXTEND_INVALID['entity']['EntityOne']['name']
        );

        // Call the method under test
        DataObjectHandler::getInstance()->getObject(
            self::PARSER_OUTPUT_WITH_EXTEND_INVALID['entity']['EntityOne']['name']
        );
    }

    /**
     * Create mock data object handler with data.
     *
     * @param array $mockData
     *
     * @return void
     */
    private function mockDataObjectHandlerWithData(array $mockData): void
    {
        $dataObjectHandlerProperty = new ReflectionProperty(DataObjectHandler::class, "INSTANCE");
        $dataObjectHandlerProperty->setAccessible(true);
        $dataObjectHandlerProperty->setValue(null);

        $mockDataProfileSchemaParser =  $this->createMock(DataProfileSchemaParser::class);
        $mockDataProfileSchemaParser
            ->method('readDataProfiles')
            ->willReturn($mockData);

        $objectManager = ObjectManagerFactory::getObjectManager();
        $mockObjectManagerInstance = $this->createMock(ObjectManager::class);
        $mockObjectManagerInstance
            ->method('create')
            ->will(
                $this->returnCallback(
                    function (
                        string $class,
                        array $arguments = []
                    ) use (
                        $objectManager,
                        $mockDataProfileSchemaParser
                    ) {
                        if ($class === DataProfileSchemaParser::class) {
                            return $mockDataProfileSchemaParser;
                        }

                        return $objectManager->create($class, $arguments);
                    }
                )
            );

        $property = new ReflectionProperty(ObjectManager::class, 'instance');
        $property->setAccessible(true);
        $property->setValue($mockObjectManagerInstance);
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $dataObjectHandlerProperty = new ReflectionProperty(DataObjectHandler::class, "INSTANCE");
        $dataObjectHandlerProperty->setAccessible(true);
        $dataObjectHandlerProperty->setValue(null);

        $objectManagerProperty = new ReflectionProperty(ObjectManager::class, 'instance');
        $objectManagerProperty->setAccessible(true);
        $objectManagerProperty->setValue(null);

        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
