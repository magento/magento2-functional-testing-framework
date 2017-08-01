<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Objects;

use Magento\AcceptanceTestFramework\DataGenerator\DataGeneratorConstants;
use Magento\AcceptanceTestFramework\DataGenerator\Managers\EntityDataManager;

class EntityDataObject
{
    private $name;
    private $type;
    private $linkedEntities = []; //array of required entity name to corresponding type
    private $data = []; //array of Data Name to Data Value

    /**
     * EntityDataObject constructor.
     * @param string $entityName
     * @param string $entityType
     * @param array $data
     * @param array $linkedEntities
     */
    public function __construct($entityName, $entityType, $data, $linkedEntities)
    {
        $this->name = $entityName;
        $this->type = $entityType;
        $this->data = $data;
        $this->linkedEntities = $linkedEntities;
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
        $groupedArray = array();

        foreach ($this->linkedEntities as $entityName => $entityType) {
            if ($entityType == $fieldType) {
                $groupedArray[] = $entityName;
            }
        }

        return $groupedArray;
    }
}
