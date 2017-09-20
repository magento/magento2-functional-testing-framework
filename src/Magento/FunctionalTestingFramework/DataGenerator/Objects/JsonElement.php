<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Objects;

class JsonElement
{
    /**
     * Json parameter name
     *
     * @var string
     */
    private $key;

    /**
     * Json parameter metadata value (e.g. string, bool)
     *
     * @var string
     */
    private $value;

    /**
     * Json type such as array or entry
     *
     * @var string
     */
    private $type;

    /**
     * Nested Json Objects defined within the same operation.xml file
     *
     * @var array
     */
    private $nestedElements = [];

    /**
     * Nested Metadata which must be included for a jsonElement of type jsonObject
     *
     * @var array|null
     */
    private $nestedMetadata = [];

    /**
     * Json required attribute, used to determine if values need to be cast before insertion.
     * @var bool
     */
    private $required;

    /**
     * JsonElement constructor.
     * @param string $key
     * @param string $value
     * @param string $type
     * @param bool $required
     * @param array $nestedElements
     * @param null|array $nestedMetadata
     */
    public function __construct($key, $value, $type, $required, $nestedElements = [], $nestedMetadata = null)
    {
        $this->key = $key;
        $this->value = $value;
        $this->type = $type;
        $this->nestedElements = $nestedElements;
        if ($required) {
            $this->required = true;
        } else {
            $this->required = false;
        }
        $this->nestedMetadata = $nestedMetadata;
    }

    /**
     * Getter for json parameter name
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
     * Getter for required attribute
     *
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Returns the nested json element based on the type of entity passed
     *
     * @param string $type
     * @return array
     */
    public function getNestedJsonElement($type)
    {
        if (array_key_exists($type, $this->nestedElements)) {
            return $this->nestedElements[$type];
        }

        return [];
    }

    /**
     * Returns relevant nested json metadata for a json element which is a json object
     *
     * @return array|null
     */
    public function getNestedMetadata()
    {
        return $this->nestedMetadata;
    }
}
