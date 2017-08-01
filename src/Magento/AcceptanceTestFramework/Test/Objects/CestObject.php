<?php

namespace Magento\AcceptanceTestFramework\Test\Objects;

class CestObject
{
    private $name;
    private $hooks = [];
    private $annotations = [];
    private $tests = [];

    public function __construct($name, $annotations, $tests, $hooks)
    {
        $this->name = $name;
        $this->annotations = $annotations;
        $this->tests = $tests;
        $this->hooks = $hooks;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAnnotations()
    {
        return $this->annotations;
    }

    public function getTests()
    {
        return $this->tests;
    }

    public function getHooks()
    {
        return $this->hooks;
    }
}
