<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Managers;

use Exception;
use Magento\AcceptanceTestFramework\DataGenerator\DataGeneratorConstants;
use Magento\AcceptanceTestFramework\DataGenerator\Objects\EntityDataObject;
use Magento\AcceptanceTestFramework\DataGenerator\Parsers\DataProfileSchemaParser;
use Magento\AcceptanceTestFramework\ObjectManagerFactory;

class DataManager
{
    private $data = []; // an array of entity names to the entity data objects themselves
    private $arrayData;
    private static $dataManager;

    public static function getInstance()
    {
        if (!self::$dataManager) {
            $entityParser = ObjectManagerFactory::getObjectManager()->create(DataProfileSchemaParser::class);
            $entityParsedData = $entityParser->readDataProfiles();

            if (!$entityParsedData) {
                throw new Exception(sprintf("No entities could be parsed from xml definitions"));
            }

            self::$dataManager = new DataManager($entityParsedData);
        }

        return self::$dataManager;
    }

    private function __construct($arrayData)
    {
        $this->arrayData = $arrayData;
    }

    private function parseDataEntities()
    {
        $entityObjects = array();
        $entities = $this->arrayData;

        foreach ($entities[DataGeneratorConstants::ENTITY_DATA] as $entityName => $entity) {
            $entityType = $entity[DataGeneratorConstants::ENTITY_DATA_TYPE];

            $dataValues = [];
            $linkedEntities = [];
            $arrayValues = [];

            if (array_key_exists(DataGeneratorConstants::DATA_VALUES, $entity)) {
                foreach ($entity[DataGeneratorConstants::DATA_VALUES] as $dataElement) {
                    $dataElementKey = strtolower($dataElement[DataGeneratorConstants::DATA_ELEMENT_KEY]);
                    $dataElementValue = $dataElement[DataGeneratorConstants::DATA_ELEMENT_VALUE];

                    $dataValues[$dataElementKey] = $dataElementValue;
                }
                unset($dataElement);
            }

            if (array_key_exists(DataGeneratorConstants::REQUIRED_ENTITY, $entity)) {
                foreach ($entity[DataGeneratorConstants::REQUIRED_ENTITY] as $linkedEntity) {
                    $linkedEntityName = $linkedEntity[DataGeneratorConstants::REQUIRED_ENTITY_VALUE];
                    $linkedEntityType = $linkedEntity[DataGeneratorConstants::REQUIRED_ENTITY_TYPE];

                    $linkedEntities[$linkedEntityName] = $linkedEntityType;
                }
                unset($linkedEntity);
            }

            if (array_key_exists(DataGeneratorConstants::ARRAY_VALUES, $entity)) {
                foreach ($entity[DataGeneratorConstants::ARRAY_VALUES] as $arrayElement) {
                    $arrayKey = $arrayElement[DataGeneratorConstants::ARRAY_ELEMENT_KEY];
                    foreach ($arrayElement[DataGeneratorConstants::ARRAY_ELEMENT_VALUE] as $arrayValue) {
                        $arrayValues[] = $arrayValue['value'];
                    }

                    $dataValues[$arrayKey] = $arrayValues;
                }
            }

            $entityXmlObject = new EntityDataObject(
                $entityName,
                $entityType,
                $dataValues,
                $linkedEntities
            );

            $entityObjects[$entityXmlObject->getName()] = $entityXmlObject;

        }
        unset($entityName);
        unset($entity);

        $this->data = $entityObjects;
    }

    public function getEntity($entityName)
    {
        return $this->getAllEntities()[$entityName];
    }

    public function getAllEntities()
    {
        if (!$this->data) {
            $this->parseDataEntities();
        }

        return $this->data;
    }
}
