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
    const ENV_DATA_OBJECT_NAME = '_ENV';

    /**
     * Singleton method to access instance of DataManager.
     * @return DataManager
     * @throws Exception
     */
    public static function getInstance()
    {
        if (!self::$dataManager) {
            $entityParser = ObjectManagerFactory::getObjectManager()->create(DataProfileSchemaParser::class);
            $entityParsedData = $entityParser->readDataProfiles();

            if (!$entityParsedData) {
                throw new Exception("No entities could be parsed from xml definitions");
            }

            self::$dataManager = new DataManager($entityParsedData);
        }

        return self::$dataManager;
    }

    /**
     * DataManager constructor.
     * @param array $arrayData
     */
    private function __construct($arrayData)
    {
        $this->arrayData = $arrayData;
    }

    /**
     * Adds all .env variables defined in the PROJECT_ROOT as EntityDataObjects. This is to allow resolution
     * of these variables when referenced in a cest.
     */
    private function parseEnvVariables()
    {
        $envFilename = PROJECT_ROOT . '/.env';

        if (file_exists($envFilename)) {
            $envData = [];

            $envFile = file($envFilename);

            foreach ($envFile as $entry) {
                $params = explode("=", $entry);
                if (count($params) != 2) {
                    continue;
                }
                $envData[strtolower(trim($params[0]))] = trim($params[1]);
            }


            $envDataObject = new EntityDataObject(self::ENV_DATA_OBJECT_NAME, 'environment', $envData, null);
            $this->data[$envDataObject->getName()] = $envDataObject;
        }
    }

    /**
     * Parses data xml and extracts all information into EntityDataObject.
     */
    private function parseDataEntities()
    {
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

            $this->data[$entityXmlObject->getName()] = $entityXmlObject;

        }
        unset($entityName);
        unset($entity);
    }

    /**
     * Method returns a single data entity by name based on what is defined in data.xml.
     * @param $entityName
     * @return EntityDataObject
     */
    public function getEntity($entityName)
    {
        return $this->getAllEntities()[$entityName];
    }

    /**
     * Method returns all data entities read from data.xml into objects.
     * @return array
     */
    public function getAllEntities()
    {
        if (!$this->data) {
            $this->parseDataEntities();
            $this->parseEnvVariables();
        }

        return $this->data;
    }
}
