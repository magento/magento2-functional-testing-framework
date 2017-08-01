<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Api;

use Magento\AcceptanceTestFramework\DataGenerator\Objects\EntityDataObject;

class EntityApiHandler
{
    private $entityObject;
    private $createdObject;

    /**
     * ApiPersistenceHandler constructor.
     * @param EntityDataObject $entityObject
     */
    public function __construct($entityObject)
    {
        $this->entityObject = $entityObject;
    }

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
    }

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
