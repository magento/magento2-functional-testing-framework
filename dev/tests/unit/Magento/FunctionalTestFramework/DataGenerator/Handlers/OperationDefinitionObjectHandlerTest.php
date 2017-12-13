<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\DataGenerator\Handlers;

use AspectMock\Test as AspectMock;
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
        // Operation Metadata. BE CAREFUL WHEN EDITING, Objects defined below rely on values in this array.
        $mockData = ["operation" => [
            "testOperationName" => [
                "dataType" => "operationDataTypeName",
                "type" => "create",
                "auth" => "adminOauth",
                "url" => "/V1/object/",
                "method" => "POST",
                "successRegex" => "/messages-message-success/",
                "contentType" => [
                    0 => [
                        "value" => "application/json"
                    ]
                ],
                "header" => [
                    0 => [
                        "param" => "testParameter",
                        "value" => "testHeader"
                    ]
                ],
                "param" => [
                    0 => [
                        "key" => "testUrlParamKey",
                        "value" => "testUrlParamValue"
                    ]
                ],
                "object" => [
                    0 => [
                        "key" => "object",
                        "dataType" => "objectType",
                        "field" => [
                            0 => [
                                "key" => "id",
                                "value" => "integer"
                            ],
                            1 => [
                                "key" => "name",
                                "value" => "string",
                                "required" => "true"
                            ],
                            2 => [
                                "key" => "active",
                                "value" => "boolean"
                            ]
                        ]
                    ]
                ],
                "array" => [
                    0 => [
                        "key" => "ObjectArray",
                        "object" => [
                            0 => [
                                "key" => "nestedObjectKey",
                                "dataType" => "nestedObjectType",
                                "field" => [
                                    0 => [
                                        "key" => "nestedFieldKey",
                                        "value" => "string"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            "testOperationName2" => [
                "dataType" => "operationDataTypeName2",
                "type" => "create",
                "field" => [
                    0 => [
                        "key" => "id",
                        "value" => "integer"
                    ],
                    1 => [
                        "key" => "name",
                        "value" => "string"
                    ]
                ],
                "array" => [
                    0 => [
                        "key" => "arrayKey",
                        "value" => [
                            0 => [
                                "value" => "string"
                            ]
                        ]
                    ]
                ]
            ]
        ]];

        //prepare OperationElements to compare against.
        $expectedNestedMetadata1 = new OperationElement("id", "integer", "field", false, [], null);
        $expectedNestedMetadata2 = new OperationElement("name", "string", "field", true, [], null);
        $expectedNestedMetadata3 = new OperationElement("active", "boolean", "field", false, [], null);
        $expectedOperation1 = new OperationElement(
            "object",
            "objectType",
            "object",
            false,
            [],
            [0 => $expectedNestedMetadata1, 1 => $expectedNestedMetadata2, 2 =>$expectedNestedMetadata3]
        );

        $twoLevelNestedMetadata = new OperationElement("nestedFieldKey", "string", "field", false, [], null);
        $oneLevelNestedMetadata = new OperationElement(
            "nestedObjectKey",
            "nestedObjectType",
            "object",
            false,
            [],
            [0 => $twoLevelNestedMetadata]
        );
        $expectedOperation2 = new OperationElement(
            "ObjectArray",
            "nestedObjectType",
            "nestedObjectKey",
            false,
            ["nestedObjectKey" => $oneLevelNestedMetadata],
            null
        );

        // Set up mocked data output
        $this->setMockParserOutput($mockData);

        // get Operations
        $operationDefinitionManager = OperationDefinitionObjectHandler::getInstance();
        $operations = $operationDefinitionManager->getAllObjects();
        $operationByName = $operationDefinitionManager->getOperationDefinition("create", "operationDataTypeName");

        // perform asserts on $operations
        $this->assertCount(2, $operations);
        $this->assertArrayHasKey("createoperationDataTypeName", $operations);
        $this->assertArrayHasKey("createoperationDataTypeName2", $operations);

        // perform asserts on $createOperationByName
        $this->assertEquals(
            [0 => 'testParameter: testHeader', 1 => "Content-Type: application/json"],
            $operationByName->getHeaders()
        );
        $this->assertEquals("create", $operationByName->getOperation());
        $this->assertEquals("POST", $operationByName->getApiMethod());
        $this->assertEquals("V1/object", $operationByName->getApiUrl());
        $this->assertEquals("operationDataTypeName", $operationByName->getDataType());
        $this->assertEquals("application/json", $operationByName->getContentType());
        $this->assertEquals("adminOauth", $operationByName->getAuth());
        $this->assertEquals("/messages-message-success/", $operationByName->getSuccessRegex());

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

        $mockSectionParser = AspectMock::double(
            OperationDefinitionParser::class,
            ["readOperationMetadata" => $data]
        )->make();
        $instance = AspectMock::double(ObjectManager::class, ['create' => $mockSectionParser])->make();
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);
    }
}
