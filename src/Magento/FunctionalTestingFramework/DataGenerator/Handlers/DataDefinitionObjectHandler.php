<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers;

use Magento\FunctionalTestingFramework\DataGenerator\Objects\DataDefinition;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\DataElement;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\OperationMetadataParser;
use Magento\FunctionalTestingFramework\DataGenerator\Util\DataObjectExtractor;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;

class DataDefinitionObjectHandler implements ObjectHandlerInterface
{
    const ENTITY_OPERATION_ROOT_TAG = 'operation';
    const ENTITY_OPERATION_TYPE = 'type';
    const ENTITY_OPERATION_DATA_TYPE = 'dataType';
    const ENTITY_OPERATION_URL = 'url';
    const ENTITY_OPERATION_METHOD = 'method';
    const ENTITY_OPERATION_AUTH = 'auth';
    const ENTITY_OPERATION_STORE_CODE = 'storeCode';
    const ENTITY_OPERATION_SUCCESS_REGEX = 'successRegex';
    const ENTITY_OPERATION_RETURN_REGEX = 'returnRegex';
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
    const ENTITY_OPERATION_DATA_OBJECT = 'metaDataObject';

    /**
     * Singleton Instance of class
     *
     * @var DataDefinitionObjectHandler
     */
    private static $DATA_DEFINITION_OBJECT_HANDLER;

    /**
     * Array containing all Data Definition Objects
     *
     * @var array
     */
    private $dataDefinitions = [];

    /**
     * Object used to extract dataObjects from array into DataElements
     *
     * @var DataObjectExtractor
     */
    private $dataDefExtractor;

    /**
     * Singleton method to return DataDefinitionProcessor.
     *
     * @return DataDefinitionObjectHandler
     */
    public static function getInstance()
    {
        if (!self::$DATA_DEFINITION_OBJECT_HANDLER) {
            self::$DATA_DEFINITION_OBJECT_HANDLER = new DataDefinitionObjectHandler();
            self::$DATA_DEFINITION_OBJECT_HANDLER->initDataDefinitions();
        }

        return self::$DATA_DEFINITION_OBJECT_HANDLER;
    }

    /**
     * Returns a DataDefinition object based on name
     *
     * @param string $dataDefinitionName
     * @return DataDefinition
     */
    public function getObject($dataDefinitionName)
    {
        return $this->dataDefinitions[$dataDefinitionName];
    }

    /**
     * Returns all data Definition objects
     *
     * @return array
     */
    public function getAllObjects()
    {
        return $this->dataDefinitions;
    }

    /**
     * DataDefintionArrayProcessor constructor.
     */
    private function __construct()
    {
        $this->dataDefExtractor = new DataObjectExtractor();
    }

    /**
     * This method takes an operation such as create and a data type such as 'customer' and returns the corresponding
     * data definition defined in metadata.xml
     *
     * @param string $operation
     * @param string $dataType
     * @return DataDefinition
     */
    public function getDataDefinition($operation, $dataType)
    {
        return $this->getObject($operation . $dataType);
    }

    /**
     * This method reads all dataDefinitions from metadata xml into memory.
     * @return void
     */
    private function initDataDefinitions()
    {
        $objectManager = ObjectManagerFactory::getObjectManager();
        $metadataParser = $objectManager->create(OperationMetadataParser::class);
        foreach ($metadataParser->readOperationMetadata()[DataDefinitionObjectHandler::ENTITY_OPERATION_ROOT_TAG] as
                 $dataDefName => $dataDefArray) {
            $operation = $dataDefArray[DataDefinitionObjectHandler::ENTITY_OPERATION_TYPE];
            $dataType = $dataDefArray[DataDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE];
            $url = $dataDefArray[DataDefinitionObjectHandler::ENTITY_OPERATION_URL] ?? null;
            $method = $dataDefArray[DataDefinitionObjectHandler::ENTITY_OPERATION_METHOD] ?? null;
            $auth = $dataDefArray[DataDefinitionObjectHandler::ENTITY_OPERATION_AUTH] ?? null;
            $storeCode = $dataDefArray[DataDefinitionObjectHandler::ENTITY_OPERATION_STORE_CODE] ?? null;
            $successRegex = $dataDefArray[DataDefinitionObjectHandler::ENTITY_OPERATION_SUCCESS_REGEX] ?? null;
            $returnRegex = $dataDefArray[DataDefinitionObjectHandler::ENTITY_OPERATION_RETURN_REGEX] ?? null;
            $headers = [];
            $params = [];
            $metaData = [];

            if (array_key_exists(DataDefinitionObjectHandler::ENTITY_OPERATION_HEADER, $dataDefArray)) {
                foreach ($dataDefArray[DataDefinitionObjectHandler::ENTITY_OPERATION_HEADER] as $headerEntry) {
                    if (isset($headerEntry[DataDefinitionObjectHandler::ENTITY_OPERATION_HEADER_VALUE])
                        && $headerEntry[DataDefinitionObjectHandler::ENTITY_OPERATION_HEADER_VALUE] !== 'none') {
                        $headers[] = $headerEntry[DataDefinitionObjectHandler::ENTITY_OPERATION_HEADER_PARAM] . ': ' .
                            $headerEntry[DataDefinitionObjectHandler::ENTITY_OPERATION_HEADER_VALUE];
                    }
                }
            }

            if (array_key_exists(DataDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM, $dataDefArray)) {
                foreach ($dataDefArray[DataDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM] as $paramEntry) {
                    $params[$paramEntry[DataDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM_TYPE]]
                    [$paramEntry[DataDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM_KEY]] =
                        $paramEntry[DataDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM_VALUE];
                }
            }

            // extract relevant dataObjects as DataElements
            if (array_key_exists(DataDefinitionObjectHandler::ENTITY_OPERATION_DATA_OBJECT, $dataDefArray)) {
                foreach ($dataDefArray[DataDefinitionObjectHandler::ENTITY_OPERATION_DATA_OBJECT] as $dataObjectArray) {
                    $metaData[] = $this->dataDefExtractor->extractDataObject($dataObjectArray);
                }
            }

            //handle loose entries

            if (array_key_exists(DataDefinitionObjectHandler::ENTITY_OPERATION_ENTRY, $dataDefArray)) {
                foreach ($dataDefArray[DataDefinitionObjectHandler::ENTITY_OPERATION_ENTRY] as $dataEntryType) {
                    $metaData[] = new DataElement(
                        $dataEntryType[DataDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY],
                        $dataEntryType[DataDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE],
                        DataDefinitionObjectHandler::ENTITY_OPERATION_ENTRY
                    );
                }
            }

            if (array_key_exists(DataDefinitionObjectHandler::ENTITY_OPERATION_ARRAY, $dataDefArray)) {
                foreach ($dataDefArray[DataDefinitionObjectHandler::ENTITY_OPERATION_ARRAY] as $dataEntryType) {
                    $subMetaData = [];
                    $value = null;
                    $type = null;

                    if (array_key_exists(DataDefinitionObjectHandler::ENTITY_OPERATION_DATA_OBJECT, $dataEntryType)) {
                        $nestedDataElement = $this->dataDefExtractor->extractDataObject(
                            $dataEntryType[DataDefinitionObjectHandler::ENTITY_OPERATION_DATA_OBJECT][0]
                        );
                        $subMetaData[$nestedDataElement->getKey()] = $nestedDataElement;
                        $value = $nestedDataElement->getValue();
                        $type = $nestedDataElement->getKey();
                    } else {
                        $value = $dataEntryType[DataDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_VALUE][0]
                        [DataDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_VALUE];
                        $type = [DataDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_VALUE];
                    }

                    $metaData[] = new DataElement(
                        $dataEntryType[DataDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_KEY],
                        $value,
                        $type,
                        $subMetaData
                    );
                }
            }

            $this->dataDefinitions[$operation . $dataType] = new DataDefinition(
                $dataDefName,
                $operation,
                $dataType,
                $method,
                $url,
                $auth,
                $headers,
                $params,
                $metaData,
                $successRegex,
                $returnRegex,
                $storeCode
            );
        }
    }
}
