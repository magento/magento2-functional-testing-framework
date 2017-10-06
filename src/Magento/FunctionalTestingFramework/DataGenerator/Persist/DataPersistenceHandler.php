<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Persist;

use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;

/**
 * Class DataPersistenceHandler
 */
class DataPersistenceHandler
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
    private $dependentObjects;

    /**
     * Store code in web api rest url.
     *
     * @var string
     */
    private $storeCode;

    /**
     * DataPersistenceHandler constructor.
     *
     * @param EntityDataObject $entityObject
     * @param array $dependentObjects
     */
    public function __construct($entityObject, $dependentObjects = [])
    {
        $this->entityObject = clone $entityObject;
        $this->storeCode = 'default';

        foreach ($dependentObjects as $dependentObject) {
            $this->dependentObjects[] = $dependentObject->getCreatedObject();
        }
    }

    /**
     * Function which executes a create request based on specific operation metadata
     *
     * @param string $storeCode
     * @return void
     */
    public function createEntity($storeCode = null)
    {
        if (!empty($storeCode)) {
            $this->storeCode = $storeCode;
        }
        $curlHandler = new CurlHandler('create', $this->entityObject, $this->storeCode);
        $result = $curlHandler->executeRequest($this->dependentObjects);
        $this->setCreatedEntity(
            $result,
            $curlHandler->getRequestDataArray(),
            $curlHandler->isContentTypeJson()
        );
    }

    /**
     * Function which executes a delete request based on specific operation metadata
     *
     * @param string $storeCode
     * @return void
     */
    public function deleteEntity($storeCode = null)
    {
        if (!empty($storeCode)) {
            $this->storeCode = $storeCode;
        }
        $curlHandler = new CurlHandler('delete', $this->createdObject, $this->storeCode);
        $result = $curlHandler->executeRequest($this->dependentObjects);
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

    /**
     * Save created entity.
     *
     * @param string|array $response
     * @param array $requestDataArray
     * @param bool $isJson
     * @return void
     */
    private function setCreatedEntity($response, $requestDataArray, $isJson)
    {
        if ($isJson) {
            $persistedData = array_merge($requestDataArray, json_decode($response, true));
        } else {
            $persistedData = array_merge(
                $this->convertToFlatArray($requestDataArray),
                ['return' => $response]
            );
        }

        $this->createdObject = new EntityDataObject(
            $this->entityObject->getName(),
            $this->entityObject->getType(),
            $persistedData,
            null,
            null
        );
    }

    /**
     * Convert an multi-dimensional array to flat array.
     *
     * @param array $arrayIn
     * @param string $rootKey
     * @return array
     */
    private function convertToFlatArray($arrayIn, $rootKey = '')
    {
        $arrayOut = [];
        foreach ($arrayIn as $key => $value) {
            if (is_array($value)) {
                if (!empty($rootKey)) {
                    $newRootKey = $rootKey . '[' . $key . ']';
                } else {
                    $newRootKey = $key;
                }
                $arrayOut = array_merge($arrayOut, $this->convertToFlatArray($value, $newRootKey));
            } elseif (!empty($rootKey)) {
                $arrayOut[$rootKey . '[' . $key . ']'] = $value;
            } else {
                $arrayOut[$key] = $value;
            }
        }
        return $arrayOut;
    }
}
