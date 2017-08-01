<?php

namespace Magento\AcceptanceTestFramework\Test\Objects;

class CestHookObject
{
    private $type;
    private $actions = [];

    public function __construct($type, $actions)
    {
        $this->type = $type;
        $this->actions = $actions;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getActions()
    {
        return $this->actions;
    }
}
