<?php

namespace Magento\AcceptanceTestFramework\Test;

class CestHookObject
{
    private $type;
    private $dependencies = array();
    private $actions = array();

    public function __construct($type, $dependencies, $actions)
    {
        $this->type = $type;
        $this->dependencies = $dependencies;
        $this->actions = $actions;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDependencies()
    {
        return $this->dependencies;
    }

    public function getActions()
    {
        return $this->actions;
    }
}