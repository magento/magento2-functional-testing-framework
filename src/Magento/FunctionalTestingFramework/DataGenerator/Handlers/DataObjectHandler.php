<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers;

use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\DataProfileSchemaParser;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;

// @codingStandardsIgnoreFile
class DataObjectHandler implements ObjectHandlerInterface
{
    /**
     * @var DataObjectHandler $DATA_OBJECT_HANDLER
     */
    private static $DATA_OBJECT_HANDLER;

    /**
     * @var array $arrayData
     */
    private $arrayData = [];

    /**
     * @var array $data
     */
    private $data = [];

    const ENV_DATA_OBJECT_NAME = '_ENV';

    const ENTITY_DATA = 'entity';
    const ENTITY_DATA_NAME = 'name';
    const ENTITY_DATA_TYPE = 'type';

    const DATA_VALUES = 'data';
    const DATA_ELEMENT_KEY = 'key';
    const DATA_ELEMENT_VALUE = 'value';
    const DATA_ELEMENT_UNIQUENESS_ATTR = 'unique';
    const DATA_ELEMENT_UNIQUENESS_ATTR_VALUE_PREFIX = 'prefix';
    const DATA_ELEMENT_UNIQUENESS_ATTR_VALUE_SUFFIX = 'suffix';

    const ARRAY_VALUES = 'array';
    const ARRAY_ELEMENT_KEY = 'key';
    const ARRAY_ELEMENT_ITEM = 'item';
    const ARRAY_ELEMENT_ITEM_VALUE = 'value';

    const VAR_VALUES = 'var';
    const VAR_KEY = 'key';
    const VAR_ENTITY = 'entityType';
    const VAR_FIELD = 'entityKey';
    const VAR_ENTITY_FIELD_SEPARATOR = '->';

    const REQUIRED_ENTITY = 'required-entity';
    const REQUIRED_ENTITY_TYPE = 'type';
    const REQUIRED_ENTITY_VALUE = 'value';

    /**
     * Singleton method to retrieve instance of DataArrayProcessor
     *
     * @return DataObjectHandler
     * @throws \Exception
     */
    public static function getInstance()
    {
        if (!self::$DATA_OBJECT_HANDLER) {
            self::$DATA_OBJECT_HANDLER = new DataObjectHandler();
            self::$DATA_OBJECT_HANDLER->initDataObjects();
        }

        return self::$DATA_OBJECT_HANDLER;
    }

    /**
     * DataArrayProcessor constructor.
     * @constructor
     */
    private function __construct()
    {
        // private constructor
    }

    /**
     * Retrieves the object representation of data represented in data.xml
     * @param string $entityName
     * @return EntityDataObject | null
     */
    public function getObject($entityName)
    {
        if (array_key_exists($entityName, $this->getAllObjects())) {
            return $this->getAllObjects()[$entityName];
        }

        return null;
    }

    /**
     * Retrieves all object representations of all data represented in data.xml
     * @return array
     */
    public function getAllObjects()
    {
        return $this->data;
    }

    /**
     * Method to initialize parsing of data.xml and read into objects.
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initDataObjects()
    {
        $entityParser = ObjectManagerFactory::getObjectManager()->create(DataProfileSchemaParser::class);
        $entityParsedData = $entityParser->readDataProfiles();

        if (!$entityParsedData) {
            // No *Data.xml files found so give up
            return;
        }

        $this->arrayData = $entityParsedData;
        $this->parseEnvVariables();
        $this->parseDataEntities();
    }

    /**
     * Adds all .env variables defined in the PROJECT_ROOT as EntityDataObjects. This is to allow resolution
     * of these variables when referenced in a cest.
     * @return void
     */
    private function parseEnvVariables()
    {
        $envFilename = PROJECT_ROOT . DIRECTORY_SEPARATOR . '.env';
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
            $envDataObject = new EntityDataObject(
                self::ENV_DATA_OBJECT_NAME,
                'environment',
                $envData,
                null,
                null
            );
            $this->data[$envDataObject->getName()] = $envDataObject;
        }
    }

    /**
     * Parses array output of parses into EntityDataObjects.
     * @return void
     */
    private function parseDataEntities()
    {
        $entities = $this->arrayData;

        foreach ($entities[self::ENTITY_DATA] as $entityName => $entity) {
            $entityType = $entity[self::ENTITY_DATA_TYPE];

            $dataValues = [];
            $linkedEntities = [];
            $arrayValues = [];
            $vars = [];
            $uniquenessValues = [];

            if (array_key_exists(self::DATA_VALUES, $entity)) {
                $dataValues = $this->parseDataElements($entity);
                $uniquenessValues = $this->parseUniquenessValues($entity);
            }

            if (array_key_exists(self::REQUIRED_ENTITY, $entity)) {
                $linkedEntities = $this->parseRequiredEntityElements($entity);
            }

            if (array_key_exists(self::ARRAY_VALUES, $entity)) {
                foreach ($entity[self::ARRAY_VALUES] as $arrayElement) {
                    $arrayKey = $arrayElement[self::ARRAY_ELEMENT_KEY];
                    foreach ($arrayElement[self::ARRAY_ELEMENT_ITEM] as $arrayValue) {
                        $arrayValues[] = $arrayValue[self::ARRAY_ELEMENT_ITEM_VALUE];
                    }

                    $dataValues[strtolower($arrayKey)] = $arrayValues;
                }
            }

            if (array_key_exists(self::VAR_VALUES, $entity)) {
                $vars = $this->parseVarElements($entity);
            }

            $entityDataObject = new EntityDataObject(
                $entityName,
                $entityType,
                $dataValues,
                $linkedEntities,
                $uniquenessValues,
                $vars
            );

            $this->data[$entityDataObject->getName()] = $entityDataObject;
        }
        unset($entityName);
        unset($entity);
    }

    /**
     * Parses <data> elements in an entity, and returns them as an array of "lowerKey"=>value.
     * @param array $entityData
     * @return array
     */
    private function parseDataElements($entityData)
    {
        $dataValues = [];
        foreach ($entityData[self::DATA_VALUES] as $dataElement) {
            $dataElementKey = strtolower($dataElement[self::DATA_ELEMENT_KEY]);
            $dataElementValue = $dataElement[self::DATA_ELEMENT_VALUE] ?? "";
            $dataValues[$dataElementKey] = $dataElementValue;
        }
        return $dataValues;
    }

    /**
     * Parses through <data> elements in an entity to return an array of "DataKey" => "UniquenessAttribute"
     * @param array $entityData
     * @return array
     */
    private function parseUniquenessValues($entityData)
    {
        $uniquenessValues = [];
        foreach ($entityData[self::DATA_VALUES] as $dataElement) {
            if (array_key_exists(self::DATA_ELEMENT_UNIQUENESS_ATTR, $dataElement)) {
                $dataElementKey = strtolower($dataElement[self::DATA_ELEMENT_KEY]);
                $uniquenessValues[$dataElementKey] = $dataElement[self::DATA_ELEMENT_UNIQUENESS_ATTR];
            }
        }
        return $uniquenessValues;
    }

    /**
     * Parses <required-entity> elements given entity, and returns them as an array of "EntityValue"=>"EntityType"
     * @param array $entityData
     * @return array
     */
    private function parseRequiredEntityElements($entityData)
    {
        $linkedEntities = [];
        foreach ($entityData[self::REQUIRED_ENTITY] as $linkedEntity) {
            $linkedEntityName = $linkedEntity[self::REQUIRED_ENTITY_VALUE];
            $linkedEntityType = $linkedEntity[self::REQUIRED_ENTITY_TYPE];

            $linkedEntities[$linkedEntityName] = $linkedEntityType;
        }
        return $linkedEntities;
    }

    /**
     * Parses <var> elements in given entity, and returns them as an array of "Key"=> entityType -> entityKey
     * @param array $entityData
     * @return array
     */
    private function parseVarElements($entityData)
    {
        $vars = [];
        foreach ($entityData[self::VAR_VALUES] as $varElement) {
            $varKey = $varElement[self::VAR_KEY];
            $varValue = $varElement[self::VAR_ENTITY] . self::VAR_ENTITY_FIELD_SEPARATOR . $varElement[self::VAR_FIELD];
            $vars[$varKey] = $varValue;
        }
        return $vars;
    }
}
