<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Objects;

class DataElement
{
    /**
     * Data parameter name
     *
     * @var string
     */
    private $key;

    /**
     * Data parameter metadata value (e.g. string, bool)
     *
     * @var string
     */
    private $value;

    /**
     * Data type such as array or entry
     *
     * @var string
     */
    private $type;

    /**
     * Nested data Objects defined within the same operation.xml file
     *
     * @var array
     */
    private $nestedElements = [];

    /**
     * Nested Metadata which must be included for a dataElement of type dataObject
     *
     * @var array|null
     */
    private $nestedMetadata = [];

    /**
     * DataElement constructor.
     * @param string $key
     * @param string $value
     * @param string $type
     * @param array $nestedElements
     * @param array $nestedMetadata
     */
    public function __construct($key, $value, $type, $nestedElements = [], $nestedMetadata = null)
    {
        $this->key = $key;
        $this->value = $value;
        $this->type = $type;
        $this->nestedElements = $nestedElements;
        $this->nestedMetadata = $nestedMetadata;
    }

    /**
     * Getter for data parameter name
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Getter for parameter metadata value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Getter for parameter value type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the nested data element based on the type of entity passed
     *
     * @param string $type
     * @return array
     */
    public function getNestedDataElement($type)
    {
        if (array_key_exists($type, $this->nestedElements)) {
            return $this->nestedElements[$type];
        }

        return [];
    }

    /**
     * Returns relevant nested data metadata for a data element which is a data object
     *
     * @return array|null
     */
    public function getNestedMetadata()
    {
        return $this->nestedMetadata;
    }
}
