<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\DataGenerator\Api;

use Magento\AcceptanceTestFramework\Config\Data;
use Magento\AcceptanceTestFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\AcceptanceTestFramework\DataGenerator\Objects\EntityDataObject;
use Magento\AcceptanceTestFramework\Test\Handlers\CestObjectHandler;
use Magento\AcceptanceTestFramework\Util\TestGenerator;

class EntityApiHandler
{
    /**
     * Entity object data to use for create, delete, or update.
     *
     * @var EntityDataObject $entityObject
     */
    private $entityObject;

    /**
     * Resulting created object from create or update.
     *
     * @var EntityDataObject $createdObject
     */
    private $createdObject;

    /**
     * Array of dependent entities, handed to ApiExecutor when entity is created.
     * @var array|null
     */
    private $dependentObjects = [];

    /**
     * ApiPersistenceHandler constructor.
     * @param EntityDataObject $entityObject
     * @param array $dependentObjects
     */
    public function __construct($entityObject, $dependentObjects = null)
    {
        $this->entityObject = clone $entityObject;
        $this->dependentObjects = $dependentObjects;
    }

    /**
     * Function which executes a create request based on specific operation metadata
     * @return void
     */
    public function createEntity()
    {
        $apiExecutor = new ApiExecutor('create', $this->entityObject, $this->dependentObjects);
        $result = $apiExecutor->executeRequest();

        $this->createdObject = new EntityDataObject(
            $this->entityObject->getName(),
            $this->entityObject->getType(),
            json_decode($result, true),
            null,
            null // No uniqueness data is needed to be further processed.
        );
    }

    /**
     * Function which executes a delete request based on specific operation metadata
     *
     * @return string | false
     */
    public function deleteEntity()
    {
        $apiExecutor = new ApiExecutor('delete', $this->createdObject);
        $result = $apiExecutor->executeRequest();

        return $result;
    }

    /**
     * Returns the createdDataObject, instantiated when the entity is created via API.
     * @return EntityDataObject
     */
    public function getCreatedObject()
    {
        return $this->createdObject;
    }

    /**
     * Returns a specific data value based on the CreatedObject's definition.
     * @param string $dataName
     * @return string
     */
    public function getCreatedDataByName($dataName)
    {
        $data = $this->createdObject->getDataByName($dataName, EntityDataObject::NO_UNIQUE_PROCESS);
        if (empty($data)) {
            $data = $this->entityObject->getDataByName($dataName, EntityDataObject::CEST_UNIQUE_VALUE);
        }
        return $data;
    }

    // TODO add update function
    /* public function updateEntity()
    {

    }*/
}
