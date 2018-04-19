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
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
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
    const ENTITY_OPERATION_URL_AREA = 'area';
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
    const ENTITY_OPERATION_BACKEND_REMOVE = 'removeBackend';

    /**
     * The singleton instance of this class
     *
     * @var OperationDefinitionObjectHandler
     */
    private static $INSTANCE;

    /**
     * An array containing all <operation>
     *
     * @var OperationDefinitionObject[]
     */
    private $operationDefinitionObjects = [];

    /**
     * A helper used to convert the primitive array parser output into objects.
     *
     * @var OperationElementExtractor
     */
    private $operationElementExtractor;

    /**
     * The constructor
     */
    private function __construct()
    {
        $this->operationElementExtractor = new OperationElementExtractor();
    }

    /**
     * Return the singleton instance of this class
     *
     * @return OperationDefinitionObjectHandler
     */
    public static function getInstance()
    {
        if (!self::$INSTANCE) {
            self::$INSTANCE = new OperationDefinitionObjectHandler();
            self::$INSTANCE->initialize();
        }

        return self::$INSTANCE;
    }

    /**
     * Return an <operation> by the "name" attribute
     *
     * @param string $name
     * @return OperationDefinitionObject
     */
    public function getObject($name)
    {
        return $this->getAllObjects()[$name];
    }

    /**
     * Return all <operation>
     *
     * @return OperationDefinitionObject[]
     */
    public function getAllObjects()
    {
        return $this->operationDefinitionObjects;
    }

    /**
     * Return an <operation> by operation and type. Eg. "create" and "address"
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
     * Read metadata xml via the Magento parser and then convert the primitive array output
     * into an array of objects.
     *
     * @return void
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function initialize()
    {
        //TODO: Reduce CyclomaticComplexity/NPathComplexity/Length of method, remove warning suppression.
        $objectManager = ObjectManagerFactory::getObjectManager();
        $parser = $objectManager->create(OperationDefinitionParser::class);
        $parserOutput = $parser->readOperationMetadata()[OperationDefinitionObjectHandler::ENTITY_OPERATION_ROOT_TAG];
        foreach ($parserOutput as $dataDefName => $opDefArray) {
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
            $removeBackend = $opDefArray[OperationDefinitionObjectHandler::ENTITY_OPERATION_BACKEND_REMOVE] ?? false;

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
                    $operationElements[] = $this->operationElementExtractor->extractOperationElement($opElementArray);
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
                        $nestedDataElement = $this->operationElementExtractor->extractOperationElement(
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

            $this->operationDefinitionObjects[$operation . $dataType] = new OperationDefinitionObject(
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
                $removeBackend,
                $successRegex,
                $returnRegex
            );
        }
    }
}
