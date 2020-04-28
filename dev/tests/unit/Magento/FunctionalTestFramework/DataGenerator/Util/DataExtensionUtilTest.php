<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\DataGenerator\Util;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\DataProfileSchemaParser;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use tests\unit\Util\MagentoTestCase;
use AspectMock\Test as AspectMock;

/**
 * Class DataExtensionUtilTest
 */
class DataExtensionUtilTest extends MagentoTestCase
{
    /**
     * Before method functionality
     * @return void
     */
    protected function setUp(): void
    {
        AspectMock::clean();
    }

    public function testNoParentData()
    {
        $extendedDataObject = [
            'entity' => [
                'extended' => [
                    'type' => 'testType',
                    'extends' => "parent",
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

        $this->expectExceptionMessage("Parent Entity parent not defined for Entity extended.");
        DataObjectHandler::getInstance()->getObject("extended");
    }

    public function testAlreadyExtendedParentData()
    {
        $extendedDataObjects = [
            'entity' => [
                'extended' => [
                    'type' => 'testType',
                    'extends' => "parent"
                ],
                'parent' => [
                    'type' => 'type',
                    'extends' => "grandparent"
                ],
                'grandparent' => [
                    'type' => 'grand'
                ]
            ]
        ];

        $this->setMockEntities($extendedDataObjects);

        $this->expectExceptionMessage(
            "Cannot extend an entity that already extends another entity. Entity: parent." . PHP_EOL
        );
        DataObjectHandler::getInstance()->getObject("extended");
    }

    public function testExtendedVarGetter()
    {
        $extendedDataObjects = [
            'entity' => [
                'extended' => [
                    'type' => 'testType',
                    'extends' => "parent"
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
        $resultextendedDataObject = DataObjectHandler::getInstance()->getObject("extended");
        // Perform Asserts
        $this->assertEquals("someOtherEntity->id", $resultextendedDataObject->getVarReference("someOtherEntity"));
    }

    public function testGetLinkedEntities()
    {
        $extendedDataObjects = [
            'entity' => [
                'extended' => [
                    'type' => 'testType',
                    'extends' => "parent"
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
        $resultextendedDataObject = DataObjectHandler::getInstance()->getObject("extended");
        $this->assertEquals("linkedEntity1", $resultextendedDataObject->getLinkedEntitiesOfType("linkedEntityType")[0]);
        $this->assertEquals("linkedEntity2", $resultextendedDataObject->getLinkedEntitiesOfType("otherEntityType")[0]);
    }

    private function setMockEntities($mockEntityData)
    {
        $property = new \ReflectionProperty(DataObjectHandler::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataProfileSchemaParser = AspectMock::double(DataProfileSchemaParser::class, [
            'readDataProfiles' => $mockEntityData
        ])->make();

        $mockObjectManager = AspectMock::double(ObjectManager::class, [
            'create' => $mockDataProfileSchemaParser
        ])->make();

        AspectMock::double(ObjectManagerFactory::class, [
            'getObjectManager' => $mockObjectManager
        ]);
    }
}
