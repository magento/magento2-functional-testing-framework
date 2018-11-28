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
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

class OperationDataArrayResolver
{
    const PRIMITIVE_TYPES = [
        'string',
        'boolean',
        'integer',
        'number'
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
     * @param array            $operationMetadata
     * @param string           $operation
     * @param boolean          $fromArray
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function resolveOperationDataArray($entityObject, $operationMetadata, $operation, $fromArray = false)
    {
        //TODO: Refactor to reduce Cyclomatic Complexity, remove SupressWarning accordingly.
        $operationDataArray = [];
        self::incrementSequence($entityObject->getName());

        foreach ($operationMetadata as $operationElement) {
            if ($operationElement->getType() == OperationElementExtractor::OPERATION_OBJECT_OBJ_NAME) {
                $entityObj = $this->resolveOperationObjectAndEntityData($entityObject, $operationElement->getValue());
                if (null === $entityObj && $operationElement->isRequired()) {
                    throw new \Exception(sprintf(
                        self::EXCEPTION_REQUIRED_DATA,
                        $operationElement->getType(),
                        $operationElement->getKey(),
                        $entityObject->getName()
                    ));
                } elseif (null === $entityObj) {
                    continue;
                }
                $operationData = $this->resolveOperationDataArray(
                    $entityObj,
                    $operationElement->getNestedMetadata(),
                    $operation,
                    $fromArray
                );
                if (!$fromArray) {
                    $operationDataArray[$operationElement->getKey()] = $operationData;
                } else {
                    $operationDataArray = $operationData;
                }
                continue;
            }

            $operationElementType = $operationElement->getValue();

            if (in_array($operationElementType, self::PRIMITIVE_TYPES)) {
                $elementData = $this->resolvePrimitiveReference(
                    $entityObject,
                    $operationElement->getKey(),
                    $operationElement->getType()
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
                } elseif ($operationElement->isRequired()) {
                    throw new \Exception(sprintf(
                        self::EXCEPTION_REQUIRED_DATA,
                        $operationElement->getType(),
                        $operationElement->getKey(),
                        $entityObject->getName()
                    ));
                }
            } else {
                $operationElementProperty = null;
                if (strpos($operationElementType, '.') !== false) {
                    $operationElementComponents = explode('.', $operationElementType);
                    $operationElementType = $operationElementComponents[0];
                    $operationElementProperty = $operationElementComponents[1];
                }

                $entityNamesOfType = $entityObject->getLinkedEntitiesOfType($operationElementType);

                // If an element is required by metadata, but was not provided in the entity, throw an exception
                if ($operationElement->isRequired() && $entityNamesOfType == null) {
                    throw new \Exception(sprintf(
                        self::EXCEPTION_REQUIRED_DATA,
                        $operationElement->getType(),
                        $operationElement->getKey(),
                        $entityObject->getName()
                    ));
                }
                foreach ($entityNamesOfType as $entityName) {
                    if ($operationElementProperty === null) {
                        $operationDataSubArray = $this->resolveNonPrimitiveElement(
                            $entityName,
                            $operationElement,
                            $operation,
                            $fromArray
                        );
                    } else {
                        $linkedEntityObj = $this->resolveLinkedEntityObject($entityName);
                        $operationDataSubArray = $linkedEntityObj->getDataByName($operationElementProperty, 0);

                        if ($operationDataSubArray === null) {
                            throw new \Exception(
                                sprintf('Property %s not found in entity %s \n', $operationElementProperty, $entityName)
                            );
                        }
                    }

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
     * Resolves a reference for a primitive piece of data, if the data cannot be found as a defined field, the method
     * looks to see if any vars have been declared with the same operationKey and resolves based on defined dependent
     * entities.
     *
     * @param EntityDataObject $entityObject
     * @param string           $operationKey
     * @param string           $operationElementType
     * @return array|string
     * @throws TestFrameworkException
     */
    private function resolvePrimitiveReference($entityObject, $operationKey, $operationElementType)
    {
        $elementData = $entityObject->getDataByName(
            $operationKey,
            EntityDataObject::CEST_UNIQUE_VALUE
        );

        if ($elementData == null && $entityObject->getVarReference($operationKey) != null) {
            list($type, $field) = explode(
                DataObjectHandler::_SEPARATOR,
                $entityObject->getVarReference($operationKey)
            );

            if ($operationElementType == OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY) {
                $elementDatas = [];
                $entities = $this->getDependentEntitiesOfType($type);
                foreach ($entities as $entity) {
                    $elementDatas[] = $entity->getDataByName($field, EntityDataObject::CEST_UNIQUE_VALUE);
                }

                return $elementDatas;
            }

            $entity = $this->getDependentEntitiesOfType($type)[0];
            $elementData = $entity->getDataByName($field, EntityDataObject::CEST_UNIQUE_VALUE);
        }

        return $elementData;
    }

    /**
     * Returns all dependent entities of the type passed in as an arg (the dependent entities are given at runtime,
     * and are not statically defined).
     *
     * @param string $type
     * @return array
     */
    private function getDependentEntitiesOfType($type)
    {
        $entitiesOfType = [];

        foreach ($this->dependentEntities as $dependentEntity) {
            if ($dependentEntity->getType() == $type) {
                $entitiesOfType[] = $dependentEntity;
            }
        }

        return $entitiesOfType;
    }

    /**
     * This function does a comparison of the entity object being matched to the operation element. If there is a
     * mismatch in type we attempt to use a nested entity, if the entities are properly matched, we simply return
     * the object.
     *
     * @param EntityDataObject $entityObject
     * @param string           $operationElementValue
     * @return EntityDataObject|null
     * @throws \Exception
     */
    private function resolveOperationObjectAndEntityData($entityObject, $operationElementValue)
    {
        if ($operationElementValue != $entityObject->getType()) {
            // if we have a mismatch attempt to retrieve linked data and return just the first linkage
            $linkName = $entityObject->getLinkedEntitiesOfType($operationElementValue);
            if (!empty($linkName)) {
                $linkName = $linkName[0];
                return DataObjectHandler::getInstance()->getObject($linkName);
            }
            return null;
        }

        return $entityObject;
    }

    /**
     * Resolves DataObjects and pre-defined metadata (in other operation.xml file) referenced by the operation
     *
     * @param string           $entityName
     * @param OperationElement $operationElement
     * @param string           $operation
     * @param boolean          $fromArray
     * @return array
     * @throws \Exception
     */
    private function resolveNonPrimitiveElement($entityName, $operationElement, $operation, $fromArray = false)
    {
        $linkedEntityObj = $this->resolveLinkedEntityObject($entityName);

        // in array case
        if (!empty($operationElement->getNestedOperationElement($operationElement->getValue()))
            && $operationElement->getType() == OperationDefinitionObjectHandler::ENTITY_OPERATION_ARRAY
        ) {
            $operationSubArray = $this->resolveOperationDataArray(
                $linkedEntityObj,
                [$operationElement->getNestedOperationElement($operationElement->getValue())],
                $operation,
                true
            );

            return $operationSubArray;
        }

        $operationMetadata = OperationDefinitionObjectHandler::getInstance()->getOperationDefinition(
            $operation,
            $linkedEntityObj->getType()
        )->getOperationMetadata();

        return $this->resolveOperationDataArray($linkedEntityObj, $operationMetadata, $operation, $fromArray);
    }

    /**
     * Method to wrap entity resolution, checks locally defined dependent entities first
     *
     * @param string $entityName
     * @return EntityDataObject
     * @throws \Exception
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
     * @return integer
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

        if (is_array($value)) {
            $newVals = [];
            foreach($value as $val) {
                $newVals[] = $this->castValue($type, $val);
            }

            return $newVals;
        }

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
            case 'number':
                $newVal = (float)$value;
                break;
        }

        return $newVal;
    }
    // @codingStandardsIgnoreEnd
}
