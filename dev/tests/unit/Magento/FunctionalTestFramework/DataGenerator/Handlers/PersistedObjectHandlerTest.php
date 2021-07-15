<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\DataGenerator\Handlers;

use Exception;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\PersistedObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Persist\CurlHandler;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\ObjectHandlerUtil;
use tests\unit\Util\TestLoggingUtil;

/**
 * Class PersistedObjectHandlerTest
 */
class PersistedObjectHandlerTest extends MagentoTestCase
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Validate testCreateEntityWithNonExistingName.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testCreateEntityWithNonExistingName(): void
    {
        // Test Data and Variables
        $entityName = 'InvalidEntity';
        $entityStepKey = 'StepKey';
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

    /**
     * Validate testCreateSimpleEntity.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testCreateSimpleEntity(): void
    {
        // Test Data and Variables
        $entityName = 'EntityOne';
        $entityStepKey = 'StepKey';
        $dataKey = 'testKey';
        $dataValue = 'testValue';
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
        $jsonResponse = "
            {
               \"" . strtolower($dataKey) . "\" : \"{$dataValue}\"
            }
        ";

        // Mock Classes
        ObjectHandlerUtil::mockDataObjectHandlerWithData($parserOutput);
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

    /**
     * Validate testDeleteSimpleEntity.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testDeleteSimpleEntity(): void
    {
        // Test Data and Variables
        $entityName = 'EntityOne';
        $entityStepKey = 'StepKey';
        $dataKey = 'testKey';
        $dataValue = 'testValue';
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
        $jsonResponse = "
            {
               \"" . strtolower($dataKey) . "\" : \"{$dataValue}\"
            }
        ";

        // Mock Classes
        ObjectHandlerUtil::mockDataObjectHandlerWithData($parserOutput);
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

    /**
     * Validate testGetSimpleEntity.
     *
     * @return void
     * @throws Exception
     */
    public function testGetSimpleEntity(): void
    {
        // Test Data and Variables
        $entityName = 'EntityOne';
        $entityStepKey = 'StepKey';
        $dataKey = 'testKey';
        $dataValue = 'testValue';
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
        $jsonResponse = "
            {
               \"" . strtolower($dataKey) . "\" : \"{$dataValue}\"
            }
        ";

        // Mock Classes
        ObjectHandlerUtil::mockDataObjectHandlerWithData($parserOutput);
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

    /**
     * Validate testUpdateSimpleEntity.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testUpdateSimpleEntity(): void
    {
        $this->markTestSkipped('Potential Bug in DataPersistenceHandler class');
        // Test Data and Variables
        $entityName = 'EntityOne';
        $entityStepKey = 'StepKey';
        $dataKey = 'testKey';
        $dataValue = 'testValue';
        $updateName = 'EntityTwo';
        $updateValue = 'newValue';
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
        $jsonResponse = "
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
        ObjectHandlerUtil::mockDataObjectHandlerWithData($parserOutput);
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

    /**
     * Validate testRetrieveEntityAcrossScopes.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testRetrieveEntityAcrossScopes(): void
    {
        // Test Data and Variables
        $entityNameOne = 'EntityOne';
        $entityStepKeyOne = 'StepKeyOne';
        $dataKeyOne = 'testKeyOne';
        $dataValueOne = 'testValueOne';
        $entityNameTwo = 'EntityTwo';
        $entityStepKeyTwo = 'StepKeyTwo';
        $dataKeyTwo = 'testKeyTwo';
        $dataValueTwo = 'testValueTwo';
        $entityNameThree = 'EntityThree';
        $entityStepKeyThree = 'StepKeyThree';
        $dataKeyThree = 'testKeyThree';
        $dataValueThree = 'testValueThree';

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

        ObjectHandlerUtil::mockDataObjectHandlerWithData($parserOutputOne);
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
     * Validate testRetrieveEntityValidField.
     *
     * @param string $name
     * @param string $key
     * @param string $value
     * @param string $type
     * @param string $scope
     * @param string $stepKey
     * @dataProvider entityDataProvider
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testRetrieveEntityValidField(
        string $name,
        string $key,
        string $value,
        string $type,
        string $scope,
        string $stepKey
    ): void {
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

        ObjectHandlerUtil::mockDataObjectHandlerWithData($parserOutputOne);
        $this->mockCurlHandler($jsonReponseOne);
        $handler->createEntity($stepKey, $scope, $name);

        // Call method
        $retrieved = $handler->retrieveEntityField($stepKey, $key, $scope);

        $this->assertEquals($value, $retrieved);
    }

    /**
     * Validate testRetrieveEntityInValidField.
     *
     * @param string $name
     * @param string $key
     * @param string $value
     * @param string $type
     * @param string $scope
     * @param string $stepKey
     * @dataProvider entityDataProvider
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testRetrieveEntityInValidField(
        string $name,
        string $key,
        string $value,
        string $type,
        string $scope,
        string $stepKey
    ): void {
        $invalidDataKey = 'invalidDataKey';
        $warnMsg = "Undefined field {$invalidDataKey} in entity object with a stepKey of {$stepKey}\n";
        $warnMsg .= 'Please fix the invalid reference. This will result in fatal error in next major release.';

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
        ObjectHandlerUtil::mockDataObjectHandlerWithData($parserOutputOne);
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
     *
     * @return array
     */
    public static function entityDataProvider(): array
    {
        return [
            ['Entity1', 'testKey1', 'testValue1', 'testType', PersistedObjectHandler::HOOK_SCOPE, 'StepKey1'],
            ['Entity2', 'testKey2', 'testValue2', 'testType', PersistedObjectHandler::SUITE_SCOPE, 'StepKey2'],
            ['Entity3', 'testKey3', 'testValue3', 'testType', PersistedObjectHandler::TEST_SCOPE, 'StepKey3']
        ];
    }

    /**
     * Create mock curl handler.
     *
     * @param string $response
     * @throws Exception
     */
    public function mockCurlHandler(string $response): void
    {
        $mockCurlHandler = $this->createMock(CurlHandler::class);
        $mockCurlHandler->expects($this->any())
            ->method('executeRequest')
            ->willReturn($response);
        $mockCurlHandler->expects($this->once())
            ->method('getRequestDataArray')
            ->willReturn([]);
        $mockCurlHandler->expects($this->once())
            ->method('isContentTypeJson')
            ->willReturn(true);

        $property = new ReflectionProperty(CurlHandler::class, "INSTANCE");
        $property->setAccessible(true);
        $property->setValue($mockCurlHandler);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        // Clear out Singleton between tests
        $property = new ReflectionProperty(PersistedObjectHandler::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue(null);

        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
        parent::tearDownAfterClass();
    }
}
