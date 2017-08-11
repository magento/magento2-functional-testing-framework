<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Managers;

use Magento\AcceptanceTestFramework\DataGenerator\Objects\JsonDefinition;
use Magento\AcceptanceTestFramework\DataGenerator\Objects\JsonElement;
use Magento\AcceptanceTestFramework\DataGenerator\Parsers\MetadataParser;
use Magento\AcceptanceTestFramework\ObjectManagerFactory;

class JsonDefinitionManager
{
    private static $jsonDefinitionManager;
    private $jsonDefinitions;

    const ENTITY_OPERATION_ROOT_TAG = 'operation';
    const ENTITY_OPERATION_TYPE = 'type';
    const ENTITY_OPERATION_DATA_TYPE = 'dataType';
    const ENTITY_OPERATION_URL = 'url';
    const ENTITY_OPERATION_METHOD = 'method';
    const ENTITY_OPERATION_AUTH = 'auth';
    const ENTITY_OPERATION_HEADER = 'header';
    const ENTITY_OPERATION_HEADER_PARAM = 'param';
    const ENTITY_OPERATION_HEADER_VALUE = 'value';
    const ENTITY_OPERATION_URL_PARAM = 'param';
    const ENTITY_OPERATION_URL_PARAM_TYPE = 'type';
    const ENTITY_OPERATION_URL_PARAM_KEY = 'key';
    const ENTITY_OPERATION_URL_PARAM_VALUE = 'value';
    const ENTITY_OPERATION_ENTRY = 'entry';
    const ENTITY_OPERATION_ENTRY_KEY = 'key';
    const ENTITY_OPERATION_ENTRY_VALUE = 'value';
    const ENTITY_OPERATION_ARRAY = 'array';
    const ENTITY_OPERATION_ARRAY_KEY = 'key';
    const ENTITY_OPERATION_ARRAY_VALUE = 'value';


    public static function getInstance()
    {
        if (!self::$jsonDefinitionManager) {
            self::$jsonDefinitionManager = new JsonDefinitionManager();
        }

        return self::$jsonDefinitionManager;
    }

    private function __construct()
    {
        // do nothing
    }

    private function getObjects()
    {
        $objectManager = ObjectManagerFactory::getObjectManager();
        $metadataParser = $objectManager->create(MetadataParser::class);
        foreach ($metadataParser->readMetadata()[JsonDefinitionManager::ENTITY_OPERATION_ROOT_TAG] as
                 $jsonDefName => $jsonDefArray) {
            $operation = $jsonDefArray[JsonDefinitionManager::ENTITY_OPERATION_TYPE];
            $dataType = $jsonDefArray[JsonDefinitionManager::ENTITY_OPERATION_DATA_TYPE];
            $url = $jsonDefArray[JsonDefinitionManager::ENTITY_OPERATION_URL] ?? null;
            $method = $jsonDefArray[JsonDefinitionManager::ENTITY_OPERATION_METHOD] ?? null;
            $auth = $jsonDefArray[JsonDefinitionManager::ENTITY_OPERATION_AUTH] ?? null;
            $headers = [];
            $params = [];
            $jsonMetadata = [];

            if (array_key_exists(JsonDefinitionManager::ENTITY_OPERATION_HEADER, $jsonDefArray)) {
                foreach ($jsonDefArray[JsonDefinitionManager::ENTITY_OPERATION_HEADER] as $headerEntry) {
                    $headers[] = $headerEntry[JsonDefinitionManager::ENTITY_OPERATION_HEADER_PARAM] . ': ' .
                        $headerEntry[JsonDefinitionManager::ENTITY_OPERATION_HEADER_VALUE];
                }
            }

            if (array_key_exists(JsonDefinitionManager::ENTITY_OPERATION_URL_PARAM, $jsonDefArray)) {
                foreach ($jsonDefArray[JsonDefinitionManager::ENTITY_OPERATION_URL_PARAM] as $paramEntry) {
                    $params[$paramEntry[JsonDefinitionManager::ENTITY_OPERATION_URL_PARAM_TYPE]]
                    [$paramEntry[JsonDefinitionManager::ENTITY_OPERATION_URL_PARAM_KEY]] =
                        $paramEntry[JsonDefinitionManager::ENTITY_OPERATION_URL_PARAM_VALUE];
                }
            }

            if (array_key_exists(JsonDefinitionManager::ENTITY_OPERATION_ENTRY, $jsonDefArray)) {
                foreach ($jsonDefArray[JsonDefinitionManager::ENTITY_OPERATION_ENTRY] as $jsonEntryType) {
                    $jsonMetadata[] = new JsonElement(
                        $jsonEntryType[JsonDefinitionManager::ENTITY_OPERATION_ENTRY_KEY],
                        $jsonEntryType[JsonDefinitionManager::ENTITY_OPERATION_ENTRY_VALUE],
                        JsonDefinitionManager::ENTITY_OPERATION_ENTRY
                    );
                }
            }

            if (array_key_exists(JsonDefinitionManager::ENTITY_OPERATION_ARRAY, $jsonDefArray)) {
                foreach ($jsonDefArray[JsonDefinitionManager::ENTITY_OPERATION_ARRAY] as $jsonEntryType) {
                    $jsonMetadata[] = new JsonElement(
                        $jsonEntryType[JsonDefinitionManager::ENTITY_OPERATION_ARRAY_KEY],
                        $jsonEntryType[JsonDefinitionManager::ENTITY_OPERATION_ARRAY_VALUE],
                        JsonDefinitionManager::ENTITY_OPERATION_ARRAY
                    );
                }
            }

            $this->jsonDefinitions[$operation][$dataType] = new JsonDefinition(
                $jsonDefName,
                $operation,
                $dataType,
                $method,
                $url,
                $auth,
                $headers,
                $params,
                $jsonMetadata
            );
        }
    }

    /**
     * @param $type
     * @return JsonDefinition
     */
    public function getJsonDefinition($operation, $type)
    {
        if (!$this->jsonDefinitions) {
            $this->getObjects();
        }

        return $this->jsonDefinitions[$operation][$type];
    }
}
