<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Api;

use Magento\AcceptanceTestFramework\DataGenerator\Managers\DataManager;
use Magento\AcceptanceTestFramework\DataGenerator\Managers\JsonDefinitionManager;
use Magento\AcceptanceTestFramework\DataGenerator\Objects\EntityDataObject;
use Magento\AcceptanceTestFramework\Util\ApiClientUtil;

class ApiExecutor
{
    private $operation;
    private $entityObject;
    private $jsonDefinition;

    private $primitives = ['string', 'boolean', 'integer', 'double', 'array'];

    /**
     * ApiSubObject constructor.
     * @param string $operation
     * @param EntityDataObject $entityObject
     */
    public function __construct($operation, $entityObject)
    {
        $this->operation = $operation;
        $this->entityObject = $entityObject;

        $this->jsonDefinition = JsonDefinitionManager::getInstance()->getJsonDefinition(
            $this->operation,
            $this->entityObject->getType()
        );
    }

    public function executeRequest()
    {
        $apiClientUrl = $this->jsonDefinition->getApiUrl();

        $matchedParams = [];
        preg_match_all("/[{](.+?)[}]/", $apiClientUrl, $matchedParams);

        if (!empty($matchedParams)) {
            foreach ($matchedParams[0] as $paramKey => $paramValue) {
                $param = $this->entityObject->getDataByName($matchedParams[1][$paramKey]);
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
     * @param Array $jsonDefMetadata
     *
     * @return array
     */
    private function getJsonDataArray($entityObject, $jsonDefMetadata = null)
    {
        $jsonArrayMetadata = !$jsonDefMetadata ? JsonDefinitionManager::getInstance()->getJsonDefinition(
            $this->operation,
            $entityObject->getType()
        )->getJsonMetadata() : $jsonDefMetadata;

        $jsonArray = array();

        foreach ($jsonArrayMetadata as $jsonElement) {
            $jsonElementType = $jsonElement->getValue();

            if (in_array($jsonElementType, $this->primitives)) {
                $jsonArray[$jsonElement->getKey()] = $this->castValue(
                    $jsonElementType,
                    $entityObject->getDataByName($jsonElement->getKey())
                );
            } else {
                $entityNamesOfType = $entityObject->getLinkedEntitiesOfType($jsonElementType);

                foreach ($entityNamesOfType as $entityName) {
                    $linkedEntityObj = DataManager::getInstance()->getEntity($entityName);
                    $jsonDataSubArray = self::getJsonDataArray($linkedEntityObj);

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

    private function getEncodedJsonString()
    {
        $jsonArray = $this->getJsonDataArray($this->entityObject, $this->jsonDefinition->getJsonMetadata());

        return json_encode([$this->entityObject->getType() => $jsonArray], JSON_PRETTY_PRINT);
    }

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
}