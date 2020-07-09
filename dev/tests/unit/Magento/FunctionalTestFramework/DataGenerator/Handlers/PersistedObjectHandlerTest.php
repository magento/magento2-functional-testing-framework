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
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\TestLoggingUtil;

/**
 * Class PersistedObjectHandlerTest
 */
class PersistedObjectHandlerTest extends MagentoTestCase
{
    /**
     * Before test functionality
     * @return void
     */
    public function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    public function testCreateEntityWithNonExistingName()
    {
        // Test Data and Variables
        $entityName = "InvalidEntity";
        $entityStepKey = "StepKey";
        $scope = PersistedObjectHandler::TEST_SCOPE;

        $exceptionMessage = "Entity \"" . $entityName . "\" does not exist." .
            "\nException occurred executing action at StepKey \"" . $entityStepKey . "\"";

        $this->expectException(TestReferenceException::class);

        $this->expectExceptionMessage($exceptionMessage);

        $handler = PersistedObjectHandler::getInstance();

        // Call method
        $handler->createEntity(
            $entityStepKey,
            $scope,
            $entityName
        );
    }

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
     * @param string $name
     * @param string $key
     * @param string $value
     * @param string $type
     * @param string $scope
     * @param string $stepKey
     * @dataProvider entityDataProvider
     */
    public function testRetrieveEntityValidField($name, $key, $value, $type, $scope, $stepKey)
    {
        $parserOutputOne = [
            'entity' => [
                $name => [
                    'type' => $type,
                    'data' => [
                        0 => [
                            'key' => $key,
                            'value' => $value
                        ]
                    ]
                ]
            ]
        ];
        $jsonReponseOne = "
            {
               \"" . strtolower($key) . "\" : \"{$value}\"
            }
        ";

        // Mock Classes and Create Entities
        $handler = PersistedObjectHandler::getInstance();

        $this->mockDataHandlerWithOutput($parserOutputOne);
        $this->mockCurlHandler($jsonReponseOne);
        $handler->createEntity($stepKey, $scope, $name);

        // Call method
        $retrieved = $handler->retrieveEntityField($stepKey, $key, $scope);

        $this->assertEquals($value, $retrieved);
    }

    /**
     * @param string $name
     * @param string $key
     * @param string $value
     * @param string $type
     * @param string $scope
     * @param string $stepKey
     * @dataProvider entityDataProvider
     * @throws TestReferenceException
     * @throws TestFrameworkException
     */
    public function testRetrieveEntityInValidField($name, $key, $value, $type, $scope, $stepKey)
    {
        $invalidDataKey = "invalidDataKey";
        $warnMsg = "Undefined field {$invalidDataKey} in entity object with a stepKey of {$stepKey}\n";
        $warnMsg .= "Please fix the invalid reference. This will result in fatal error in next major release.";

        $parserOutputOne = [
            'entity' => [
                $name => [
                    'type' => $type,
                    'data' => [
                        0 => [
                            'key' => $key,
                            'value' => $value
                        ]
                    ]
                ]
            ]
        ];
        $jsonReponseOne = "
            {
               \"" . strtolower($key) . "\" : \"{$value}\"
            }
        ";

        // Mock Classes and Create Entities
        $handler = PersistedObjectHandler::getInstance();

        $this->mockDataHandlerWithOutput($parserOutputOne);
        $this->mockCurlHandler($jsonReponseOne);
        $handler->createEntity($stepKey, $scope, $name);

        // Call method
        $handler->retrieveEntityField($stepKey, $invalidDataKey, $scope);

        // validate log statement
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'warning',
            $warnMsg,
            []
        );
    }

    /**
     * Data provider for testRetrieveEntityField
     */
    public static function entityDataProvider()
    {
        return [
            ['Entity1', 'testKey1', 'testValue1', 'testType', PersistedObjectHandler::HOOK_SCOPE, 'StepKey1'],
            ['Entity2', 'testKey2', 'testValue2', 'testType', PersistedObjectHandler::SUITE_SCOPE, 'StepKey2'],
            ['Entity3', 'testKey3', 'testValue3', 'testType', PersistedObjectHandler::TEST_SCOPE, 'StepKey3']
        ];
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

    public function tearDown(): void
    {
        // Clear out Singleton between tests
        $property = new \ReflectionProperty(PersistedObjectHandler::class, "INSTANCE");
        $property->setAccessible(true);
        $property->setValue(null);

        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    /**
     * After class functionality
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
        parent::tearDownAfterClass();
    }
}
