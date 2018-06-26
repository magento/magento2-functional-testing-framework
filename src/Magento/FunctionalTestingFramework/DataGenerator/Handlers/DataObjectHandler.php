<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers;

use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\DataProfileSchemaParser;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\DataGenerator\Util\DataExtensionUtil;

class DataObjectHandler implements ObjectHandlerInterface
{
    const _ENTITY = 'entity';
    const _NAME = 'name';
    const _TYPE = 'type';
    const _EXTENDS = 'extends';
    const _DATA = 'data';
    const _KEY = 'key';
    const _VALUE = 'value';
    const _UNIQUE = 'unique';
    const _PREFIX = 'prefix';
    const _SUFFIX = 'suffix';
    const _ARRAY = 'array';
    const _ITEM = 'item';
    const _VAR = 'var';
    const _ENTITY_TYPE = 'entityType';
    const _ENTITY_KEY = 'entityKey';
    const _SEPARATOR = '->';
    const _REQUIRED_ENTITY = 'requiredEntity';
    const DATA_NAME_ERROR_MSG = "Entity names cannot contain non alphanumeric characters.\tData='%s'";

    /**
     * The singleton instance of this class
     *
     * @var DataObjectHandler $INSTANCE
     */
    private static $INSTANCE;

    /**
     * A collection of entity data objects that were seen in XML files and the .env file
     *
     * @var EntityDataObject[] $entityDataObjects
     */
    private $entityDataObjects = [];

    /**
     * Instance of DataExtensionUtil class
     *
     * @var DataExtensionUtil
     */
    private $extendUtil;

    /**
     * Constructor
     */
    private function __construct()
    {
        $parser = ObjectManagerFactory::getObjectManager()->create(DataProfileSchemaParser::class);
        $parserOutput = $parser->readDataProfiles();
        if (!$parserOutput) {
            return;
        }
        $this->entityDataObjects = $this->processParserOutput($parserOutput);
        $this->extendUtil = new DataExtensionUtil();
    }

    /**
     * Return the singleton instance of this class. Initialize it if needed.
     *
     * @return DataObjectHandler
     * @throws \Exception
     */
    public static function getInstance()
    {
        if (!self::$INSTANCE) {
            self::$INSTANCE = new DataObjectHandler();
        }
        return self::$INSTANCE;
    }

    /**
     * Get an EntityDataObject by name
     *
     * @param string $name The name of the entity you want. Comes from the name attribute in data xml.
     * @return EntityDataObject | null
     */
    public function getObject($name)
    {
        if (array_key_exists($name, $this->entityDataObjects)) {
            return $this->extendDataObject($this->entityDataObjects[$name]);
        }

        return null;
    }

    /**
     * Get all EntityDataObjects
     *
     * @return EntityDataObject[]
     */
    public function getAllObjects()
    {
        foreach ($this->entityDataObjects as $entityName => $entityObject) {
            $this->entityDataObjects[$entityName] = $this->extendDataObject($entityObject);
        }
        return $this->entityDataObjects;
    }

    /**
     * Convert the parser output into a collection of EntityDataObjects
     *
     * @param string[] $parserOutput Primitive array output from the Magento parser.
     * @return EntityDataObject[]
     * @throws XmlException
     */
    private function processParserOutput($parserOutput)
    {
        $entityDataObjects = [];
        $rawEntities = $parserOutput[self::_ENTITY];

        foreach ($rawEntities as $name => $rawEntity) {
            if (preg_match('/[^a-zA-Z0-9_]/', $name)) {
                throw new XmlException(sprintf(self::DATA_NAME_ERROR_MSG, $name));
            }

            $type = $rawEntity[self::_TYPE] ?? null;
            $data = [];
            $linkedEntities = [];
            $uniquenessData = [];
            $vars = [];
            $parentEntity = null;

            if (array_key_exists(self::_DATA, $rawEntity)) {
                $data = $this->processDataElements($rawEntity);
                $uniquenessData = $this->processUniquenessData($rawEntity);
            }

            if (array_key_exists(self::_REQUIRED_ENTITY, $rawEntity)) {
                $linkedEntities = $this->processLinkedEntities($rawEntity);
            }

            if (array_key_exists(self::_ARRAY, $rawEntity)) {
                $arrays = $rawEntity[self::_ARRAY];
                foreach ($arrays as $array) {
                    $key = strtolower($array[self::_KEY]);
                    $data[$key] = $this->processArray($array[self::_ITEM], $data, $key);
                }
            }

            if (array_key_exists(self::_VAR, $rawEntity)) {
                $vars = $this->processVarElements($rawEntity);
            }

            if (array_key_exists(self::_EXTENDS, $rawEntity)) {
                $parentEntity = $rawEntity[self::_EXTENDS];
            }

            $entityDataObject = new EntityDataObject(
                $name,
                $type,
                $data,
                $linkedEntities,
                $uniquenessData,
                $vars,
                $parentEntity
            );

            $entityDataObjects[$entityDataObject->getName()] = $entityDataObject;
        }

        return $entityDataObjects;
    }

    /**
     * Takes an array of items and a top level entity data array and merges in elements from parsed entity definitions.
     *
     * @param array  $arrayItems
     * @param array  $data
     * @param string $key
     * @return array
     */
    private function processArray($arrayItems, $data, $key)
    {
        $items = [];
        foreach ($arrayItems as $item) {
            $items[] = $item[self::_VALUE];
        }

        return array_merge($items, $data[$key] ?? []);
    }

    /**
     * Parses <data> elements in an entity, and returns them as an array of "lowerKey"=>value.
     *
     * @param string[] $entityData
     * @return string[]
     */
    private function processDataElements($entityData)
    {
        $dataValues = [];
        foreach ($entityData[self::_DATA] as $dataElement) {
            $dataElementKey = strtolower($dataElement[self::_KEY]);
            $dataElementValue = $dataElement[self::_VALUE] ?? "";
            $dataValues[$dataElementKey] = $dataElementValue;
        }
        return $dataValues;
    }

    /**
     * Parses through <data> elements in an entity to return an array of "DataKey" => "UniquenessAttribute"
     *
     * @param string[] $entityData
     * @return string[]
     */
    private function processUniquenessData($entityData)
    {
        $uniquenessValues = [];
        foreach ($entityData[self::_DATA] as $dataElement) {
            if (array_key_exists(self::_UNIQUE, $dataElement)) {
                $dataElementKey = strtolower($dataElement[self::_KEY]);
                $uniquenessValues[$dataElementKey] = $dataElement[self::_UNIQUE];
            }
        }
        return $uniquenessValues;
    }

    /**
     * Parses <requiredEntity> elements given entity, and returns them as an array of "EntityValue"=>"EntityType"
     *
     * @param string[] $entityData
     * @return string[]
     */
    private function processLinkedEntities($entityData)
    {
        $linkedEntities = [];
        foreach ($entityData[self::_REQUIRED_ENTITY] as $linkedEntity) {
            $linkedEntityName = $linkedEntity[self::_VALUE];
            $linkedEntityType = $linkedEntity[self::_TYPE];

            $linkedEntities[$linkedEntityName] = $linkedEntityType;
        }
        return $linkedEntities;
    }

    /**
     * Parses <var> elements in given entity, and returns them as an array of "Key"=> entityType -> entityKey
     *
     * @param string[] $entityData
     * @return string[]
     */
    private function processVarElements($entityData)
    {
        $vars = [];
        foreach ($entityData[self::_VAR] as $varElement) {
            $varKey = $varElement[self::_KEY];
            $varValue = $varElement[self::_ENTITY_TYPE] . self::_SEPARATOR . $varElement[self::_ENTITY_KEY];
            $vars[$varKey] = $varValue;
        }
        return $vars;
    }

    /**
     * This method checks if the data object is extended and creates a new data object accordingly
     *
     * @param EntityDataObject $dataObject
     * @return EntityDataObject
     */
    private function extendDataObject($dataObject)
    {
        if ($dataObject->getParentName() != null) {
            return $this->extendUtil->extendEntity($dataObject);
        }
        return $dataObject;
    }
}
