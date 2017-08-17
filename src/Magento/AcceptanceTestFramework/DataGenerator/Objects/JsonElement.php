<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\DataGenerator\Objects;

class JsonElement
{
    /**
     * Key of Json Element.
     *
     * @var string
     */
    private $key;

    /**
     * Value.
     *
     * @var string
     */
    private $value;

    /**
     * Type of element.
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
     * Returns key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Returns value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
