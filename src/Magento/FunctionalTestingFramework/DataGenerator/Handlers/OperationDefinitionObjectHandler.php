<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers;

use Magento\FunctionalTestingFramework\DataGenerator\Objects\OperationDefinitionObject;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\OperationElement;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\OperationDefinitionParser;
use Magento\FunctionalTestingFramework\DataGenerator\Util\OperationElementExtractor;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;

class OperationDefinitionObjectHandler implements ObjectHandlerInterface
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
    const ENTITY_OPERATION_CONTENT_TYPE = 'contentType';
    const ENTITY_OPERATION_HEADER_PARAM = 'param';
    const ENTITY_OPERATION_HEADER_VALUE = 'value';
    const ENTITY_OPERATION_URL_PARAM = 'param';
    const ENTITY_OPERATION_URL_PARAM_KEY = 'key';
    const ENTITY_OPERATION_URL_PARAM_VALUE = 'value';
    const ENTITY_OPERATION_ENTRY = 'field';
    const ENTITY_OPERATION_ENTRY_KEY = 'key';
    const ENTITY_OPERATION_ENTRY_VALUE = 'value';
    const ENTITY_OPERATION_ARRAY = 'array';
    const ENTITY_OPERATION_ARRAY_KEY = 'key';
    const ENTITY_OPERATION_ARRAY_VALUE = 'value';
    const ENTITY_OPERATION_OBJECT = 'object';
    const ENTITY_OPERATION_OBJECT_KEY = 'key';
    const ENTITY_OPERATION_OBJECT_VALUE = 'value';
    const ENTITY_OPERATION_REQUIRED = 'required';

    /**
     * Singleton Instance of class
     *
     * @var OperationDefinitionObjectHandler
     */
    private static $DATA_DEFINITION_OBJECT_HANDLER;

    /**
     * Array containing all Data Definition Objects
     *
     * @var array
     */
    private $operationDefinitions = [];

    /**
     * Object used to extract dataObjects from array into DataElements
     *
     * @var OperationElementExtractor
     */
    private $dataDefExtractor;

    /**
     * Singleton method to return DataDefinitionProcessor.
     *
     * @return OperationDefinitionObjectHandler
     */
    public static function getInstance()
    {
        if (!self::$DATA_DEFINITION_OBJECT_HANDLER) {
            self::$DATA_DEFINITION_OBJECT_HANDLER = new OperationDefinitionObjectHandler();
            self::$DATA_DEFINITION_OBJECT_HANDLER->initDataDefinitions();
        }

        return self::$DATA_DEFINITION_OBJECT_HANDLER;
    }

    /**
     * Returns a OperationDefinitionObject object based on name
     *
     * @param string $operationDefinitionName
     * @return OperationDefinitionObject
     */
    public function getObject($operationDefinitionName)
    {
        return $this->operationDefinitions[$operationDefinitionName];
    }

    /**
     * Returns all data Definition objects
     *
     * @return array
     */
    public function getAllObjects()
    {
        return $this->operationDefinitions;
    }

    /**
     * DataDefintionArrayProcessor constructor.
     */
    private function __construct()
    {
        $this->dataDefExtractor = new OperationElementExtractor();
    }

    /**
     * This method takes an operation such as create and a data type such as 'customer' and returns the corresponding
     * data definition defined in metadata.xml
     *
     * @param string $operation
     * @param string $dataType
     * @return OperationDefinitionObject
     */
    public function getOperationDefinition($operation, $dataType)
    {
        return $this->getObject($operation . $dataType);
    }

    /**
     * This method reads all dataDefinitions from metadata xml into memory.
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function initDataDefinitions()
    {
        //TODO: Reduce CyclomaticComplexity/NPathComplexity/Length of method, remove warning suppression.
        $objectManager = ObjectManagerFactory::getObjectManager();
        $metadataParser = $objectManager->create(OperationDefinitionParser::class);
        foreach ($metadataParser->readOperationMetadata()
                 [OperationDefinitionObjectHandler::ENTITY_OPERATION_ROOT_TAG] as $dataDefName => $opDefArray) {
            $operation = $opDefArray[OperationDefinitionObjectHandler::ENTITY_OPERATION_TYPE];
            $dataType = $opDefArray[OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE];
            $url = $opDefArray[OperationDefinitionObjectHandler::ENTITY_OPERATION_URL] ?? null;
            $method = $opDefArray[OperationDefinitionObjectHandler::ENTITY_OPERATION_METHOD] ?? null;
            $auth = $opDefArray[OperationDefinitionObjectHandler::ENTITY_OPERATION_AUTH] ?? null;
            $successRegex = $opDefArray[OperationDefinitionObjectHandler::ENTITY_OPERATION_SUCCESS_REGEX] ?? null;
            $returnRegex = $opDefArray[OperationDefinitionObjectHandler::ENTITY_OPERATION_RETURN_REGEX] ?? null;
            $contentType = $opDefArray[OperationDefinitionObjectHandler::ENTITY_OPERATION_CONTENT_TYPE][0]['value']
                ?? null;
            $headers = [];
            $params = [];
            $operationElements = [];

            if (array_key_exists(OperationDefinitionObjectHandler::ENTITY_OPERATION_HEADER, $opDefArray)) {
                foreach ($opDefArray[OperationDefinitionObjectHandler::ENTITY_OPERATION_HEADER] as $headerEntry) {
                    if (isset($headerEntry[OperationDefinitionObjectHandler::ENTITY_OPERATION_HEADER_VALUE])
                        && $headerEntry[OperationDefinitionObjectHandler::ENTITY_OPERATION_HEADER_VALUE] !== 'none') {
                        $headers[] = $headerEntry[OperationDefinitionObjectHandler::ENTITY_OPERATION_HEADER_PARAM]
                            . ': '
                            . $headerEntry[OperationDefinitionObjectHandler::ENTITY_OPERATION_HEADER_VALUE];
                    }
                }
            }

            if (array_key_exists(OperationDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM, $opDefArray)) {
                foreach ($opDefArray[OperationDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM] as $paramEntry) {
                    $params[$paramEntry[OperationDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM_KEY]] =
                        $paramEntry[OperationDefinitionObjectHandler::ENTITY_OPERATION_URL_PARAM_VALUE];
                }
            }

            // extract relevant OperationObjects as OperationElements
            if (array_key_exists(OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT, $opDefArray)) {
                foreach ($opDefArray[OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT] as $opElementArray) {
                    $operationElements[] = $this->dataDefExtractor->extractOperationElement($opElementArray);
                }
            }

            //handle loose operation fields
            if (array_key_exists(OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY, $opDefArray)) {
                foreach ($opDefArray[OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY] as $operationField) {
                    $operationElements[] = new OperationElement(
                        $operationField[OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY],
                        $operationField[OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE],
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY,
                        $operationField[OperationDefinitionObjectHandler::ENTITY_OPERATION_REQUIRED] ?? null
                    );
                }
            }

            // handle loose json arrays
            if (array_key_exists(OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY, $opDefArray)) {
                foreach ($opDefArray[OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY] as $operationField) {
                    $subOperationElements = [];
                    $value = null;
                    $type = null;

                    if (array_key_exists(
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT,
                        $operationField
                    )) {
                        $nestedDataElement = $this->dataDefExtractor->extractOperationElement(
                            $operationField[OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT][0]
                        );
                        $subOperationElements[$nestedDataElement->getKey()] = $nestedDataElement;
                        $value = $nestedDataElement->getValue();
                        $type = $nestedDataElement->getKey();
                    } else {
                        $value = $operationField[OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_VALUE][0]
                        [OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_VALUE];
                        $type = OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY;
                    }

                    $operationElements[] = new OperationElement(
                        $operationField[OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY_KEY],
                        $value,
                        $type,
                        $operationField[OperationDefinitionObjectHandler::ENTITY_OPERATION_REQUIRED] ?? null,
                        $subOperationElements
                    );
                }
            }

            $this->operationDefinitions[$operation . $dataType] = new OperationDefinitionObject(
                $dataDefName,
                $operation,
                $dataType,
                $method,
                $url,
                $auth,
                $headers,
                $params,
                $operationElements,
                $contentType,
                $successRegex,
                $returnRegex
            );
        }
    }
}
