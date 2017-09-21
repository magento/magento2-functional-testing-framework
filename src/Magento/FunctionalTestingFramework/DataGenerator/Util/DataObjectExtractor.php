<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Util;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataDefinitionObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\DataElement;

class DataObjectExtractor
{
    const DATA_OBJECT_KEY = 'key';
    const DATA_OBJECT_DATA_TYPE = 'dataType';
    const DATA_OBJECT_ARRAY = 'array';
    const DATA_OBJECT_ENTRY = 'entry';
    const MATA_DATA_OBJECT_NAME = 'metaDataObject';
    const DATA_OBJECT_ARRAY_VALUE = 'value';

    /**
     * DataObjectExtractor constructor.
     */
    public function __construct()
    {
        // public constructor
    }

    /**
     * Takes an array representative of a dataObject and converts the array into a DataElement
     *
     * @param array $dataObjectArray
     * @return DataElement
     * @throws \Exception
     */
    public function extractDataObject($dataObjectArray)
    {
        // extract key
        $dataDefKey = $dataObjectArray[DataObjectExtractor::DATA_OBJECT_KEY];

        // extract dataType
        $dataType = $dataObjectArray[DataObjectExtractor::DATA_OBJECT_DATA_TYPE];

        $metaData = [];
        $nestedDataElements = [];

        // extract nested entries
        if (array_key_exists(DataObjectExtractor::DATA_OBJECT_ENTRY, $dataObjectArray)) {
            $this->extractDataEntries($metaData, $dataObjectArray[DataObjectExtractor::DATA_OBJECT_ENTRY]);
        }

        // extract nested arrays
        if (array_key_exists(DataObjectExtractor::DATA_OBJECT_ARRAY, $dataObjectArray)) {
            $this->extractDataArrays($metaData, $dataObjectArray[DataObjectExtractor::DATA_OBJECT_ARRAY]);
        }

        // extract nested
        if (array_key_exists(DataObjectExtractor::MATA_DATA_OBJECT_NAME, $dataObjectArray)) {
            foreach ($dataObjectArray[DataObjectExtractor::MATA_DATA_OBJECT_NAME] as $dataObject) {
                $nestedDataElement = $this->extractDataObject($dataObject);
                $nestedDataElements[$nestedDataElement->getKey()] = $nestedDataElement;
            }
        }

        // a dataObject specified in xml must contain corresponding metadata for the object
        if (empty($metaData)) {
            throw new \Exception("must specify dataObject metadata if declaration is used");
        }

        return new DataElement(
            $dataDefKey,
            $dataType,
            DataObjectExtractor::MATA_DATA_OBJECT_NAME,
            $nestedDataElements,
            $metaData
        );
    }

    /**
     * Creates and Adds relevant DataElements from data entries defined within dataObject array
     *
     * @param array &$metaData
     * @param array $dataEntryArray
     * @return void
     */
    private function extractDataEntries(&$metaData, $dataEntryArray)
    {
        foreach ($dataEntryArray as $dataEntryType) {
            $metaData[] = new DataElement(
                $dataEntryType[DataDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY],
                $dataEntryType[DataDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE],
                DataDefinitionObjectHandler::ENTITY_OPERATION_ENTRY
            );
        }
    }

    /**
     * Creates and Adds relevant DataElements from data arrays defined within dataObject array
     *
     * @param array &$dataArrayData
     * @param array $dataArrayArray
     * @return void
     */
    private function extractDataArrays(&$dataArrayData, $dataArrayArray)
    {
        foreach ($dataArrayArray as $dataEntryType) {
            $dataElementValue =
                $dataEntryType[DataDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_VALUE][0]
                [DataObjectExtractor::DATA_OBJECT_ARRAY_VALUE] ?? null;

            $nestedDataElements = [];
            if (array_key_exists(DataObjectExtractor::MATA_DATA_OBJECT_NAME, $dataEntryType)) {
                //add the key to reference this object later
                $dataObjectKeyedArray = $dataEntryType[DataObjectExtractor::MATA_DATA_OBJECT_NAME][0];
                $dataObjectKeyedArray[DataObjectExtractor::DATA_OBJECT_KEY] =
                    $dataEntryType[DataDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_KEY];
                $dataElement = $this->extractDataObject($dataObjectKeyedArray);
                $dataElementValue = $dataElement->getValue();
                $nestedDataElements[$dataElement->getValue()] = $dataElement;
            }
            $dataArrayData[] = new DataElement(
                $dataEntryType[DataDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_KEY],
                $dataElementValue,
                DataDefinitionObjectHandler::ENTITY_OPERATION_ARRAY,
                $nestedDataElements
            );
        }
    }
}
