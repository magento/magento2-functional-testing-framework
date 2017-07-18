<?php

namespace Magento\AcceptanceTestFramework\Test;

class TestObject
{
    private $name;
    private $orderedSteps = [];
    private $stepsToMerge = [];
    private $parsedSteps = [];
    private $dependencies = [];
    private $annotations = [];

    public function __construct($name, $dependencies, $parsedSteps, $annotations)
    {
        $this->name = $name;
        $this->dependencies = $dependencies;
        $this->parsedSteps = $parsedSteps;
        $this->annotations = $annotations;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDependencies()
    {
        return $this->dependencies;
    }

    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     *This method calls a function to merge custom steps and returns the resulting ordered set of steps.
     * @return array
     */
    public function getOrderedActions()
    {
        $this->mergeActions();
        return $this->orderedSteps;
    }

    /**
     * This method takes the steps from the parser and splits steps which need merge from steps that are ordered.
     * @return void
     */
    private function sortActions()
    {
        foreach ($this->parsedSteps as $parsedStep) {
            if ($parsedStep->getLinkedAction()) {
                $this->stepsToMerge[$parsedStep->getName()] = $parsedStep;
            } else {
                $this->orderedSteps[$parsedStep->getName()] = $parsedStep;
            }
        }
    }

    /**
     * This method runs a step sort, loops steps which need to be merged, and runs the mergeStep function on each one.
     * @return void
     */
    private function mergeActions()
    {
        $this->sortActions();

        foreach ($this->stepsToMerge as $stepName => $stepToMerge) {
            if (!array_key_exists($stepName, $this->orderedSteps)) {
                $this->mergeAction($stepToMerge);
            }
        }
        unset($stepName);
        unset($stepToMerge);
    }

    /**
     * Recursively merges in each step and its dependencies
     * @param ActionObject $stepToMerge
     * @return void
     */
    private function mergeAction($stepToMerge)
    {
        $linkedStep = $stepToMerge->getLinkedAction();

        if (!array_key_exists($linkedStep, $this->orderedSteps)) {
            $this->mergeAction($this->stepsToMerge[$stepToMerge->getLinkedAction()]);
        }

        $position = array_search($linkedStep, array_keys($this->orderedSteps)) + $stepToMerge->getOrderOffset();
        $previous_items = array_slice($this->orderedSteps, 0, $position, true);
        $next_items = array_slice($this->orderedSteps, $position, null, true);
        $this->orderedSteps = $previous_items + [$stepToMerge->getName() => $stepToMerge] + $next_items;
    }

}
