<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Persist;

use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

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
     * @param array            $dependentObjects
     * @param array            $customFields
     */
    public function __construct($entityObject, $dependentObjects = [], $customFields = [])
    {
        // merge any custom fields into a new EntityDataObject for the persistence handler
        if (!empty($customFields)) {
            $this->entityObject = new EntityDataObject(
                $entityObject->getName(),
                $entityObject->getType(),
                array_merge($entityObject->getAllData(), $customFields),
                $entityObject->getLinkedEntities(),
                $this->stripCustomFieldsFromUniquenessData($entityObject->getUniquenessData(), $customFields),
                $entityObject->getVarReferences(),
                $entityObject->getParentName(),
                $entityObject->getFilename(),
                $entityObject->getDeprecated()
            );
        } else {
            $this->entityObject = clone $entityObject;
        }
        $this->storeCode = null;

        foreach ($dependentObjects as $dependentObject) {
            $this->dependentObjects[] = $dependentObject->getCreatedObject();
        }
    }

    /**
     * Function which executes a create request based on specific operation metadata
     *
     * @param string $storeCode
     * @return void
     * @throws TestFrameworkException
     */
    public function createEntity($storeCode = null)
    {
        if (!empty($storeCode)) {
            $this->storeCode = $storeCode;
        }
        $curlHandler = new CurlHandler('create', $this->entityObject, $this->storeCode);
        $result = $curlHandler->executeRequest($this->dependentObjects);
        $this->setCreatedObject(
            $result,
            null,
            $curlHandler->getRequestDataArray(),
            $curlHandler->isContentTypeJson()
        );
    }

    /**
     * Function which executes a put request based on specific operation metadata.
     *
     * @param string $updateDataName
     * @param array  $updateDependentObjects
     * @return void
     * @throws TestFrameworkException
     * @throws \Exception
     */
    public function updateEntity($updateDataName, $updateDependentObjects = [])
    {
        foreach ($updateDependentObjects as $dependentObject) {
            $this->dependentObjects[] = $dependentObject->getCreatedObject();
        }
        $updateEntityObject = DataObjectHandler::getInstance()->getObject($updateDataName);
        $curlHandler = new CurlHandler('update', $updateEntityObject, $this->storeCode);
        $result = $curlHandler->executeRequest(array_merge($this->dependentObjects, [$this->createdObject]));
        $this->setCreatedObject(
            $result,
            null,
            $curlHandler->getRequestDataArray(),
            $curlHandler->isContentTypeJson()
        );
    }

    /**
     * Function which executes a get request on specific operation metadata.
     *
     * @param integer|null $index
     * @param string       $storeCode
     * @return void
     * @throws TestFrameworkException
     */
    public function getEntity($index = null, $storeCode = null)
    {
        if (!empty($storeCode)) {
            $this->storeCode = $storeCode;
        }
        $curlHandler = new CurlHandler('get', $this->entityObject, $this->storeCode);
        $result = $curlHandler->executeRequest($this->dependentObjects);
        $this->setCreatedObject(
            $result,
            $index,
            $curlHandler->getRequestDataArray(),
            $curlHandler->isContentTypeJson()
        );
    }

    /**
     * Function which executes a delete request based on specific operation metadata
     *
     * @return void
     * @throws TestFrameworkException
     */
    public function deleteEntity()
    {
        $curlHandler = new CurlHandler('delete', $this->createdObject, $this->storeCode);
        $curlHandler->executeRequest($this->dependentObjects);
    }

    /**
     * Returns the created data object, instantiated when the entity is created via API.
     *
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
     * @throws TestFrameworkException
     */
    public function getCreatedDataByName($dataName)
    {
        return $this->createdObject->getDataByName($dataName, EntityDataObject::NO_UNIQUE_PROCESS);
    }

    /**
     * Save the created data object.
     *
     * @param string|array $response
     * @param integer|null $index
     * @param array        $requestDataArray
     * @param boolean      $isJson
     * @return void
     */
    private function setCreatedObject($response, $index, $requestDataArray, $isJson)
    {
        if ($isJson) {
            $responseData = json_decode($response, true);
            if (is_array($responseData) && (null !== $index)) {
                $responseData = $responseData[$index];
            }
            if (is_array($responseData)) {
                $persistedData = $this->convertToFlatArray(array_merge(
                    $requestDataArray,
                    $this->convertCustomAttributesArray($responseData)
                ));
            } else {
                $persistedData = $this->convertToFlatArray(array_merge($requestDataArray, ['return' => $responseData]));
            }
        } else {
            $persistedData = array_merge($this->convertToFlatArray($requestDataArray), ['return' => $response]);
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
     * @param array  $arrayIn
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

    /**
     * Convert custom_attributes array from
     * e.g.
     * 'custom_attributes' => [
     *      0 => [
     *          'attribute_code' => 'code1',
     *          'value' => 'value1',
     *      ],
     *      1 => [
     *          'attribute_code' => 'code2',
     *          'value' => 'value2',
     *      ],
     *  ]
     *
     * To
     *
     * 'custom_attributes' => [
     *      'code1' => 'value1',
     *      'code2' => 'value2',
     *  ]
     *
     * @param array $arrayIn
     * @return array
     */
    private function convertCustomAttributesArray($arrayIn)
    {
        $keys = ['custom_attributes'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $arrayIn)) {
                continue;
            }
            $arrayCopy = $arrayIn[$key];
            foreach ($arrayCopy as $index => $attributes) {
                $arrayIn[$key][$attributes['attribute_code']] = $attributes['value'];
            }
        }
        return $arrayIn;
    }

    /**
     * Function to strip out any overwritten custom field uniqueness data. Takes the uniqueness array and the
     * customFields from the user and unsets any intersections.
     *
     * @param array $uniquenessData
     * @param array $customFields
     * @return array
     */
    private function stripCustomFieldsFromUniquenessData($uniquenessData, $customFields)
    {
        $newUniquenessArray = $uniquenessData;
        $intersectingKeys = array_intersect_key($uniquenessData, $customFields);
        foreach ($intersectingKeys as $customFieldKey => $customFieldValue) {
            unset($newUniquenessArray[$customFieldKey]);
        }

        return $newUniquenessArray;
    }
}
