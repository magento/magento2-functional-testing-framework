<?php

namespace Magento\AcceptanceTestFramework\Test;

class ActionObject
{
    private $name;
    private $actor;
    private $function;
    private $parameter;
    private $selector;
    private $linkedAction;
    private $returnVariable;
    private $userInput;
    private $orderOffset = 0;

    public function __construct($name, $actor, $function, $selector, $parameter, $order, $linkedAction, $returnVariable, $userInput)
    {
        $this->name = $name;
        $this->actor = $actor;
        $this->function = $function;
        $this->selector = $selector;
        $this->parameter = $parameter;
        $this->linkedAction = $linkedAction;
        $this->returnVariable = $returnVariable;
        $this->userInput = $userInput;

        if ($order == 'after') {
            $this->orderOffset = 1;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getActor()
    {
        return $this->actor;
    }

    public function getFunction()
    {
        return $this->function;
    }

    public function getParameter()
    {
        return $this->parameter;
    }

    public function getSelector()
    {
        return $this->selector;
    }

    public function getLinkedAction()
    {
        return $this->linkedAction;
    }

    public function getReturnVariable()
    {
        return $this->returnVariable;
    }

    public function getUserInput()
    {
        return $this->userInput;
    }

    public function getOrderOffset()
    {
        return $this->orderOffset;
    }
}
