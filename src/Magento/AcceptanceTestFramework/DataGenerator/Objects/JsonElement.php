<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Objects;

class JsonElement
{
    private $key;
    private $value;
    private $type;

    public function __construct($key, $value, $type)
    {
        $this->key = $key;
        $this->value = $value;
        $this->type = $type;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getType()
    {
        return $this->type;
    }
}