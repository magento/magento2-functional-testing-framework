<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Persist;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\OperationDefinitionObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\OperationElement;
use Magento\FunctionalTestingFramework\DataGenerator\Util\OperationElementExtractor;

class OperationDataArrayResolver
{
    const PRIMITIVE_TYPES = [
        'string',
        'boolean',
        'integer',
        'double',
        OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY
    ];
    const EXCEPTION_REQUIRED_DATA = "%s of key \" %s\" in \"%s\" is required by metadata, but was not provided.";

    /**
     * The array of entity name and number of objects being created,
     * we don't need to track objects in update and delete operations.
     *
     * @var array
     */
    private static $entitySequences = [];

    /**
     * The array of dependentEntities this class can be given. When finding linked entities, APIExecutor
     * uses this repository before looking for static data.
     *
     * @var array
     */
    private $dependentEntities = [];

    /**
     * OperationDataArrayResolver constructor.
     *
     * @param array $dependentEntities
     */
    public function __construct($dependentEntities = null)
    {
        //empty constructor
        if ($dependentEntities !== null) {
            foreach ($dependentEntities as $entity) {
                $this->dependentEntities[$entity->getName()] = $entity;
            }
        }
    }

    /**
     * This function returns an array which is structurally equal to the data which is needed by the magento web api,
     * magento backend / frontend requests for entity creation. The function retrieves an array describing the entity's
     * operation metadata and traverses any dependencies recursively forming an array which represents the data
     * structure for the request of the desired entity type.
     *
     * @param EntityDataObject $entityObject
     * @param array $operationMetadata
     * @param string $operation
     * @return array
     * @throws \Exception
     */
    public function resolveOperationDataArray($entityObject, $operationMetadata, $operation)
    {
        $operationDataArray = [];
        self::incrementSequence($entityObject->getName());

        foreach ($operationMetadata as $operationElement) {
            if ($operationElement->getType() == OperationElementExtractor::OPERATION_OBJECT_OBJ_NAME) {
                $entityObj = $this->resolveOperationObjectAndEntityData($entityObject, $operationElement->getValue());
                $operationDataArray[$operationElement->getValue()] =
                    $this->resolveOperationDataArray($entityObj, $operationElement->getNestedMetadata(), $operation);
                continue;
            }

            $operationElementType = $operationElement->getValue();

            if (in_array($operationElementType, self::PRIMITIVE_TYPES)) {
                $elementData = $entityObject->getDataByName(
                    $operationElement->getKey(),
                    EntityDataObject::CEST_UNIQUE_VALUE
                );

                // If data was defined at all, attempt to put it into operation data array
                // If data was not defined, and element is required, throw exception
                // If no data is defined, don't input defaults per primitive into operation data array
                if ($elementData != null) {
                    if (array_key_exists($operationElement->getKey(), $entityObject->getUniquenessData())) {
                        $uniqueData = $entityObject->getUniquenessDataByName($operationElement->getKey());
                        if ($uniqueData === 'suffix') {
                            $elementData .= (string)self::getSequence($entityObject->getName());
                        } else {
                            $elementData = (string)self::getSequence($entityObject->getName()) . $elementData;
                        }
                    }
                    $operationDataArray[$operationElement->getKey()] = $this->castValue(
                        $operationElementType,
                        $elementData
                    );

                } elseif ($operationElement->getRequired()) {
                    throw new \Exception(sprintf(
                        self::EXCEPTION_REQUIRED_DATA,
                        $operationElement->getType(),
                        $operationElement->getKey(),
                        $entityObject->getName()
                    ));
                }
            } else {
                $entityNamesOfType = $entityObject->getLinkedEntitiesOfType($operationElementType);

                // If an element is required by metadata, but was not provided in the entity, throw an exception
                if ($operationElement->getRequired() && $entityNamesOfType == null) {
                    throw new \Exception(sprintf(
                        self::EXCEPTION_REQUIRED_DATA,
                        $operationElement->getType(),
                        $operationElement->getKey(),
                        $entityObject->getName()
                    ));
                }
                foreach ($entityNamesOfType as $entityName) {
                    $operationDataSubArray = $this->resolveNonPrimitiveElement(
                        $entityName,
                        $operationElement,
                        $operation
                    );

                    if ($operationElement->getType() == OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY) {
                        $operationDataArray[$operationElement->getKey()][] = $operationDataSubArray;
                    } else {
                        $operationDataArray[$operationElement->getKey()] = $operationDataSubArray;
                    }
                }
            }
        }

        return $operationDataArray;
    }

    /**
     * This function does a comparison of the entity object being matched to the operation element. If there is a
     * mismatch in type we attempt to use a nested entity, if the entities are properly matched, we simply return
     * the object.
     *
     * @param EntityDataObject $entityObject
     * @param string $operationElementValue
     * @return EntityDataObject|null
     */
    private function resolveOperationObjectAndEntityData($entityObject, $operationElementValue)
    {
        if ($operationElementValue != $entityObject->getType()) {
            // if we have a mismatch attempt to retrieve linked data and return just the first linkage
            $linkName = $entityObject->getLinkedEntitiesOfType($operationElementValue)[0];
            return DataObjectHandler::getInstance()->getObject($linkName);
        }

        return $entityObject;
    }

    /**
     * Resolves DataObjects and pre-defined metadata (in other operation.xml file) referenced by the operation
     *
     * @param string $entityName
     * @param OperationElement $operationElement
     * @param string $operation
     * @return array
     */
    private function resolveNonPrimitiveElement($entityName, $operationElement, $operation)
    {
        $linkedEntityObj = $this->resolveLinkedEntityObject($entityName);

        // in array case
        if (!empty($operationElement->getNestedOperationElement($operationElement->getValue()))
            && $operationElement->getType() == OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY
        ) {
            $operationSubArray = $this->resolveOperationDataArray(
                $linkedEntityObj,
                [$operationElement->getNestedOperationElement($operationElement->getValue())],
                $operation
            );

            return $operationSubArray[$operationElement->getValue()];
        }

        $operationMetadata = OperationDefinitionObjectHandler::getInstance()->getOperationDefinition(
            $operation,
            $linkedEntityObj->getType()
        )->getOperationMetadata();

        return $this->resolveOperationDataArray($linkedEntityObj, $operationMetadata, $operation);
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
                if (strtolower($newVal) === 'false') {
                    return false;
                }
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
