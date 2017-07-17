<?php
/**
 * Created by PhpStorm.
 * User: imeron
 * Date: 6/22/17
 * Time: 2:13 PM
 */

namespace Magento\AcceptanceTestFramework\DataGenerator\Managers;

use Magento\AcceptanceTestFramework\DataGenerator\DataGeneratorConstants;
use Magento\AcceptanceTestFramework\DataGenerator\Objects\EntityDataObject;
use Magento\AcceptanceTestFramework\DataProfileSchemaParser;
use Magento\AcceptanceTestFramework\ObjectManagerFactory;

class DataManager
{
    private $data = []; // an array of entity names to the entity data objects themselves

    public function __construct($type)
    {
        $this->data = $this->parseDataEntities($type);
    }

    private function parseDataEntities($type)
    {
        $entityObjects = array();
        $objectManager = ObjectManagerFactory::getObjectManager();
        $entityParser = $objectManager->create(DataProfileSchemaParser::class);
        $entities = $entityParser->readDataProfiles();

        foreach ($entities[DataGeneratorConstants::ENTITY_DATA] as $entityName => $entity) {
            $entityType = $entity[DataGeneratorConstants::ENTITY_DATA_TYPE];

            if (strcasecmp($entityType, $type) == 0) {
                $entityXmlObject = new EntityDataObject(
                    $entityName,
                    $entityType,
                    $entity[DataGeneratorConstants::DATA_VALUES] ?? null,
                    $entity[DataGeneratorConstants::REQUIRED_ENTITY] ?? null
                );

                $entityObjects[$entityXmlObject->getName()] = $entityXmlObject;
            }
        }
        unset($entityName);
        unset($entity);

        return $entityObjects;
    }

    public function getEntity($entityName)
    {
        return $this->data[$entityName];
    }

    public function getAllEntities()
    {
        return $this->data;
    }
}