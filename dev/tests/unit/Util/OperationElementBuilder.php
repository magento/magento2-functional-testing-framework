<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Util;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\OperationDefinitionObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\OperationElement;

class OperationElementBuilder
{
    /**
     * A set of fields which can be operation elements or primitive data (valueName => valueType). By default this
     * value contains a set of primitive fields.
     *
     * @var array
     */
    private $fields = [
        'name' => 'string',
        'gpa' => 'number',
        'phone' => 'integer',
        'isPrimary' => 'boolean',
        'empty_string' => 'string'
    ];

    /**
     * Array of nested metadata, merged to main object via addElement()
     *
     * @var array
     */
    private $nestedMetadata = [];

    /**
     * The key to which the metadata defined will be mapped
     * in JSON { key : value }
     *
     * @var string
     */
    private $key = 'testType';

    /**
     * The type of value to which the metadata defined will be mapped (e.g. string, boolean, user defined object).
     * in JSON { key : value }
     *
     * @var string
     */
    private $type = 'testType';

    /**
     * The element type to which the metadata defined will be transformed into.
     * in JSON:
     * { } <- object
     * [ ] <- array
     * key : value <- field
     *
     * @var string
     */
    private $elementType = OperationDefinitionObjectHandler::ENTITY_OPERATION_OBJECT;

    /**
     * The array of elements which the Operation Element contains to resolve declarations within arrays specifically.
     * Arrays can take object references or definitions within themselves. This metadata has to be at a parent level in
     * order to resolve properly.
     *
     * @var array
     */
    private $nestedElements;

    /**
     * Build function which takes params defined by the user and returns a new Operation Element.
     *
     * @return OperationElement
     */
    public function build()
    {
        return new OperationElement(
            $this->key,
            $this->type,
            $this->elementType,
            null,
            $this->nestedElements,
            array_merge($this->nestedMetadata, self::buildOperationElementFields($this->fields))
        );
    }

    /**
     * Sets a new element type, overwrites any existing.
     *
     * @param string $elementType
     * @return OperationElementBuilder
     */
    public function withElementType($elementType)
    {
        $this->elementType = $elementType;
        return $this;
    }

    /**
     * Set a new set of fields or operation elements
     *
     * @param array $fields
     * @return OperationElementBuilder
     */
    public function withFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Sets a key for the operation element. See ref to param key for explanation.
     *
     * @param string $key
     * @return OperationElementBuilder
     */
    public function withKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Sets a type for the operation element. See ref to param type for explanation.
     *
     * @param string $type
     * @return OperationElementBuilder
     */
    public function withType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Adds a set of new Operation Elements to the nested metadata.
     *
     * @param array $elementsToAdd
     * @return OperationElementBuilder
     */
    public function addElements($elementsToAdd)
    {
        foreach ($elementsToAdd as $fieldKey => $metadata) {
            $this->nestedMetadata[$fieldKey] = $metadata;
        }

        return $this;
    }

    /**
     * Adds a new set of fields (value => type) into an object parameter to be converted to Operation Elements.
     *
     * @param array $fieldsToAdd
     * @return OperationElementBuilder
     */
    public function addFields($fieldsToAdd)
    {
        foreach ($fieldsToAdd as $fieldKey => $type) {
            $this->fields[$fieldKey] = $type;
        }

        return $this;
    }

    /**
     * Sets an array nested elements to an object property.
     *
     * @param array $nestedElements
     * @return OperationElementBuilder
     */
    public function withNestedElements($nestedElements)
    {
        $this->nestedElements = $nestedElements;
        return $this;
    }

    /**
     * Takes an array of fields (value => type) and returns an array of Operations Elements of type field.
     *
     * @param array $fields
     * @return array
     */
    public static function buildOperationElementFields($fields)
    {
        $operationElements = [];
        foreach ($fields as $fieldName => $type) {
            $operationElements[] = new OperationElement(
                $fieldName,
                $type,
                null,
                OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY
            );
        }

        return $operationElements;
    }
}
