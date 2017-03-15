<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
    public function changeConfiguration($config, $value) {
        $this->getModule('WebDriver')->_reconfigure(array($config => $value));
    }
}
