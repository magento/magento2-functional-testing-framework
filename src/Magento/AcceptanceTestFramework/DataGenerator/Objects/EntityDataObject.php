<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\DataGenerator\Objects;

/**
 * Class EntityDataObject
 */
class EntityDataObject
{
    /**
     * Name of the entity
     *
     * @var string
     */
    private $name;

    /**
     * Type of the entity
     *
     * @var string
     */
    private $type;

    /**
     * An array of required entity name to corresponding type
     *
     * @var array
     */
    private $linkedEntities = [];

    /**
     * An array of Data Name to Data Value
     *
     * @var array
     */
    private $data = [];

    /**
     * Array of data name and its uniqueness attribute value.
     *
     * @var array
     */
    private $uniquenessData = [];

    /**
     * EntityDataObject constructor.
     * @param string $entityName
     * @param string $entityType
     * @param array $data
     * @param array $linkedEntities
     * @param array $uniquenessData
     */
    public function __construct($entityName, $entityType, $data, $linkedEntities, $uniquenessData = null)
    {
        $this->name = $entityName;
        $this->type = $entityType;
        $this->data = $data;
        $this->linkedEntities = $linkedEntities;
        if ($uniquenessData) {
            $this->uniquenessData = $uniquenessData;
        }
    }

    /**
     * Getter for linked entity names
     *
     * @return array
     */
    public function getLinkedEntities()
    {
        return $this->linkedEntities;
    }

    /**
     * Getter for entity name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Getter for entity type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Getter for Entity's data.
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * This function retrieves data from an entity defined in xml.
     *
     * @param string $dataName
     * @return string|null
     */
    public function getDataByName($dataName)
    {
        $name = strtolower($dataName);

        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }

    /**
     * This function takes an array of entityTypes indexed by name and a string that represents the type of interest.
     * The function returns an array of entityNames relevant to the specified type.
     *
     * @param string $fieldType
     * @return array
     */
    public function getLinkedEntitiesOfType($fieldType)
    {
        $groupedArray = [];

        foreach ($this->linkedEntities as $entityName => $entityType) {
            if ($entityType == $fieldType) {
                $groupedArray[] = $entityName;
            }
        }

        return $groupedArray;
    }

    /**
     * This function retrieves uniqueness data by its name.
     *
     * @param string $dataName
     * @return string|null
     */
    public function getUniquenessDataByName($dataName)
    {
        $name = strtolower($dataName);

        if (array_key_exists($name, $this->uniquenessData)) {
            return $this->uniquenessData[$name];
        }

        return null;
    }
}
