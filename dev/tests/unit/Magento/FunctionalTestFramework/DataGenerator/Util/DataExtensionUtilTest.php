<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\DataGenerator\Util;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\DataProfileSchemaParser;
use Magento\FunctionalTestingFramework\ObjectManager;
use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;

/**
 * Class DataExtensionUtilTest
 */
class DataExtensionUtilTest extends MagentoTestCase
{
    public function testNoParentData(): void
    {
        $extendedDataObject = [
            'entity' => [
                'extended' => [
                    'type' => 'testType',
                    'extends' => 'parent',
                    'data' => [
                        0 => [
                            'key' => 'testKey',
                            'value' => 'testValue'
                        ]
                    ]
                ]
            ]
        ];

        $this->setMockEntities($extendedDataObject);

        $this->expectExceptionMessage('Parent Entity parent not defined for Entity extended.');
        DataObjectHandler::getInstance()->getObject('extended');
    }

    public function testAlreadyExtendedParentData(): void
    {
        $extendedDataObjects = [
            'entity' => [
                'extended' => [
                    'type' => 'testType',
                    'extends' => 'parent'
                ],
                'parent' => [
                    'type' => 'type',
                    'extends' => 'grandparent'
                ],
                'grandparent' => [
                    'type' => 'grand'
                ]
            ]
        ];

        $this->setMockEntities($extendedDataObjects);

        $this->expectExceptionMessage(
            'Cannot extend an entity that already extends another entity. Entity: parent.' . PHP_EOL
        );
        DataObjectHandler::getInstance()->getObject('extended');
    }

    public function testExtendedVarGetter(): void
    {
        $extendedDataObjects = [
            'entity' => [
                'extended' => [
                    'type' => 'testType',
                    'extends' => 'parent'
                ],
                'parent' => [
                    'type' => 'type',
                    'var' => [
                        'someOtherEntity' => [
                            'entityType' => 'someOtherEntity',
                            'entityKey' => 'id',
                            'key' => 'someOtherEntity'
                        ]
                    ]
                ]
            ]
        ];

        $this->setMockEntities($extendedDataObjects);
        $resultextendedDataObject = DataObjectHandler::getInstance()->getObject('extended');
        // Perform Asserts
        $this->assertEquals('someOtherEntity->id', $resultextendedDataObject->getVarReference('someOtherEntity'));
    }

    public function testGetLinkedEntities(): void
    {
        $extendedDataObjects = [
            'entity' => [
                'extended' => [
                    'type' => 'testType',
                    'extends' => 'parent'
                ],
                'parent' => [
                    'type' => 'type',
                    'requiredEntity' => [
                        'linkedEntity1' => [
                            'type' => 'linkedEntityType',
                            'value' => 'linkedEntity1'
                        ],
                        'linkedEntity2' => [
                            'type' => 'otherEntityType',
                            'value' => 'linkedEntity2'
                        ],
                    ]
                ]
            ]
        ];

        $this->setMockEntities($extendedDataObjects);
        // Perform Asserts
        $resultextendedDataObject = DataObjectHandler::getInstance()->getObject('extended');
        $this->assertEquals('linkedEntity1', $resultextendedDataObject->getLinkedEntitiesOfType('linkedEntityType')[0]);
        $this->assertEquals('linkedEntity2', $resultextendedDataObject->getLinkedEntitiesOfType('otherEntityType')[0]);
    }

    /**
     * Prepare mock entites.
     *
     * @param $mockEntityData
     *
     * @return void
     */
    private function setMockEntities($mockEntityData): void
    {
        $property = new ReflectionProperty(DataObjectHandler::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataProfileSchemaParser = $this->createMock(DataProfileSchemaParser::class);
        $mockDataProfileSchemaParser->expects($this->any())
            ->method('readDataProfiles')
            ->willReturn($mockEntityData);

        $mockObjectManager = $this->createMock(ObjectManager::class);
        $mockObjectManager
            ->method('create')
            ->willReturn($mockDataProfileSchemaParser);

        $property = new ReflectionProperty(ObjectManager::class, 'instance');
        $property->setAccessible(true);
        $property->setValue($mockObjectManager);
    }
}
