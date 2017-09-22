<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Persist;

use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;

/**
 * Class DataPersistHandler
 */
class DataPersistHandler
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
     * Array of dependent entities, handed to CurlHandler when entity is created.
     * @var array|null
     */
    private $dependentObjects = [];

    /**
     * Store code in web api rest url.
     *
     * @var string
     */
    private $storeCode;

    /**
     * DataPersistHandler constructor.
     *
     * @param EntityDataObject $entityObject
     * @param array $dependentObjects
     */
    public function __construct($entityObject, $dependentObjects = null)
    {
        $this->entityObject = clone $entityObject;
        $this->dependentObjects = $dependentObjects;
        $this->storeCode = 'default';
    }

    /**
     * Function which executes a create request based on specific operation metadata
     *
     * @param string $storeCode
     * @return void
     */
    public function createEntity($storeCode = null)
    {
        if (isset($storeCode)) {
            $this->storeCode = $storeCode;
        }
        $curlHandler = new CurlHandler('create', $this->entityObject, $this->dependentObjects, $this->storeCode);
        $result = $curlHandler->executeRequest();

        $persistedDataArray = $curlHandler->getPersistedDataArray();
        if ($curlHandler->isWebApiRequest()) {
            $persistedDataArray = array_merge($persistedDataArray, json_decode($result, true));
        } else {
            $persistedDataArray = array_merge($persistedDataArray, ['return' => $result]);
        }

        $this->createdObject = new EntityDataObject(
            $this->entityObject->getName(),
            $this->entityObject->getType(),
            $persistedDataArray,
            null,
            null
        );
    }

    /**
     * Function which executes a delete request based on specific operation metadata
     *
     * @param string $storeCode
     * @return string | false
     */
    public function deleteEntity($storeCode = null)
    {
        if (isset($storeCode)) {
            $this->storeCode = $storeCode;
        }
        $curlHandler = new CurlHandler('delete', $this->createdObject, null, $this->storeCode);
        $result = $curlHandler->executeRequest();

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
        return $this->createdObject->getDataByName($dataName, EntityDataObject::NO_UNIQUE_PROCESS);
    }

    // TODO add update function
    /* public function updateEntity()
    {

    }*/
}
