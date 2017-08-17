<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\DataGenerator\Managers;

use Magento\AcceptanceTestFramework\DataGenerator\Objects\JsonDefinition;
use Magento\AcceptanceTestFramework\DataGenerator\Objects\JsonElement;
use Magento\AcceptanceTestFramework\DataGenerator\Parsers\MetadataParser;
use Magento\AcceptanceTestFramework\ObjectManagerFactory;

class JsonDefinitionManager
{
    /**
     * Json definition manager.
     *
     * @var JsonDefinitionManager
     */
    private static $jsonDefinitionManager;

    /**
     * Array with json definitions.
     *
     * @var array
     */
    private $jsonDefinitions;

    /**
     * Entity operation params.
     */
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

    /**
     * Returns instance of JsonDefinitionManager.
     *
     * @return JsonDefinitionManager
     */
    public static function getInstance()
    {
        if (!self::$jsonDefinitionManager) {
            self::$jsonDefinitionManager = new JsonDefinitionManager();
        }

        return self::$jsonDefinitionManager;
    }

    /**
     * JsonDefinitionManager constructor.
     */
    private function __construct()
    {
        // do nothing
    }

    /**
     * Retrieves Json Definitions for all entities.
     *
     * @return void
     */
    private function retrieveJsonDefinitions()
    {
        $objectManager = ObjectManagerFactory::getObjectManager();
        $metadataParser = $objectManager->create(MetadataParser::class);
        foreach ($metadataParser->readMetadata()[self::ENTITY_OPERATION_ROOT_TAG] as
                 $jsonDefName => $jsonDefArray) {
            $operation = $jsonDefArray[self::ENTITY_OPERATION_TYPE];
            $dataType = $jsonDefArray[self::ENTITY_OPERATION_DATA_TYPE];
            $url = $jsonDefArray[self::ENTITY_OPERATION_URL] ?? null;
            $method = $jsonDefArray[self::ENTITY_OPERATION_METHOD] ?? null;
            $auth = $jsonDefArray[self::ENTITY_OPERATION_AUTH] ?? null;
            $headers = [];
            $params = [];
            $jsonMetadata = [];

            if (array_key_exists(self::ENTITY_OPERATION_HEADER, $jsonDefArray)) {
                foreach ($jsonDefArray[self::ENTITY_OPERATION_HEADER] as $headerEntry) {
                    $headers[] = $headerEntry[self::ENTITY_OPERATION_HEADER_PARAM] . ': ' .
                        $headerEntry[self::ENTITY_OPERATION_HEADER_VALUE];
                }
            }

            if (array_key_exists(self::ENTITY_OPERATION_URL_PARAM, $jsonDefArray)) {
                foreach ($jsonDefArray[self::ENTITY_OPERATION_URL_PARAM] as $paramEntry) {
                    $params[$paramEntry[self::ENTITY_OPERATION_URL_PARAM_TYPE]]
                    [$paramEntry[self::ENTITY_OPERATION_URL_PARAM_KEY]] =
                        $paramEntry[self::ENTITY_OPERATION_URL_PARAM_VALUE];
                }
            }

            if (array_key_exists(self::ENTITY_OPERATION_ENTRY, $jsonDefArray)) {
                foreach ($jsonDefArray[self::ENTITY_OPERATION_ENTRY] as $jsonEntryType) {
                    $jsonMetadata[] = new JsonElement(
                        $jsonEntryType[self::ENTITY_OPERATION_ENTRY_KEY],
                        $jsonEntryType[self::ENTITY_OPERATION_ENTRY_VALUE],
                        self::ENTITY_OPERATION_ENTRY
                    );
                }
            }

            if (array_key_exists(self::ENTITY_OPERATION_ARRAY, $jsonDefArray)) {
                foreach ($jsonDefArray[self::ENTITY_OPERATION_ARRAY] as $jsonEntryType) {
                    $jsonMetadata[] = new JsonElement(
                        $jsonEntryType[self::ENTITY_OPERATION_ARRAY_KEY],
                        $jsonEntryType[self::ENTITY_OPERATION_ARRAY_VALUE],
                        self::ENTITY_OPERATION_ARRAY
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
     * Returns json definition object.
     *
     * @param string $operation
     * @param string $type
     * @return JsonDefinition
     */
    public function getJsonDefinition($operation, $type)
    {
        if (!$this->jsonDefinitions) {
            $this->retrieveJsonDefinitions();
        }

        return $this->jsonDefinitions[$operation][$type];
    }
}
