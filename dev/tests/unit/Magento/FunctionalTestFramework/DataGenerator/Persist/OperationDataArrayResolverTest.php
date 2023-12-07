<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\DataGenerator\Persist;

use Exception;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\OperationDefinitionObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Persist\OperationDataArrayResolver;
use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\EntityDataObjectBuilder;
use tests\unit\Util\OperationDefinitionBuilder;
use tests\unit\Util\OperationElementBuilder;
use tests\unit\Util\TestLoggingUtil;

class OperationDataArrayResolverTest extends MagentoTestCase
{
    const NESTED_METADATA_EXPECTED_RESULT = ['parentType' => [
        'name' => 'Hopper',
        'address' => ['city' => 'Hawkins', 'state' => 'Indiana', 'zip' => 78758],
        'isPrimary' => true,
        'gpa' => 3.5678,
        'phone' => 5555555
    ]];

    const NESTED_METADATA_ARRAY_RESULT = ['parentType' => [
        'name' => 'Hopper',
        'isPrimary' => true,
        'gpa' => 3.5678,
        'phone' => 5555555,
        'address' => [
            ['city' => 'Hawkins', 'state' => 'Indiana', 'zip' => 78758],
            ['city' => 'Austin', 'state' => 'Texas', 'zip' => 78701],
        ]
    ]];

    /**
     * Before test functionality
     *
     * @return void
     */
    public function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Test a basic metadata resolve between primitive values and a primitive data set
     * <object>
     *  <field>stringField</field>
     *  <field>intField</field>
     *  <field>boolField</field>
     *  <field>doubleField</field>
     * </object>
     *
     * @return void
     * @throws Exception
     */
    public function testBasicPrimitiveMetadataResolve(): void
    {
        // set up data object
        $entityObjectBuilder = new EntityDataObjectBuilder();
        $testDataObject = $entityObjectBuilder->build();

        // set up meta data operation elements
        $operationElementBuilder = new OperationElementBuilder();
        $operationElement = $operationElementBuilder->build();

        // resolve data object and metadata array
        $operationDataArrayResolver = new OperationDataArrayResolver();
        $result = $operationDataArrayResolver->resolveOperationDataArray(
            $testDataObject,
            [$operationElement],
            'create'
        );

        // assert on result
        $expectedResult = ['testType' => [
            'name' => 'Hopper',
            'gpa' => 3.5678,
            'phone' => 5555555,
            'isPrimary' => true
        ]];

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test a nested metadata operation resolve:
     * <object>
     *  <field>someField</field>
     *  <field>objectRef</field>
     * </object>
     *
     * @return void
     * @throws Exception
     */
    public function testNestedMetadataResolve(): void
    {
        // set up data objects
        $entityDataObjBuilder = new EntityDataObjectBuilder();
        $parentDataObject = $entityDataObjBuilder
            ->withName('parentObject')
            ->withType('parentType')
            ->withLinkedEntities(['childObject' => 'childType'])
            ->build();

        $childDataObject = $entityDataObjBuilder
            ->withName('childObject')
            ->withType('childType')
            ->withDataFields(['city' => 'Hawkins', 'state' => 'Indiana', 'zip' => '78758'])
            ->build();

        // mock data object handler
        $this->mockDataObjectHandler($childDataObject);

        // set up metadata objects
        $parentOpElementBuilder = new OperationElementBuilder();
        $parentElement = $parentOpElementBuilder
            ->withKey('parentType')
            ->withType('parentType')
            ->addFields(['address' => 'childType'])
            ->build();

        $operationDefinitionBuilder = new OperationDefinitionBuilder();
        $childOperationDefinition = $operationDefinitionBuilder
            ->withName('createChildType')
            ->withOperation('create')
            ->withType('childType')
            ->withMetadata([
                'city' => 'string',
                'state' => 'string',
                'zip' => 'integer'
            ])->build();

        // mock meta data object handler
        $this->mockOperationDefinitionObjectHandler($childOperationDefinition);

        // resolve data object and metadata array
        $operationResolver = new OperationDataArrayResolver();
        $result = $operationResolver->resolveOperationDataArray($parentDataObject, [$parentElement], 'create', false);

        // assert on the result
        $this->assertEquals(self::NESTED_METADATA_EXPECTED_RESULT, $result);
    }

    /**
     * Test a nested metadata operation:
     * <object>
     *  <field>someField</field>
     *  <object>
     *      <field>anotherField</field>
     *  </object>
     * </object>
     *
     * @return void
     * @throws Exception
     */
    public function testNestedMetadata(): void
    {
        // set up data objects
        $entityDataObjectBuilder = new EntityDataObjectBuilder();
        $parentDataObject = $entityDataObjectBuilder
            ->withName('parentObject')
            ->withType('parentType')
            ->withLinkedEntities(['childObject' => 'childType'])
            ->build();

        $childDataObject = $entityDataObjectBuilder
            ->withName('childObject')
            ->withType('childType')
            ->withDataFields(['city' => 'Hawkins', 'state' => 'Indiana', 'zip' => '78758'])
            ->build();

        // mock data object handler
        $this->mockDataObjectHandler($childDataObject);

        // set up metadata objects
        $childOpElementBuilder = new OperationElementBuilder();
        $childElement = $childOpElementBuilder
            ->withKey('address')
            ->withType('childType')
            ->withFields(['city' => 'string', 'state' => 'string', 'zip' => 'integer'])
            ->build();

        $parentOpElementBuilder = new OperationElementBuilder();
        $parentElement = $parentOpElementBuilder
            ->withKey('parentType')
            ->withType('parentType')
            ->addElements(['address' => $childElement])
            ->build();

        // resolve data object and metadata array
        $operationResolver = new OperationDataArrayResolver();
        $result = $operationResolver->resolveOperationDataArray($parentDataObject, [$parentElement], 'create', false);

        // assert on the result
        $this->assertEquals(self::NESTED_METADATA_EXPECTED_RESULT, $result);
    }

    /**
     * Test a nested metadata operation with a declared object:
     * <object>
     *  <field>someField</field>
     *  <array>
     *   <object>
     *      <field>anotherField</field>
     *   </object>
     *  </array
     * </object>
     *
     * @return void
     * @throws Exception
     */
    public function testNestedMetadataArrayOfObjects(): void
    {
        // set up data objects
        $entityDataObjectBuilder = new EntityDataObjectBuilder();
        $parentDataObject = $entityDataObjectBuilder
            ->withName('parentObject')
            ->withType('parentType')
            ->withLinkedEntities(['childObject1' => 'childType', 'childObject2' => 'childType'])
            ->build();

        // mock data object handler
        $callback = function ($name) {
            $entityDataObjectBuilder = new EntityDataObjectBuilder();

            if ($name === 'childObject1') {
                return $entityDataObjectBuilder
                    ->withName('childObject1')
                    ->withType('childType')
                    ->withDataFields(['city' => 'Hawkins', 'state' => 'Indiana', 'zip' => '78758'])
                    ->build();
            }

            if ($name === 'childObject2') {
                return $entityDataObjectBuilder
                    ->withName('childObject2')
                    ->withType('childType')
                    ->withDataFields(['city' => 'Austin', 'state' => 'Texas', 'zip' => '78701'])
                    ->build();
            }
        };
        $this->mockDataObjectHandler($callback);

        // set up metadata objects
        $childOpElementBuilder = new OperationElementBuilder();
        $childElement = $childOpElementBuilder
            ->withKey('childType')
            ->withType('childType')
            ->withFields(['city' => 'string', 'state' => 'string', 'zip' => 'integer'])
            ->build();

        $arrayOpElementBuilder = new OperationElementBuilder();
        $arrayElement = $arrayOpElementBuilder
            ->withKey('address')
            ->withType('childType')
            ->withFields([])
            ->withElementType(OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY)
            ->withNestedElements(['childType' => $childElement])
            ->build();

        $parentOpElementBuilder = new OperationElementBuilder();
        $parentElement = $parentOpElementBuilder
            ->withKey('parentType')
            ->withType('parentType')
            ->addElements(['address' => $arrayElement])
            ->build();

        // resolve data object and metadata array
        $operationResolver = new OperationDataArrayResolver();
        $result = $operationResolver->resolveOperationDataArray($parentDataObject, [$parentElement], 'create', false);

        // Do assert on result here
        $this->assertEquals(self::NESTED_METADATA_ARRAY_RESULT, $result);
    }

    /**
     * Test a nested metadata operation with a value pointing to an object ref:
     * <object>
     *  <field>someField</field>
     *  <array>
     *   <value>object</value>
     *  </array
     * </object>
     *
     * @return void
     * @throws Exception
     */
    public function testNestedMetadataArrayOfValue(): void
    {
        // set up data objects
        $entityDataObjectBuilder = new EntityDataObjectBuilder();
        $parentDataObject = $entityDataObjectBuilder
            ->withName('parentObject')
            ->withType('parentType')
            ->withLinkedEntities(['childObject1' => 'childType', 'childObject2' => 'childType'])
            ->build();

        $callback = function ($name) {
            $entityDataObjectBuilder = new EntityDataObjectBuilder();

            if ($name === 'childObject1') {
                return $entityDataObjectBuilder
                    ->withName('childObject1')
                    ->withType('childType')
                    ->withDataFields(['city' => 'Hawkins', 'state' => 'Indiana', 'zip' => '78758'])
                    ->build();
            };

            if ($name === 'childObject2') {
                return $entityDataObjectBuilder
                    ->withName('childObject2')
                    ->withType('childType')
                    ->withDataFields(['city' => 'Austin', 'state' => 'Texas', 'zip' => '78701'])
                    ->build();
            }
        };
        // mock data object handler
        $this->mockDataObjectHandler($callback);

        // set up metadata objects
        $arrayOpElementBuilder = new OperationElementBuilder();
        $arrayElement = $arrayOpElementBuilder
            ->withKey('address')
            ->withType('childType')
            ->withElementType(OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY)
            ->withNestedElements([])
            ->withFields([])
            ->build();

        $parentOpElementBuilder = new OperationElementBuilder();
        $parentElement = $parentOpElementBuilder
            ->withKey('parentType')
            ->withType('parentType')
            ->addElements(['address' => $arrayElement])
            ->build();

        $operationDefinitionBuilder = new OperationDefinitionBuilder();
        $childOperationDefinition = $operationDefinitionBuilder
            ->withName('createChildType')
            ->withOperation('create')
            ->withType('childType')
            ->withMetadata([
                'city' => 'string',
                'state' => 'string',
                'zip' => 'integer'
            ])->build();

        // mock meta data object handler
        $this->mockOperationDefinitionObjectHandler($childOperationDefinition);

        // resolve data object and metadata array
        $operationResolver = new OperationDataArrayResolver();
        $result = $operationResolver->resolveOperationDataArray($parentDataObject, [$parentElement], 'create', false);

        // Do assert on result here
        $this->assertEquals(self::NESTED_METADATA_ARRAY_RESULT, $result);
    }

    public function testNestedMetadataArrayOfDiverseObjects(): void
    {
        $entityDataObjBuilder = new EntityDataObjectBuilder();
        $parentDataObject = $entityDataObjBuilder
            ->withName('parentObject')
            ->withType('parentType')
            ->withLinkedEntities(['child1Object' => 'childType1','child2Object' => 'childType2'])
            ->build();

        $child1DataObject = $entityDataObjBuilder
            ->withName('child1Object')
            ->withType('childType1')
            ->withDataFields(['city' => 'Testcity','zip' => 12345])
            ->build();

        $child2DataObject = $entityDataObjBuilder
            ->withName('child2Object')
            ->withType('childType2')
            ->withDataFields(['city' => 'Testcity 2','zip' => 54321,'state' => 'Teststate'])
            ->build();

        $dataObjectCallback = function ($name) use ($child1DataObject, $child2DataObject) {
            switch ($name) {
                case 'child1Object':
                    return $child1DataObject;
                case 'child2Object':
                    return $child2DataObject;
            }
        };
        $this->mockDataObjectHandler($dataObjectCallback);

        $operationDefinitionBuilder = new OperationDefinitionBuilder();
        $child1OperationDefinition = $operationDefinitionBuilder
            ->withName('createchildType1')
            ->withOperation('create')
            ->withType('childType1')
            ->withMetadata([
                'city' => 'string',
                'zip' => 'integer'
            ])->build();

        $child2OperationDefinition = $operationDefinitionBuilder
            ->withName('createchildType2')
            ->withOperation('create')
            ->withType('childType2')
            ->withMetadata([
                'city' => 'string',
                'zip' => 'integer',
                'state' => 'string'
            ])->build();

        $operationObjectCallback = function ($name) use ($child1OperationDefinition, $child2OperationDefinition) {
            switch ($name) {
                case 'createchildType1':
                    return $child1OperationDefinition;
                case 'createchildType2':
                    return $child2OperationDefinition;
            }
        };
        $this->mockOperationDefinitionObjectHandler($operationObjectCallback);

        $arrayObElementBuilder = new OperationElementBuilder();
        $arrayElement = $arrayObElementBuilder
            ->withKey('address')
            ->withType(['childType1','childType2'])
            ->withFields([])
            ->withElementType(OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY)
            //->withNestedElements(['childType1' => $child1Element, 'childType2' => $child2Element])
            ->build();

        $parentOpElementBuilder = new OperationElementBuilder();
        $parentElement = $parentOpElementBuilder
            ->withKey('parentType')
            ->withType('parentType')
            ->addElements(['address' => $arrayElement])
            ->build();

        $operationResolver = new OperationDataArrayResolver();
        $result = $operationResolver->resolveOperationDataArray($parentDataObject, [$parentElement], 'create', false);

        $expectedResult = [
            'parentType' => [
                'address' => [
                    [
                        'city' => 'Testcity',
                        'zip' => '12345'
                    ],
                    [
                        'city' => 'Testcity 2',
                        'zip' => '54321',
                        'state' => 'Teststate'
                    ]
                ],
                'name' => 'Hopper',
                'gpa' => '3.5678',
                'phone' => '5555555',
                'isPrimary' => '1'
            ]
        ];

        $this->assertEquals($expectedResult, $result);
    }

    public function testExtendedWithRequiredEntity(): void
    {
        $entityDataObjectBuilder = new EntityDataObjectBuilder();
        $extEntityDataObject = $entityDataObjectBuilder
            ->withName('extEntity')
            ->withType('entity')
            ->withLinkedEntities(['baseSubentity' => 'subentity','extSubentity' => 'subentity'])
            ->build();

        $callback = function ($name) {
            $entityDataObjectBuilder = new EntityDataObjectBuilder();

            if ($name === 'baseSubentity') {
                return $entityDataObjectBuilder
                    ->withName('baseSubentity')
                    ->withType('subentity')
                    ->withDataFields(['subtest' => 'BaseSubtest'])
                    ->build();
            }

            if ($name === 'extSubentity') {
                return $entityDataObjectBuilder
                    ->withName('extSubentity')
                    ->withType('subentity')
                    ->withDataFields(['subtest' => 'ExtSubtest'])
                    ->build();
            }
        };
        $this->mockDataObjectHandler($callback);

        $subentityOpElementBuilder = new OperationElementBuilder();
        $subentityOpElement = $subentityOpElementBuilder
            ->withKey('sub')
            ->withType('subentity')
            ->withElementType('object')
            ->withFields(['subtest' => 'string'])
            ->build();

        $operationResolver = new OperationDataArrayResolver();
        $result = $operationResolver->resolveOperationDataArray(
            $extEntityDataObject,
            [$subentityOpElement],
            'create',
            false
        );

        $expected = [
            'sub' => [
                'subtest' => 'ExtSubtest'
            ]
        ];

        $this->assertEquals($expected, $result);
    }
    /**
     * After class functionality
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }

    /**
     * Set up mock DataObjectHandler
     *
     * @param $childDataObject
     *
     * @return void
     */
    private function mockDataObjectHandler($childDataObject): void
    {
        $instance = $this->createMock(DataObjectHandler::class);
        if (is_callable($childDataObject)) {
            $instance->expects($this->any())
                ->method('getObject')
                ->willReturnCallback($childDataObject);
        } else {
            $instance->expects($this->any())
                ->method('getObject')
                ->willReturn($childDataObject);
        }

        $property = new ReflectionProperty(DataObjectHandler::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue(null, $instance);
    }

    /**
     * Set up mock OperationDefinitionObjectHandler
     *
     * @param $childOperationDefinition
     *
     * @return void
     */
    private function mockOperationDefinitionObjectHandler($childOperationDefinition): void
    {
        $instance = $this->createPartialMock(OperationDefinitionObjectHandler::class, ['getObject']);
        if (is_callable($childOperationDefinition)) {
            $instance->expects($this->any())
                ->method('getObject')
                ->willReturnCallback($childOperationDefinition);
        } else {
            $instance->expects($this->any())
                ->method('getObject')
                ->willReturn($childOperationDefinition);
        }

        $property = new ReflectionProperty(OperationDefinitionObjectHandler::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue(null, $instance);
    }
}
