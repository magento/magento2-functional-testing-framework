<?php

namespace Magento\AcceptanceTestFramework\Test;

class CestObject
{
    private $name;
    private $hooks = [];
    private $annotations = [];
    private $useStatements = [];
    private $tests = [];

    public function __construct($name, $annotations, $useStatements, $tests, $hooks)
    {
        $this->name = $name;
        $this->annotations = $annotations;
        $this->useStatements = $useStatements;
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

    public function getUseStatements()
    {
        return $this->useStatements;
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


