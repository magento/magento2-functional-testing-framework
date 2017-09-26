<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Util;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\JsonDefinitionObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\JsonElement;

class JsonObjectExtractor
{
    const JSON_OBJECT_KEY = 'key';
    const JSON_OBJECT_DATA_TYPE = 'dataType';
    const JSON_OBJECT_ARRAY = 'array';
    const JSON_OBJECT_ENTRY = 'entry';
    const JSON_OBJECT_OBJ_NAME = 'jsonObject';
    const JSON_OBJECT_ARRAY_VALUE = 'value';

    /**
     * JsonObjectExtractor constructor.
     */
    public function __construct()
    {
        // public constructor
    }

    /**
     * Takes an array representative of a jsonObject and converts the array into a JsonElement
     *
     * @param array $jsonObjectArray
     * @return JsonElement
     * @throws \Exception
     */
    public function extractJsonObject($jsonObjectArray)
    {
        // extract key
        $jsonDefKey = $jsonObjectArray[JsonObjectExtractor::JSON_OBJECT_KEY];

        // extract dataType
        $dataType = $jsonObjectArray[JsonObjectExtractor::JSON_OBJECT_DATA_TYPE];

        $jsonMetadata = [];
        $nestedJsonElements = [];

        // extract nested entries
        if (array_key_exists(JsonObjectExtractor::JSON_OBJECT_ENTRY, $jsonObjectArray)) {
            $this->extractJsonEntries($jsonMetadata, $jsonObjectArray[JsonObjectExtractor::JSON_OBJECT_ENTRY]);
        }

        // extract nested arrays
        if (array_key_exists(JsonObjectExtractor::JSON_OBJECT_ARRAY, $jsonObjectArray)) {
            $this->extractJsonArrays($jsonMetadata, $jsonObjectArray[JsonObjectExtractor::JSON_OBJECT_ARRAY]);
        }

        // extract nested
        if (array_key_exists(JsonObjectExtractor::JSON_OBJECT_OBJ_NAME, $jsonObjectArray)) {
            foreach ($jsonObjectArray[JsonObjectExtractor::JSON_OBJECT_OBJ_NAME] as $jsonObject) {
                $nestedJsonElement = $this->extractJsonObject($jsonObject);
                $jsonMetadata[] = $nestedJsonElement;
            }
        }

        // a jsonObject specified in xml must contain corresponding metadata for the object
        if (empty($jsonMetadata)) {
            throw new \Exception("must specificy jsonObject metadata if declaration is used");
        }

        return new JsonElement(
            $jsonDefKey,
            $dataType,
            JsonObjectExtractor::JSON_OBJECT_OBJ_NAME,
            $jsonObjectArray[JsonDefinitionObjectHandler::ENTITY_OPERATION_REQUIRED] ?? null,
            $nestedJsonElements,
            $jsonMetadata
        );
    }

    /**
     * Creates and Adds relevant JsonElements from json entries defined within jsonObject array
     *
     * @param array &$jsonMetadata
     * @param array $jsonEntryArray
     * @return void
     */
    private function extractJsonEntries(&$jsonMetadata, $jsonEntryArray)
    {
        foreach ($jsonEntryArray as $jsonEntryType) {
            $jsonMetadata[] = new JsonElement(
                $jsonEntryType[JsonDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY],
                $jsonEntryType[JsonDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE],
                JsonDefinitionObjectHandler::ENTITY_OPERATION_ENTRY,
                $jsonEntryType[JsonDefinitionObjectHandler::ENTITY_OPERATION_REQUIRED] ?? null
            );
        }
    }

    /**
     * Creates and Adds relevant JsonElements from json arrays defined within jsonObject array
     *
     * @param array &$jsonArrayData
     * @param array $jsonArrayArray
     * @return void
     */
    private function extractJsonArrays(&$jsonArrayData, $jsonArrayArray)
    {
        foreach ($jsonArrayArray as $jsonEntryType) {
            $jsonElementValue =
                $jsonEntryType[JsonDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_VALUE][0]
                [JsonObjectExtractor::JSON_OBJECT_ARRAY_VALUE] ?? null;

            $nestedJsonElements = [];
            if (array_key_exists(JsonObjectExtractor::JSON_OBJECT_OBJ_NAME, $jsonEntryType)) {
                //add the key to reference this object later
                $jsonObjectKeyedArray = $jsonEntryType[JsonObjectExtractor::JSON_OBJECT_OBJ_NAME][0];
                $jsonObjectKeyedArray[JsonObjectExtractor::JSON_OBJECT_KEY] =
                    $jsonEntryType[JsonDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_KEY];
                $jsonElement = $this->extractJsonObject($jsonObjectKeyedArray);
                $jsonElementValue = $jsonElement->getValue();
                $nestedJsonElements[$jsonElement->getValue()] = $jsonElement;
            }
            $jsonArrayData[] = new JsonElement(
                $jsonEntryType[JsonDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_KEY],
                $jsonElementValue,
                JsonDefinitionObjectHandler::ENTITY_OPERATION_ARRAY,
                $jsonEntryType[JsonDefinitionObjectHandler::ENTITY_OPERATION_REQUIRED] ?? null,
                $nestedJsonElements
            );
        }
    }
}
