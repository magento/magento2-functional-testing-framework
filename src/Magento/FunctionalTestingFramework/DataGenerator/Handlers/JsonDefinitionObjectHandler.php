<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers;

use Magento\FunctionalTestingFramework\DataGenerator\Objects\JsonDefinition;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\JsonElement;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\OperationMetadataParser;
use Magento\FunctionalTestingFramework\DataGenerator\Util\JsonObjectExtractor;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;

class JsonDefinitionObjectHandler implements ObjectHandlerInterface
{
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
    const ENTITY_OPERATION_OBJECT = 'object';
    const ENTITY_OPERATION_OBJECT_KEY = 'key';
    const ENTITY_OPERATION_OBJECT_VALUE = 'value';
    const ENTITY_OPERATION_JSON_OBJECT = 'jsonObject';

    /**
     * Singleton Instance of class
     *
     * @var JsonDefinitionObjectHandler
     */
    private static $JSON_DEFINITION_OBJECT_HANDLER;

    /**
     * Array containing all Json Definition Objects
     *
     * @var array
     */
    private $jsonDefinitions = [];

    /**
     * Object used to extract jsonObjects from array into JsonElements
     *
     * @var JsonObjectExtractor
     */
    private $jsonDefExtractor;

    /**
     * Singleton method to return JsonDefinitionProcessor.
     *
     * @return JsonDefinitionObjectHandler
     */
    public static function getInstance()
    {
        if (!self::$JSON_DEFINITION_OBJECT_HANDLER) {
            self::$JSON_DEFINITION_OBJECT_HANDLER = new JsonDefinitionObjectHandler();
            self::$JSON_DEFINITION_OBJECT_HANDLER->initJsonDefinitions();
        }

        return self::$JSON_DEFINITION_OBJECT_HANDLER;
    }

    /**
     * Returns a JsonDefinition object based on name
     *
     * @param string $jsonDefinitionName
     * @return JsonDefinition
     */
    public function getObject($jsonDefinitionName)
    {
        return $this->jsonDefinitions[$jsonDefinitionName];
    }

    /**
     * Returns all Json Definition objects
     *
     * @return array
     */
    public function getAllObjects()
    {
        return $this->jsonDefinitions;
    }

    /**
     * JsonDefintionArrayProcessor constructor.
     */
    private function __construct()
    {
        $this->jsonDefExtractor = new JsonObjectExtractor();
    }

    /**
     * This method takes an operation such as create and a data type such as 'customer' and returns the corresponding
     * json definition defined in metadata.xml
     *
     * @param string $operation
     * @param string $dataType
     * @return JsonDefinition
     */
    public function getJsonDefinition($operation, $dataType)
    {
        return $this->getObject($operation . $dataType);
    }

    /**
     * This method reads all jsonDefinitions from metadata xml into memory.
     * @return void
     */
    private function initJsonDefinitions()
    {
        $objectManager = ObjectManagerFactory::getObjectManager();
        $metadataParser = $objectManager->create(OperationMetadataParser::class);
        foreach ($metadataParser->readOperationMetadata()[JsonDefinitionObjectHandler::ENTITY_OPERATION_ROOT_TAG] as
                 $jsonDefName => $jsonDefArray) {
            $operation = $jsonDefArray[JsonDefinitionObjectHandler::ENTITY_OPERATION_TYPE];
            $dataType = $jsonDefArray[JsonDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE];
            $url = $jsonDefArray[JsonDefinitionObjectHandler::ENTITY_OPERATION_URL] ?? null;
            $method = $jsonDefArray[JsonDefinitionObjectHandler::ENTITY_OPERATION_METHOD] ?? null;
            $auth = $jsonDefArray[JsonDefinitionObjectHandler::ENTITY_OPERATION_AUTH] ?? null;
            $headers = [];
            $params = [];
            $jsonMetadata = [];

            if (array_key_exists(JsonDefinitionObjectHandler::ENTITY_OPERATION_HEADER, $jsonDefArray)) {
                foreach ($jsonDefArray[JsonDefinitionObjectHandler::ENTITY_OPERATION_HEADER] as $headerEntry) {
                    $headers[] = $headerEntry[JsonDefinitionObjectHandler::ENTITY_OPERATION_HEADER_PARAM] . ': ' .
                        $headerEntry[JsonDefinitionObjectHandler::ENTITY_OPERATION_HEADER_VALUE];
                }
            }

            if (array_key_exists(JsonDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM, $jsonDefArray)) {
                foreach ($jsonDefArray[JsonDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM] as $paramEntry) {
                    $params[$paramEntry[JsonDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM_TYPE]]
                    [$paramEntry[JsonDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM_KEY]] =
                        $paramEntry[JsonDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM_VALUE];
                }
            }

            // extract relevant jsonObjects as JsonElements
            if (array_key_exists(JsonDefinitionObjectHandler::ENTITY_OPERATION_JSON_OBJECT, $jsonDefArray)) {
                foreach ($jsonDefArray[JsonDefinitionObjectHandler::ENTITY_OPERATION_JSON_OBJECT] as $jsonObjectArray) {
                    $jsonMetadata[] = $this->jsonDefExtractor->extractJsonObject($jsonObjectArray);
                }
            }

            //handle loose entries

            if (array_key_exists(JsonDefinitionObjectHandler::ENTITY_OPERATION_ENTRY, $jsonDefArray)) {
                foreach ($jsonDefArray[JsonDefinitionObjectHandler::ENTITY_OPERATION_ENTRY] as $jsonEntryType) {
                    $jsonMetadata[] = new JsonElement(
                        $jsonEntryType[JsonDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY],
                        $jsonEntryType[JsonDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE],
                        JsonDefinitionObjectHandler::ENTITY_OPERATION_ENTRY
                    );
                }
            }

            if (array_key_exists(JsonDefinitionObjectHandler::ENTITY_OPERATION_ARRAY, $jsonDefArray)) {
                foreach ($jsonDefArray[JsonDefinitionObjectHandler::ENTITY_OPERATION_ARRAY] as $jsonEntryType) {
                    $jsonSubMetadata = [];
                    $value = null;
                    $type = null;

                    if (array_key_exists('jsonObject', $jsonEntryType)) {
                        $jsonNestedElement = $this->jsonDefExtractor->extractJsonObject(
                            $jsonEntryType['jsonObject'][0]
                        );
                        $jsonSubMetadata[$jsonNestedElement->getKey()] = $jsonNestedElement;
                        $value = $jsonNestedElement->getValue();
                        $type = $jsonNestedElement->getKey();
                    } else {
                        $value = $jsonEntryType[JsonDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_VALUE][0]
                        [JsonDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_VALUE];
                        $type = [JsonDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_VALUE];
                    }

                    $jsonMetadata[] = new JsonElement(
                        $jsonEntryType[JsonDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_KEY],
                        $value,
                        $type,
                        $jsonSubMetadata
                    );
                }
            }

            $this->jsonDefinitions[$operation . $dataType] = new JsonDefinition(
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
}
