<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\DataGenerator\Objects;

use Magento\AcceptanceTestFramework\DataGenerator\DataGeneratorConstants;
use Magento\AcceptanceTestFramework\DataGenerator\Managers\EntityDataManager;

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
     * Array of Data Name to Data Value.
     *
     * @var array
     */
    private $data = [];

    /**
     * EntityDataObject constructor.
     * @param string $entityName
     * @param string $entityType
     * @param array $data
     * @param array $linkedEntities
     */
    public function __construct($entityName, $entityType, array $data, array $linkedEntities)
    {
        $this->name = $entityName;
        $this->type = $entityType;
        $this->data = $data;
        $this->linkedEntities = $linkedEntities;
    }

    /**
     * Returns array of linked entities.
     *
     * @return array
     */
    public function getLinkedEntities(): array
    {
        return $this->linkedEntities;
    }

    /**
     * Returns entity name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns entity type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * This function retrieves data from an entity defined in xml.
     *
     * @param string $dataName
     * @return string|null
     */
    public function getDataByName(string $dataName)
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
    public function getLinkedEntitiesOfType(string $fieldType): array
    {
        $groupedArray = [];

        foreach ($this->linkedEntities as $entityName => $entityType) {
            if ($entityType == $fieldType) {
                $groupedArray[] = $entityName;
            }
        }

        return $groupedArray;
    }
}
