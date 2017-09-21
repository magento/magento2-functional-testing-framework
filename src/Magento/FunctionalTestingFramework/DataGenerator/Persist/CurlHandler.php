<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\DataGenerator\Persist;

use Magento\FunctionalTestingFramework\DataGenerator\Persist\Curl\AdminExecutor;
use Magento\FunctionalTestingFramework\DataGenerator\Persist\Curl\WebapiExecutor;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataDefinitionObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\DataDefinition;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\DataElement;
use Magento\FunctionalTestingFramework\DataGenerator\Util\DataObjectExtractor;
use Magento\FunctionalTestingFramework\Util\Protocol\CurlInterface;

/**
 * Class CurlHandler
 */
class CurlHandler
{
    const PRIMITIVE_TYPES = ['string', 'boolean', 'integer', 'double', 'array'];

    /**
     * Describes the operation for the executor ('create','update','delete')
     *
     * @var string
     */
    private $operation;

    /**
     * The entity object data being created, updated, or deleted.
     *
     * @var EntityDataObject $entityObject
     */
    private $entityObject;

    /**
     * The data definitions used to map the operation.
     *
     * @var DataDefinition $dataDefinition
     */
    private $dataDefinition;

    /**
     * The array of dependentEntities this class can be given. When finding linked entities, CurlHandler
     * uses this repository before looking for static data.
     *
     * @var array
     */
    private $dependentEntities = [];

    /**
     * The array of entity name and number of objects being created,
     * we don't need to track objects in update and delete operations.
     *
     * @var array
     */
    private static $entitySequences = [];

    /**
     * Store code in web api rest url.
     *
     * @var string
     */
    private $storeCode;

    /**
     * Operation to Curl method mapping.
     *
     * @var array
     */
    private static $curlMethodMapping = [
            'create' => CurlInterface::POST,
            'delete' => CurlInterface::DELETE,
            'update' => CurlInterface::PUT,
            'get' => CurlInterface::GET,
        ];

    /**
     * Persisted entity.
     *
     * @var EntityDataObject
     */
    private $persistedEntity;

    /**
     * Persisted dependent entities.
     *
     * @var array
     */
    private $persistedDependentEntities = [];

    /**
     * If it's a REST request.
     *
     * @var bool
     */
    private $isRestRequest;

    /**
     * ApiSubObject constructor.
     * @param string $operation
     * @param EntityDataObject $entityObject
     * @param array $dependentEntities
     * @param string $storeCode
     */
    public function __construct($operation, $entityObject, $dependentEntities = null, $storeCode = 'default')
    {
        $this->operation = $operation;
        $this->entityObject = $entityObject;
        $this->storeCode = $storeCode;
        $this->isRestRequest = true;
        if ($dependentEntities != null) {
            foreach ($dependentEntities as $entity) {
                $this->dependentEntities[$entity->getName()] = $entity;
            }
        }

        $this->dataDefinition = DataDefinitionObjectHandler::getInstance()->getDataDefinition(
            $this->operation,
            $this->entityObject->getType()
        );
    }

    /**
     * Executes an api request based on parameters given by constructor.
     *
     * @return string | null
     */
    public function executeRequest()
    {
        $apiUrl = $this->dataDefinition->getApiUrl($this->storeCode);

        $matchedParams = [];
        preg_match_all("/[{](.+?)[}]/", $apiUrl, $matchedParams);

        if (!empty($matchedParams)) {
            foreach ($matchedParams[0] as $paramKey => $paramValue) {
                $param = $this->entityObject->getDataByName(
                    $matchedParams[1][$paramKey],
                    EntityDataObject::CEST_UNIQUE_VALUE
                );
                $apiUrl = str_replace($paramValue, $param, $apiUrl);
            }
        }

        $executor = null;
        $successRegex = null;
        $returnRegex = null;
        $headers = $this->dataDefinition->getHeaders();
        $params = $this->convertDataArray($this->entityObject, $this->dataDefinition->getMetaData());

        $authorization = $this->dataDefinition->getAuth();
        switch ($authorization) {
            case 'adminOauth':
                $executor = new WebapiExecutor($this->storeCode);
                $executor->write($apiUrl, $params, self::$curlMethodMapping[$this->operation], $headers);
                break;
            case 'adminFormkey':
                $this->isRestRequest = false;
                $executor = new AdminExecutor();
                $executor->write($apiUrl, $params, self::$curlMethodMapping[$this->operation], $headers);
                $successRegex = $this->dataDefinition->getSuccessRegex();
                $returnRegex = $this->dataDefinition->getReturnRegex();
                break;
            case 'customFromkey':
                $this->isRestRequest = false;
                // TODO: add frontend request executor.
                break;
        }
        $response = $executor->read($successRegex, $returnRegex);
        $executor->close();
        return $response;
    }

    /**
     * If the request is a Rest.
     *
     * @return bool
     */
    public function isRestRequest()
    {
        return $this->isRestRequest;
    }

    /**
     * This function returns an array which is structurally equal to the data which is needed by the magento web api,
     * magento backend / frontend requests for entity creation. The function retrieves an array describing the entity's
     * metadata and traverses any dependencies recursively forming an array which represents the data structure for the
     * request of the desired entity type.
     *
     * @param EntityDataObject $entityObject
     * @param array $dataArrayMetadata
     * @return array
     */
    public function convertDataArray($entityObject, $dataArrayMetadata)
    {
        $dataArray = [];
        self::incrementSequence($entityObject->getName());

        foreach ($dataArrayMetadata as $dataElement) {
            if ($dataElement->getType() == DataObjectExtractor::MATA_DATA_OBJECT_NAME) {
                $dataArray[$dataElement->getValue()] =
                    $this->convertDataArray($entityObject, $dataElement->getNestedMetadata());
            }

            $dataElementType = $dataElement->getValue();

            if (in_array($dataElementType, CurlHandler::PRIMITIVE_TYPES)) {
                $elementData = $entityObject->getDataByName(
                    $dataElement->getKey(),
                    EntityDataObject::CEST_UNIQUE_VALUE
                );

                if (array_key_exists($dataElement->getKey(), $entityObject->getUniquenessData())) {
                    $uniqueData = $entityObject->getUniquenessDataByName($dataElement->getKey());
                    if ($uniqueData === 'suffix') {
                        $elementData .= (string)self::getSequence($entityObject->getName());
                    } else {
                        $elementData = (string)self::getSequence($entityObject->getName()) . $elementData;
                    }
                }

                $dataArray[$dataElement->getKey()] = $this->castValue($dataElementType, $elementData);
            } else {
                $entityNamesOfType = $entityObject->getLinkedEntitiesOfType($dataElementType);

                foreach ($entityNamesOfType as $entityName) {
                    $dataSubArray = $this->resolveNonPrimitiveElement($entityName, $dataElement);

                    if ($dataElement->getType() == 'array') {
                        $dataArray[$dataElement->getKey()][] = $dataSubArray;
                    } else {
                        $dataArray[$dataElement->getKey()] = $dataSubArray;
                    }
                }
            }
        }

        return $dataArray;
    }

    /**
     * Resolves dataObjects and pre-defined metadata (in other operation.xml file) referenced by the metadata.
     *
     * @param string $entityName
     * @param DataElement $dataElement
     * @return array
     */
    private function resolveNonPrimitiveElement($entityName, $dataElement)
    {
        $linkedEntityObj = $this->resolveLinkedEntityObject($entityName);

        if (!empty($dataElement->getNestedDataElement($dataElement->getValue()))
            && $dataElement->getType() == 'array') {
            $dataSubArray = $this->convertDataArray(
                $linkedEntityObj,
                [$dataElement->getNestedDataElement($dataElement->getValue())]
            );

            return $dataSubArray[$dataElement->getValue()];
        }

        $metaData = DataDefinitionObjectHandler::getInstance()->getDataDefinition(
            $this->operation,
            $linkedEntityObj->getType()
        )->getMetaData();

        return $this->convertDataArray($linkedEntityObj, $metaData);
    }

    /**
     * Method to wrap entity resolution, checks locally defined dependent entities first
     *
     * @param string $entityName
     * @return EntityDataObject
     */
    private function resolveLinkedEntityObject($entityName)
    {
        // check our dependent entity list to see if we have this defined
        if (array_key_exists($entityName, $this->dependentEntities)) {
            return $this->dependentEntities[$entityName];
        }

        return DataObjectHandler::getInstance()->getObject($entityName);
    }

    /**
     * Increment an entity's sequence number by 1.
     *
     * @param string $entityName
     * @return void
     */
    private static function incrementSequence($entityName)
    {
        if (array_key_exists($entityName, self::$entitySequences)) {
            self::$entitySequences[$entityName]++;
        } else {
            self::$entitySequences[$entityName] = 1;
        }
    }

    /**
     * Get the current sequence number for an entity.
     *
     * @param string $entityName
     * @return int
     */
    private static function getSequence($entityName)
    {
        if (array_key_exists($entityName, self::$entitySequences)) {
            return self::$entitySequences[$entityName];
        }
        return 0;
    }

    // @codingStandardsIgnoreStart
    /**
     * This function takes a string value and its corresponding type and returns the string cast
     * into its the type passed.
     *
     * @param string $type
     * @param string $value
     * @return mixed
     */
    private function castValue($type, $value)
    {
        $newVal = $value;

        switch ($type) {
            case 'string':
                break;
            case 'integer':
                $newVal = (integer)$value;
                break;
            case 'boolean':
                $newVal = (boolean)$value;
                break;
            case 'double':
                $newVal = (double)$value;
                break;
        }

        return $newVal;
    }
    // @codingStandardsIgnoreEnd
}
