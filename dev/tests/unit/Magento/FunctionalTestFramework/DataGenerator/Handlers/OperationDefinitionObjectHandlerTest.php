<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\DataGenerator\Handlers;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\OperationDefinitionObject;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\OperationElement;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\OperationDefinitionObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\OperationDefinitionParser;
use PHPUnit\Framework\TestCase;

/**
 * Class OperationDefinitionObjectHandlerTest
 */
class OperationDefinitionObjectHandlerTest extends TestCase
{
    public function testGetAllObjects()
    {
        // Define data variables
        $testDataTypeName1 = "operationDataTypeName";
        $testDataTypeName2 = "operationDataTypeName2";
        $testAuth = "adminOAuth";
        $testUrl = "V1/object";
        $testOperationType = "create";
        $testMethod = "POST";
        $testSuccessRegex = "/messages-message-success/";
        $testContentType = "application/json";
        $testHeaderParam = "testParameter";
        $testHeaderValue = "testHeader";
        // Nested data variables
        $nestedObjectKey = "object";
        $nestedObjectType = "objectType";
        $nestedEntryKey1 = "id";
        $nestedEntryValue1 = "integer";
        $nestedEntryKey2 = "name";
        $nestedEntryValue2 = "string";
        $nestedEntryRequired2 = "true";
        $nestedEntryKey3 = "active";
        $nestedEntryValue3 = "boolean";
        // Two Level nested data variables
        $objectArrayKey = "ObjectArray";
        $twiceNestedObjectKey = "nestedObjectKey";
        $twiceNestedObjectType = "nestedObjectType";
        $twiceNestedEntryKey = "nestedFieldKey";
        $twiceNestedEntryValue = "string";

        // Operation Metadata. BE CAREFUL WHEN EDITING, Objects defined below rely on values in this array.

        $mockData = [OperationDefinitionObjectHandler::ENTITY_OPERATION_ROOT_TAG => [
            "testOperationName" => [
                OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE => $testDataTypeName1,
                OperationDefinitionObjectHandler::ENTITY_OPERATION_TYPE => $testOperationType,
                OperationDefinitionObjectHandler::ENTITY_OPERATION_AUTH => $testAuth,
                OperationDefinitionObjectHandler::ENTITY_OPERATION_URL => $testUrl,
                OperationDefinitionObjectHandler::ENTITY_OPERATION_METHOD => $testMethod,
                OperationDefinitionObjectHandler::ENTITY_OPERATION_SUCCESS_REGEX => $testSuccessRegex,
                OperationDefinitionObjectHandler::ENTITY_OPERATION_CONTENT_TYPE => [
                    0 => [
                        "value" => $testContentType
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
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM_KEY => "testUrlParamKey",
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM_VALUE => "testUrlParamValue"
                    ]
                ],
                OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT => [
                    0 => [
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT_KEY => $nestedObjectKey,
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE => $nestedObjectType,
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY => [
                            0 => [
                                OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY => $nestedEntryKey1,
                                OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE => $nestedEntryValue1
                            ],
                            1 => [
                                OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY => $nestedEntryKey2,
                                OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE => $nestedEntryValue2,
                                OperationDefinitionObjectHandler::ENTITY_OPERATION_REQUIRED => $nestedEntryRequired2
                            ],
                            2 => [
                                OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY => $nestedEntryKey3,
                                OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE => $nestedEntryValue3
                            ]
                        ]
                    ]
                ],
                OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY => [
                    0 => [
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT_KEY => $objectArrayKey,
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT => [
                            0 => [
                                OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT_KEY => $twiceNestedObjectKey,
                                OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE => $twiceNestedObjectType,
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
            ],
            "testOperationName2" => [
                OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE => $testDataTypeName2,
                OperationDefinitionObjectHandler::ENTITY_OPERATION_TYPE => $testOperationType,
                OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY => [
                    0 => [
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY => "id",
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE => "integer"
                    ],
                    1 => [
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY => "name",
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE => "string"
                    ]
                ],
                OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY => [
                    0 => [
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_KEY => "arrayKey",
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_VALUE => [
                            0 => [
                                OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE => "string"
                            ]
                        ]
                    ]
                ]
            ]
        ]];

        //prepare OperationElements to compare against.
        $field = OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY;

        $expectedNestedMetadata1 = new OperationElement($nestedEntryKey1, $nestedEntryValue1, $field, false, [], null);
        $expectedNestedMetadata2 = new OperationElement(
            $nestedEntryKey2,
            $nestedEntryValue2,
            $field,
            $nestedEntryRequired2,
            [],
            null
        );
        $expectedNestedMetadata3 = new OperationElement($nestedEntryKey3, $nestedEntryValue3, $field, false, [], null);
        $expectedOperation1 = new OperationElement(
            $nestedObjectKey,
            $nestedObjectType,
            OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT,
            false,
            [],
            [0 => $expectedNestedMetadata1, 1 => $expectedNestedMetadata2, 2 =>$expectedNestedMetadata3]
        );

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
            [0 => $twoLevelNestedMetadata]
        );

        $expectedOperation2 = new OperationElement(
            $objectArrayKey,
            $twiceNestedObjectType,
            $twiceNestedObjectKey,
            false,
            [$twiceNestedObjectKey => $oneLevelNestedMetadata],
            null
        );

        // Set up mocked data output
        $this->setMockParserOutput($mockData);

        // get Operations
        $operationDefinitionManager = OperationDefinitionObjectHandler::getInstance();
        $operations = $operationDefinitionManager->getAllObjects();
        $operationByName = $operationDefinitionManager->getOperationDefinition($testOperationType, $testDataTypeName1);

        // perform asserts on $operations
        $this->assertCount(2, $operations);
        $this->assertArrayHasKey($testOperationType . $testDataTypeName1, $operations);
        $this->assertArrayHasKey($testOperationType . $testDataTypeName2, $operations);

        // perform asserts on $createOperationByName
        $this->assertEquals(
            [0 => "{$testHeaderParam}: {$testHeaderValue}",
                1 =>  OperationDefinitionObject::HTTP_CONTENT_TYPE_HEADER . ": {$testContentType}"],
            $operationByName->getHeaders()
        );
        $this->assertEquals($testOperationType, $operationByName->getOperation());
        $this->assertEquals($testMethod, $operationByName->getApiMethod());
        $this->assertEquals($testUrl, $operationByName->getApiUrl());
        $this->assertEquals($testDataTypeName1, $operationByName->getDataType());
        $this->assertEquals($testContentType, $operationByName->getContentType());
        $this->assertEquals($testAuth, $operationByName->getAuth());
        $this->assertEquals($testSuccessRegex, $operationByName->getSuccessRegex());

        // perform asserts on the instantiated metadata in the $createOperationByName
        $this->assertEquals($expectedOperation1, $operationByName->getOperationMetadata()[0]);
        $this->assertEquals($expectedOperation2, $operationByName->getOperationMetadata()[1]);

    }

    /**
     * Function used to set mock for parser return and force init method to run between tests.
     *
     * @param array $data
     */
    private function setMockParserOutput($data)
    {
        // clear section object handler value to inject parsed content
        $property = new \ReflectionProperty(
            OperationDefinitionObjectHandler::class,
            'DATA_DEFINITION_OBJECT_HANDLER'
        );
        $property->setAccessible(true);
        $property->setValue(null);

        $mockOperationParser = AspectMock::double(
            OperationDefinitionParser::class,
            ["readOperationMetadata" => $data]
        )->make();
        $instance = AspectMock::double(ObjectManager::class, ['create' => $mockOperationParser])->make();
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);
    }
}
