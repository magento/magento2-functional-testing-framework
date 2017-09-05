<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AcceptanceTestFramework\DataGenerator\Api;

use Magento\AcceptanceTestFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\AcceptanceTestFramework\DataGenerator\Handlers\JsonDefinitionObjectHandler;
use Magento\AcceptanceTestFramework\DataGenerator\Objects\EntityDataObject;
use Magento\AcceptanceTestFramework\DataGenerator\Objects\JsonDefinition;
use Magento\AcceptanceTestFramework\DataGenerator\Objects\JsonElement;
use Magento\AcceptanceTestFramework\DataGenerator\Util\JsonObjectExtractor;
use Magento\AcceptanceTestFramework\Util\ApiClientUtil;

/**
 * Class ApiExecutor
 */
class ApiExecutor
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
     * The json definitions used to map the operation.
     *
     * @var JsonDefinition $jsonDefinition
     */
    private $jsonDefinition;

    /**
     * The array of dependentEntities this class can be given. When finding linked entities, APIExecutor
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
     * ApiSubObject constructor.
     * @param string $operation
     * @param EntityDataObject $entityObject
     * @param array $dependentEntities
     */
    public function __construct($operation, $entityObject, $dependentEntities = null)
    {
        $this->operation = $operation;
        $this->entityObject = $entityObject;
        if ($dependentEntities != null) {
            foreach ($dependentEntities as $entity) {
                $this->dependentEntities[$entity->getName()] = $entity;
            }
        }

        $this->jsonDefinition = JsonDefinitionObjectHandler::getInstance()->getJsonDefinition(
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
        $apiClientUrl = $this->jsonDefinition->getApiUrl();

        $matchedParams = [];
        preg_match_all("/[{](.+?)[}]/", $apiClientUrl, $matchedParams);

        if (!empty($matchedParams)) {
            foreach ($matchedParams[0] as $paramKey => $paramValue) {
                $param = $this->entityObject->getDataByName(
                    $matchedParams[1][$paramKey],
                    EntityDataObject::CEST_UNIQUE_VALUE
                );
                $apiClientUrl = str_replace($paramValue, $param, $apiClientUrl);
            }
        }

        $authorization = $this->jsonDefinition->getAuth();
        $headers = $this->jsonDefinition->getHeaders();

        if ($authorization) {
            $headers[] = $this->getAuthorizationHeader($authorization);
        }

        $jsonBody = $this->getEncodedJsonString();

        $apiClientUtil = new ApiClientUtil(
            $apiClientUrl,
            $headers,
            $this->jsonDefinition->getApiMethod(),
            empty($jsonBody) ? null : $jsonBody
        );

        return $apiClientUtil->submit();
    }

    /**
     * Returns the authorization token needed for some requests via REST call.
     *
     * @param string $authUrl
     * @return string
     */
    private function getAuthorizationHeader($authUrl)
    {
        $headers = ['Content-Type: application/json'];
        $authCreds = [
            'username' => getenv('MAGENTO_ADMIN_USERNAME'),
            'password' => getenv('MAGENTO_ADMIN_PASSWORD')
        ];

        $apiClientUtil = new ApiClientUtil($authUrl, $headers, 'POST', json_encode($authCreds));
        $token = $apiClientUtil->submit();
        $authHeader = 'Authorization: Bearer ' . str_replace('"', "", $token);

        return $authHeader;
    }

    /**
     * This function returns an array which is structurally equal to the json which is needed by the web api for
     * entity creation. The function retrieves an array describing the json metadata and traverses any dependencies
     * recursively forming an array which represents the json structure for the api of the desired type.
     *
     * @param EntityDataObject $entityObject
     * @param array $jsonArrayMetadata
     * @return array
     */
    private function convertJsonArray($entityObject, $jsonArrayMetadata)
    {
        $jsonArray = [];
        self::incrementSequence($entityObject->getName());

        foreach ($jsonArrayMetadata as $jsonElement) {
            if ($jsonElement->getType() == JsonObjectExtractor::JSON_OBJECT_OBJ_NAME) {
                $jsonArray[$jsonElement->getValue()] =
                    $this->convertJsonArray($entityObject, $jsonElement->getNestedMetadata());
            }

            $jsonElementType = $jsonElement->getValue();

            if (in_array($jsonElementType, ApiExecutor::PRIMITIVE_TYPES)) {
                $elementData = $entityObject->getDataByName(
                    $jsonElement->getKey(),
                    EntityDataObject::CEST_UNIQUE_VALUE
                );

                if (array_key_exists($jsonElement->getKey(), $entityObject->getUniquenessData())) {
                    $uniqueData = $entityObject->getUniquenessDataByName($jsonElement->getKey());
                    if ($uniqueData === 'suffix') {
                        $elementData .= (string)self::getSequence($entityObject->getName());
                    } else {
                        $elementData = (string)self::getSequence($entityObject->getName())
                            . $elementData;
                    }
                }

                $jsonArray[$jsonElement->getKey()] = $this->castValue($jsonElementType, $elementData);
            } else {
                $entityNamesOfType = $entityObject->getLinkedEntitiesOfType($jsonElementType);

                foreach ($entityNamesOfType as $entityName) {
                    $jsonDataSubArray = $this->resolveNonPrimitiveElement($entityName, $jsonElement);

                    if ($jsonElement->getType() == 'array') {
                        $jsonArray[$jsonElement->getKey()][] = $jsonDataSubArray;
                    } else {
                        $jsonArray[$jsonElement->getKey()] = $jsonDataSubArray;
                    }
                }
            }
        }

        return $jsonArray;
    }

    /**
     * Resolves JsonObjects and pre-defined metadata (in other operation.xml file) referenced by the json metadata
     *
     * @param string $entityName
     * @param JsonElement $jsonElement
     * @return array
     */
    private function resolveNonPrimitiveElement($entityName, $jsonElement)
    {
        $linkedEntityObj = $this->resolveLinkedEntityObject($entityName);

        if (!empty($jsonElement->getNestedJsonElement($jsonElement->getValue()))
            && $jsonElement->getType() == 'array') {
            $jsonSubArray = $this->convertJsonArray(
                $linkedEntityObj,
                [$jsonElement->getNestedJsonElement($jsonElement->getValue())]
            );

            return $jsonSubArray[$jsonElement->getValue()];
        }

        $jsonMetadata = JsonDefinitionObjectHandler::getInstance()->getJsonDefinition(
            $this->operation,
            $linkedEntityObj->getType()
        )->getJsonMetadata();

        return $this->convertJsonArray($linkedEntityObj, $jsonMetadata);
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
     * This function retrieves an array representative of json body for a request and returns it encoded as a string.
     *
     * @return string
     */
    public function getEncodedJsonString()
    {
        $jsonMetadataArray = $this->convertJsonArray($this->entityObject, $this->jsonDefinition->getJsonMetadata());

        return json_encode($jsonMetadataArray, JSON_PRETTY_PRINT);
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
