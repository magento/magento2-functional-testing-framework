<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\DataGenerator\Objects;

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
     * JsonElement constructor.
     * @param string $key
     * @param string $value
     * @param string $type
     */
    public function __construct($key, $value, $type)
    {
        $this->key = $key;
        $this->value = $value;
        $this->type = $type;
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
}
