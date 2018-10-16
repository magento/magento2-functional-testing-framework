<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\DataGenerator\Handlers;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\PersistedObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\DataProfileSchemaParser;
use Magento\FunctionalTestingFramework\DataGenerator\Persist\CurlHandler;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;

/**
 * Class PersistedObjectHandlerTest
 */
class PersistedObjectHandlerTest extends MagentoTestCase
{
    public function testCreateSimpleEntity()
    {
        // Test Data and Variables
        $entityName = "EntityOne";
        $entityStepKey = "StepKey";
        $dataKey = "testKey";
        $dataValue = "testValue";
        $scope = PersistedObjectHandler::TEST_SCOPE;
        $parserOutput = [
            'entity' => [
                'EntityOne' => [
                    'type' => 'testType',
                    'data' => [
                        0 => [
                            'key' => $dataKey,
                            'value' => $dataValue
                        ]
                    ]
                ]
            ]
        ];
        $jsonResponse =  "
            {
               \"" . strtolower($dataKey) . "\" : \"{$dataValue}\"
            }
        ";

        // Mock Classes
        $this->mockDataHandlerWithOutput($parserOutput);
        $this->mockCurlHandler($jsonResponse);
        $handler = PersistedObjectHandler::getInstance();

        // Call method
        $handler->createEntity(
            $entityStepKey,
            $scope,
            $entityName
        );

        $persistedValue = $handler->retrieveEntityField($entityStepKey, $dataKey, $scope);
        $this->assertEquals($dataValue, $persistedValue);
    }

    public function testDeleteSimpleEntity()
    {
        // Test Data and Variables
        $entityName = "EntityOne";
        $entityStepKey = "StepKey";
        $dataKey = "testKey";
        $dataValue = "testValue";
        $scope = PersistedObjectHandler::TEST_SCOPE;
        $parserOutput = [
            'entity' => [
                'EntityOne' => [
                    'type' => 'testType',
                    'data' => [
                        0 => [
                            'key' => $dataKey,
                            'value' => $dataValue
                        ]
                    ]
                ]
            ]
        ];
        $jsonResponse =  "
            {
               \"" . strtolower($dataKey) . "\" : \"{$dataValue}\"
            }
        ";

        // Mock Classes
        $this->mockDataHandlerWithOutput($parserOutput);
        $this->mockCurlHandler($jsonResponse);
        $handler = PersistedObjectHandler::getInstance();

        // Call method
        $handler->createEntity(
            $entityStepKey,
            $scope,
            $entityName
        );

        $handler->deleteEntity(
            $entityStepKey,
            $scope
        );

        // Handler found and called Delete on existing entity
        $this->addToAssertionCount(1);
    }

    public function testGetSimpleEntity()
    {
        // Test Data and Variables
        $entityName = "EntityOne";
        $entityStepKey = "StepKey";
        $dataKey = "testKey";
        $dataValue = "testValue";
        $scope = PersistedObjectHandler::TEST_SCOPE;
        $parserOutput = [
            'entity' => [
                'EntityOne' => [
                    'type' => 'testType',
                    'data' => [
                        0 => [
                            'key' => $dataKey,
                            'value' => $dataValue
                        ]
                    ]
                ]
            ]
        ];
        $jsonResponse =  "
            {
               \"" . strtolower($dataKey) . "\" : \"{$dataValue}\"
            }
        ";

        // Mock Classes
        $this->mockDataHandlerWithOutput($parserOutput);
        $this->mockCurlHandler($jsonResponse);
        $handler = PersistedObjectHandler::getInstance();

        // Call method
        $handler->getEntity(
            $entityStepKey,
            $scope,
            $entityName
        );

        $persistedValue = $handler->retrieveEntityField($entityStepKey, $dataKey, $scope);
        $this->assertEquals($dataValue, $persistedValue);
    }

    public function testUpdateSimpleEntity()
    {
        $this->markTestSkipped("Potential Bug in DataPersistenceHandler class");
        // Test Data and Variables
        $entityName = "EntityOne";
        $entityStepKey = "StepKey";
        $dataKey = "testKey";
        $dataValue = "testValue";
        $updateName = "EntityTwo";
        $updateValue = "newValue";
        $scope = PersistedObjectHandler::TEST_SCOPE;
        $parserOutput = [
            'entity' => [
                $entityName => [
                    'type' => 'testType',
                    'data' => [
                        0 => [
                            'key' => $dataKey,
                            'value' => $dataValue
                        ]
                    ]
                ],
                $updateName => [
                    'type' => 'testType',
                    'data' => [
                        0 => [
                            'key' => $dataKey,
                            'value' => $updateValue
                        ]
                    ]
                ]
            ]
        ];
        $jsonResponse =  "
            {
               \"" . strtolower($dataKey) . "\" : \"{$dataValue}\"
            }
        ";
        $updatedResponse = "
            {
               \"" . strtolower($dataKey) . "\" : \"{$updateValue}\"
            }
        ";

        // Mock Classes
        $this->mockDataHandlerWithOutput($parserOutput);
        $this->mockCurlHandler($jsonResponse);
        $handler = PersistedObjectHandler::getInstance();
        $handler->createEntity(
            $entityStepKey,
            $scope,
            $entityName
        );
        $this->mockCurlHandler($updatedResponse);
        
        // Call method
        $handler->updateEntity(
            $entityStepKey,
            $scope,
            $updateName
        );

        $persistedValue = $handler->retrieveEntityField($entityStepKey, $dataKey, $scope);
        $this->assertEquals($updateValue, $persistedValue);
    }

    public function testRetrieveEntityAcrossScopes()
    {
        // Test Data and Variables
        $entityNameOne = "EntityOne";
        $entityStepKeyOne = "StepKeyOne";
        $dataKeyOne = "testKeyOne";
        $dataValueOne = "testValueOne";
        $entityNameTwo = "EntityTwo";
        $entityStepKeyTwo = "StepKeyTwo";
        $dataKeyTwo = "testKeyTwo";
        $dataValueTwo = "testValueTwo";
        $entityNameThree = "EntityThree";
        $entityStepKeyThree = "StepKeyThree";
        $dataKeyThree = "testKeyThree";
        $dataValueThree = "testValueThree";

        $parserOutputOne = [
            'entity' => [
                $entityNameOne => [
                    'type' => 'testType',
                    'data' => [
                        0 => [
                            'key' => $dataKeyOne,
                            'value' => $dataValueOne
                        ]
                    ]
                ],
                $entityNameTwo => [
                    'type' => 'testType',
                    'data' => [
                        0 => [
                            'key' => $dataKeyTwo,
                            'value' => $dataValueTwo
                        ]
                    ]
                ],
                $entityNameThree => [
                    'type' => 'testType',
                    'data' => [
                        0 => [
                            'key' => $dataKeyThree,
                            'value' => $dataValueThree
                        ]
                    ]
                ]
            ]
        ];
        $jsonReponseOne = "
            {
               \"" . strtolower($dataKeyOne) . "\" : \"{$dataValueOne}\"
            }
        ";
        $jsonReponseTwo = "
            {
               \"" . strtolower($dataKeyTwo) . "\" : \"{$dataValueTwo}\"
            }
        ";
        $jsonReponseThree = "
            {
               \"" . strtolower($dataKeyThree) . "\" : \"{$dataValueThree}\"
            }
        ";

        // Mock Classes and Create Entities
        $handler = PersistedObjectHandler::getInstance();

        $this->mockDataHandlerWithOutput($parserOutputOne);
        $this->mockCurlHandler($jsonReponseOne);
        $handler->createEntity(
            $entityStepKeyOne,
            PersistedObjectHandler::TEST_SCOPE,
            $entityNameOne
        );

        $this->mockCurlHandler($jsonReponseTwo);
        $handler->createEntity(
            $entityStepKeyTwo,
            PersistedObjectHandler::HOOK_SCOPE,
            $entityNameTwo
        );

        $this->mockCurlHandler($jsonReponseThree);
        $handler->createEntity(
            $entityStepKeyThree,
            PersistedObjectHandler::SUITE_SCOPE,
            $entityNameThree
        );

        // Call method
        $retrievedFromTest = $handler->retrieveEntityField(
            $entityStepKeyOne,
            $dataKeyOne,
            PersistedObjectHandler::HOOK_SCOPE
        );
        $retrievedFromHook = $handler->retrieveEntityField(
            $entityStepKeyTwo,
            $dataKeyTwo,
            PersistedObjectHandler::SUITE_SCOPE
        );
        $retrievedFromSuite = $handler->retrieveEntityField(
            $entityStepKeyThree,
            $dataKeyThree,
            PersistedObjectHandler::TEST_SCOPE
        );

        $this->assertEquals($dataValueOne, $retrievedFromTest);
        $this->assertEquals($dataValueTwo, $retrievedFromHook);
        $this->assertEquals($dataValueThree, $retrievedFromSuite);
    }

    /**
     * Mocks DataObjectHandler to use given output to create
     * @param $parserOutput
     * @throws \Exception
     */
    public function mockDataHandlerWithOutput($parserOutput)
    {
        // Clear DataObjectHandler singleton if already set
        $property = new \ReflectionProperty(DataObjectHandler::class, "INSTANCE");
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataProfileSchemaParser = AspectMock::double(DataProfileSchemaParser::class, [
            'readDataProfiles' => $parserOutput
        ])->make();

        $mockObjectManager = AspectMock::double(ObjectManager::class, [
            'create' => $mockDataProfileSchemaParser
        ])->make();

        AspectMock::double(ObjectManagerFactory::class, [
            'getObjectManager' => $mockObjectManager
        ]);
    }

    public function mockCurlHandler($response)
    {
        AspectMock::double(CurlHandler::class, [
            "__construct" => null,
            "executeRequest" => $response,
            "getRequestDataArray" => [],
            "isContentTypeJson" => true
        ]);
    }

    public function tearDown()
    {
        // Clear out Singleton between tests
        $property = new \ReflectionProperty(PersistedObjectHandler::class, "INSTANCE");
        $property->setAccessible(true);
        $property->setValue(null);

        parent::tearDown(); // TODO: Change the autogenerated stub
    }
}
