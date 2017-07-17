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


    public function __construct($entityName, $entityType, $data, $linkedEntities)
    {
        $this->name = $entityName;
        $this->type = $entityType;

        if ($data) {
            foreach ($data as $dataElement) {
                $dataElementKey = $dataElement[DataGeneratorConstants::DATA_ELEMENT_KEY];
                $dataElementValue = $dataElement[DataGeneratorConstants::DATA_ELEMENT_VALUE];

                $this->data[$dataElementKey] = $dataElementValue;
            }
            unset($dataElement);
        }

        if ($linkedEntities) {
            foreach ($linkedEntities as $linkedEntity) {
                $linkedEntityName = $linkedEntity[DataGeneratorConstants::REQUIRED_ENTITY_VALUE];
                $linkedEntityType = $linkedEntity[DataGeneratorConstants::REQUIRED_ENTITY_TYPE];

                $this->linkedEntities[$linkedEntityName] = $linkedEntityType;
            }
            unset($linkedEntity);
        }
    }

    public function hasLinkedEntity($entityName)
    {
        return array_key_exists($entityName, $this->linkedEntities);
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
     * This function retrieves data from an entity defined in xml. The data can be defined explicitly by the entity or
     * within any entity linked to or required by the entity. The function will evaluate in the following order:
     * 1) Data declared explicitly by the entity
     * 2) Data declared explicitly as a known linked entity
     * 3) Data declared by entities linked to linked entities
     * @param string $name
     * @param string $entityName
     * @return string
     */
    public function getDataByName($name, $entityName = null)
    {
        if ($entityName == null) {
            return $this->data[$name];
        } elseif ($this->hasLinkedEntity($entityName)) {
            $entityTypeManager = EntityDataManager::getDataManager($this->linkedEntities[$entityName]);
            return $entityTypeManager->getEntity($entityName)->getDataByName($name);
        } else {
            foreach ($this->linkedEntities as $linkedEntityName => $linkedEntityType) {
                $result = EntityDataManager::getDataManager($linkedEntityType)->getEntity($linkedEntityName)
                    ->getDataByName($name, $entityName);

                if ($result) {
                    return $result;
                }
            }
        }
    }

    public function persistEntity()
    {
        $this->persistDependencies();
        // TODO fetch json representation and api persistence mechanism
    }

    private function persistDependencies()
    {
        // TODO call method to fetch json representation and api persistence mechanism
    }
}
