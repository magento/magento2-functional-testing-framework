<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\DataGenerator\Handlers;

use Exception;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\OperationDefinitionObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\OperationDefinitionObject;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\OperationElement;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\OperationDefinitionParser;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\TestLoggingUtil;

/**
 * Class OperationDefinitionObjectHandlerTest
 */
class OperationDefinitionObjectHandlerTest extends MagentoTestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Validate testGetMultipleObjects.
     *
     * @return void
     * @throws Exception
     */
    public function testGetMultipleObjects(): void
    {
        // Data Variables for Assertions
        $dataType1 = 'type1';
        $operationType1 = 'create';
        $operationType2 = 'update';

        /**
         * Parser Output. Just two simple pieces of metadata with 1 field each
         * operationName
         *      createType1
         *          has field
         *              key=id, value=integer
         *      updateType1
         *          has field
         *              key=id, value=integer
         */
        $mockData = [
            OperationDefinitionObjectHandler::ENTITY_OPERATION_ROOT_TAG => [
                'testOperationName' => [
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE => $dataType1,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_TYPE => $operationType1,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_AUTH => 'auth',
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_URL => 'V1/Type1',
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_METHOD => 'POST',
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY => [
                        0 => [
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY => 'id',
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE => 'integer'
                        ],
                    ]
                ],
                [
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE => $dataType1,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_TYPE => $operationType2,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_AUTH => 'auth',
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_URL => 'V1/Type1/{id}',
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_METHOD => 'PUT',
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY => [
                        0 => [
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY => 'id',
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE => 'integer'
                        ],
                    ]
                ]
            ]
        ];
        $this->mockOperationHandlerWithData($mockData);

        //Perform Assertions
        $operationDefinitionManager = OperationDefinitionObjectHandler::getInstance();
        $operations = $operationDefinitionManager->getAllObjects();
        $this->assertArrayHasKey($operationType1 . $dataType1, $operations);
        $this->assertArrayHasKey($operationType2 . $dataType1, $operations);
    }

    /**
     * Validate testDeprecatedOperation.
     *
     * @return void
     * @throws Exception
     */
    public function testDeprecatedOperation(): void
    {
        // Data Variables for Assertions
        $dataType1 = 'type1';
        $operationType1 = 'create';

        /**
         * Parser Output. Just one metadata with 1 field
         * operationName
         *      createType1
         *          has field
         *              key=id, value=integer
         */
        $mockData = [
            OperationDefinitionObjectHandler::ENTITY_OPERATION_ROOT_TAG => [
                'testOperationName' => [
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE => $dataType1,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_TYPE => $operationType1,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_AUTH => 'auth',
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_URL => 'V1/Type1',
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_METHOD => 'POST',
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY => [
                        0 => [
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY => 'id',
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE => 'integer'
                        ],
                    ],
                    OperationDefinitionObjectHandler::OBJ_DEPRECATED => 'deprecation message'
                ]
            ]
        ];
        $this->mockOperationHandlerWithData($mockData);

        //Perform Assertions
        $operationDefinitionManager = OperationDefinitionObjectHandler::getInstance();
        $operations = $operationDefinitionManager->getAllObjects();

        $this->assertArrayHasKey($operationType1 . $dataType1, $operations);
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'notice',
            'NOTICE: 1 metadata operation name violations detected. See mftf.log for details.',
            []
        );
        // test run time deprecation notice
        $operation = $operationDefinitionManager->getOperationDefinition($operationType1, $dataType1);
        $operation->logDeprecated();
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'warning',
            'DEPRECATION: The operation testOperationName is deprecated.',
            ['operationType' => 'create', 'deprecatedMessage' => 'deprecation message']
        );
    }

    /**
     * Validate testObjectCreation.
     *
     * @return void
     * @throws Exception
     */
    public function testObjectCreation(): void
    {
        // Data Variables for Assertions
        $testDataTypeName1 = 'type1';
        $testAuth = 'auth';
        $testUrl = 'V1/dataType';
        $testOperationType = 'create';
        $testMethod = 'POST';
        $testSuccessRegex = '/messages-message-success/';
        $testContentType = 'application/json';
        $testHeaderParam = 'testParameter';
        $testHeaderValue = 'testHeader';
        // Nested Object variables
        $nestedObjectKey = 'objectKey';
        $nestedObjectType = 'objectType';
        $nestedEntryKey1 = 'id';
        $nestedEntryValue1 = 'integer';
        $nestedEntryKey2 = 'name';
        $nestedEntryValue2 = 'string';
        $nestedEntryRequired2 = 'true';
        $nestedEntryKey3 = 'active';
        $nestedEntryValue3 = 'boolean';

        /**
         * Complex Object
         *  testOperation
         *      createType1
         *          has contentType
         *          has headers
         *          has URL
         *          has successRegex
         *          has nested object
         *              key nestedKey type nestedType
         *              has 3 fields
         *                  key id, value integer
         *                  key name, value string, required TRUE
         *                  key active, value boolean
         *
         */
        $mockData = [
            OperationDefinitionObjectHandler::ENTITY_OPERATION_ROOT_TAG => [
                'testOperationName' => [
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE => $testDataTypeName1,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_TYPE => $testOperationType,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_AUTH => $testAuth,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_URL => $testUrl,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_METHOD => $testMethod,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_SUCCESS_REGEX => $testSuccessRegex,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_CONTENT_TYPE => [
                        0 => [
                            'value' => $testContentType
                        ]
                    ],
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_HEADER => [
                        0 => [
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_HEADER_PARAM => $testHeaderParam,
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_HEADER_VALUE => $testHeaderValue,
                        ]
                    ],
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM => [
                        0 => [
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM_KEY => 'testUrlParamKey',
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM_VALUE => 'testUrlParamValue'
                        ]
                    ],
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT => [
                        0 => [
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT_KEY => $nestedObjectKey,
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE => $nestedObjectType,
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY => [
                                0 => [
                                    OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY => $nestedEntryKey1,
                                    OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE =>
                                        $nestedEntryValue1
                                ],
                                1 => [
                                    OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY => $nestedEntryKey2,
                                    OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE =>
                                        $nestedEntryValue2,
                                    OperationDefinitionObjectHandler::ENTITY_OPERATION_REQUIRED =>
                                        $nestedEntryRequired2
                                ],
                                2 => [
                                    OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY => $nestedEntryKey3,
                                    OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE =>
                                        $nestedEntryValue3
                                ]
                            ]
                        ]
                    ],
                ]
            ]
        ];
        // Prepare objects to compare against
        $field = OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY;
        $expectedNestedField = new OperationElement(
            $nestedEntryKey1,
            $nestedEntryValue1,
            $field,
            false,
            [],
            null
        );
        $expectedNestedField2 = new OperationElement(
            $nestedEntryKey2,
            $nestedEntryValue2,
            $field,
            $nestedEntryRequired2,
            [],
            null
        );
        $expectedNestedField3 = new OperationElement(
            $nestedEntryKey3,
            $nestedEntryValue3,
            $field,
            false,
            [],
            null
        );
        $expectedOperation = new OperationElement(
            $nestedObjectKey,
            $nestedObjectType,
            OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT,
            false,
            [],
            [
                0 => $expectedNestedField,
                1 => $expectedNestedField2,
                2 => $expectedNestedField3
            ]
        );

        // Set up mocked data output
        $this->mockOperationHandlerWithData($mockData);

        // Get Operation
        $operationDefinitionManager = OperationDefinitionObjectHandler::getInstance();
        $operation = $operationDefinitionManager->getOperationDefinition($testOperationType, $testDataTypeName1);

        // Perform Asserts
        $this->assertEquals(
            [0 => "{$testHeaderParam}: {$testHeaderValue}",
                1 =>  OperationDefinitionObject::HTTP_CONTENT_TYPE_HEADER . ": {$testContentType}"],
            $operation->getHeaders()
        );
        $this->assertEquals($testOperationType, $operation->getOperation());
        $this->assertEquals($testMethod, $operation->getApiMethod());
        $this->assertEquals($testUrl, $operation->getApiUrl());
        $this->assertEquals($testDataTypeName1, $operation->getDataType());
        $this->assertEquals($testContentType, $operation->getContentType());
        $this->assertEquals($testAuth, $operation->getAuth());
        $this->assertEquals($testSuccessRegex, $operation->getSuccessRegex());

        // perform asserts on the instantiated metadata in the $createOperationByName
        $this->assertEquals($expectedOperation, $operation->getOperationMetadata()[0]);
    }

    /**
     * Validate testObjectArrayCreation.
     *
     * @return void
     * @throws Exception
     */
    public function testObjectArrayCreation(): void
    {
        // Data Variables for Assertions
        $dataType1 = 'type1';
        $operationType1 = 'create';
        $objectArrayKey = 'ObjectArray';
        $twiceNestedObjectKey = 'nestedObjectKey';
        $twiceNestedObjectType = 'nestedObjectType';
        $twiceNestedEntryKey = 'nestedFieldKey';
        $twiceNestedEntryValue = 'string';
        // Parser Output
        /**
         * Metadata with nested array of objects, with a single field
         *  OperationName
         *      createType1
         *          has array with key ObjectArray
         *              objects with key = nestedObjectKey, type = nestedObjectType
         *                  has field with key = nestedFieldKey, value = string
         */
        $mockData = [
            OperationDefinitionObjectHandler::ENTITY_OPERATION_ROOT_TAG => [
                'testOperationName' => [
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE => $dataType1,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_TYPE => $operationType1,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_AUTH => 'auth',
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_URL => 'V1/Type1',
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_METHOD => 'POST',
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY => [
                        0 => [
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT_KEY => $objectArrayKey,
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT => [
                                0 => [
                                    OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT_KEY =>
                                        $twiceNestedObjectKey,
                                    OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE =>
                                        $twiceNestedObjectType,
                                    OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY => [
                                        0 => [
                                            OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY =>
                                                $twiceNestedEntryKey,
                                            OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE =>
                                                $twiceNestedEntryValue
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        // Prepare Objects to compare against
        $twoLevelNestedMetadata = new OperationElement(
            $twiceNestedEntryKey,
            $twiceNestedEntryValue,
            OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY,
            false,
            [],
            null
        );

        $oneLevelNestedMetadata = new OperationElement(
            $twiceNestedObjectKey,
            $twiceNestedObjectType,
            OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT,
            false,
            [],
            [
                0 => $twoLevelNestedMetadata
            ]
        );

        $expectedOperation = new OperationElement(
            $objectArrayKey,
            $twiceNestedObjectType,
            $twiceNestedObjectKey,
            false,
            [
                $twiceNestedObjectKey => $oneLevelNestedMetadata
            ],
            null
        );

        // Set up mocked data output
        $this->mockOperationHandlerWithData($mockData);

        // Get Operation
        $operationDefinitionManager = OperationDefinitionObjectHandler::getInstance();
        $operation = $operationDefinitionManager->getOperationDefinition($operationType1, $dataType1);
        // Make Assertions

        $this->assertEquals($expectedOperation, $operation->getOperationMetadata()[0]);
    }

    /**
     * Validate testLooseJsonCreation.
     *
     * @return void
     * @throws Exception
     */
    public function testLooseJsonCreation(): void
    {
        // Data Variables for Assertions
        $dataType = 'dataType';
        $operationType = 'create';
        $entryKey = 'id';
        $entryValue = 'integer';
        $arrayKey = 'arrayKey';
        $arrayValue = 'string';
        /**
         * Operation with no objects, just an entry and an array of strings
         *  testOperationName
         *      createDataType
         *          has entry key = id, value = integer
         *          has array key = arrayKey
         *              fields of value = string
         */
        $mockData = [
            OperationDefinitionObjectHandler::ENTITY_OPERATION_ROOT_TAG => [
                'testOperationName' => [
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE => $dataType,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_TYPE => $operationType,
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY => [
                        0 => [
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY => $entryKey,
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE => $entryValue
                        ]
                    ],
                    OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY => [
                        0 => [
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_KEY => $arrayKey,
                            OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_VALUE => [
                                0 => [
                                    OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE => $arrayValue
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        // Prepare Objects to assert against
        $entry = new OperationElement(
            $entryKey,
            $entryValue,
            OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY,
            false,
            [],
            null
        );
        $array = new OperationElement(
            $arrayKey,
            $arrayValue,
            OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY,
            false,
            [],
            null
        );

        // Set up mocked data output
        $this->mockOperationHandlerWithData($mockData);

        // get Operations
        $operationDefinitionManager = OperationDefinitionObjectHandler::getInstance();
        $operation = $operationDefinitionManager->getOperationDefinition($operationType, $dataType);

        // Perform Assertions
        $this->assertEquals($entry, $operation->getOperationMetadata()[0]);
        $this->assertEquals($array, $operation->getOperationMetadata()[1]);
    }

    /**
     * Create mock operation handler with data.
     *
     * @param array $mockData
     *
     * @return void
     */
    private function mockOperationHandlerWithData(array $mockData): void
    {
        $operationDefinitionObjectHandlerProperty = new ReflectionProperty(
            OperationDefinitionObjectHandler::class,
            'INSTANCE'
        );
        $operationDefinitionObjectHandlerProperty->setAccessible(true);
        $operationDefinitionObjectHandlerProperty->setValue(null, null);

        $mockOperationParser = $this->createMock(OperationDefinitionParser::class);
        $mockOperationParser
            ->method('readOperationMetadata')
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
                        $mockOperationParser
                    ) {
                        if ($class === OperationDefinitionParser::class) {
                            return $mockOperationParser;
                        }

                        return $objectManager->create($class, $arguments);
                    }
                )
            );

        $property = new ReflectionProperty(ObjectManager::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null, $mockObjectManagerInstance);
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $operationDefinitionObjectHandlerProperty = new ReflectionProperty(
            OperationDefinitionObjectHandler::class,
            'INSTANCE'
        );
        $operationDefinitionObjectHandlerProperty->setAccessible(true);
        $operationDefinitionObjectHandlerProperty->setValue(null, null);

        $objectManagerProperty = new ReflectionProperty(ObjectManager::class, 'instance');
        $objectManagerProperty->setAccessible(true);
        $objectManagerProperty->setValue(null, null);

        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
