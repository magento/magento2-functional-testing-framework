<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Api;

use Magento\AcceptanceTestFramework\DataGenerator\Objects\EntityDataObject;

class EntityApiHandler
{
    /**
     * Entity object data to use for create, delete, or update.
     * @var EntityDataObject $entityObject
     */
    private $entityObject;

    /**
     * Resulting created object from create or update.
     * @var EntityDataObject $createdObject
     */
    private $createdObject;

    /**
     * ApiPersistenceHandler constructor.
     * @constructor
     * @param EntityDataObject $entityObject
     */
    public function __construct($entityObject)
    {
        $this->entityObject = $entityObject;
    }

    /**
     * Function which executes a create request based on specific operation metadata
     * @return string | false
     */
    public function createEntity()
    {
        $apiExecutor = new ApiExecutor('create', $this->entityObject);
        $result = $apiExecutor->executeRequest();

        $this->createdObject = new EntityDataObject(
            '__created' . $this->entityObject->getName(),
            $this->entityObject->getType(),
            json_decode($result, true),
            null
        );

        return $result;
    }

    /**
     * Function which executes a delete request based on specific operation metadata
     * @return string | false
     */
    public function deleteEntity()
    {
        $apiExecutor = new ApiExecutor('delete', $this->createdObject);
        $result = $apiExecutor->executeRequest();

        return $result;
    }

    // TODO add update function
    /* public function updateEntity()
    {

    }*/
}
