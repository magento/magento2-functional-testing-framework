<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Objects;

use Magento\AcceptanceTestFramework\DataGenerator\DataGeneratorConstants;
use Magento\AcceptanceTestFramework\DataGenerator\Managers\DataManager;

class EntityDataObject
{
    /**
     * Entity name.
     *
     * @var string
     */
    private $name;

    /**
     * Entity type.
     *
     * @var string
     */
    private $type;

    /**
     * Array of required entity name to corresponding type.
     *
     * @var array
     */
    private $linkedEntities = [];

    /**
     * Array of data name to data value.
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

    public function getLinkedEntities()
    {
        return $this->linkedEntities;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * This function retrieves data from an entity defined in xml.
     *
     * @param string $dataName
     * @return string
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
