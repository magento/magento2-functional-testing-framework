<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\DataGenerator\Api;

use Magento\AcceptanceTestFramework\DataGenerator\Objects\EntityDataObject;

class EntityApiHandler
{
    /**
     * Original data object.
     *
     * @var EntityDataObject
     */
    private $entityObject;

    /**
     * New data object.
     *
     * @var EntityDataObject
     */
    private $createdObject;

    /**
     * ApiPersistenceHandler constructor.
     * @param EntityDataObject $entityObject
     */
    public function __construct($entityObject)
    {
        $this->entityObject = $entityObject;
    }

    /**
     * Method responsible for creating entity.
     *
     * @return void
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
    }

    /**
     * Method responsible for deleting entity.
     *
     * @return bool|string
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
