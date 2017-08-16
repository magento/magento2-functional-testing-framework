<?php

namespace Magento\AcceptanceTestFramework\Test\Objects;

use Magento\AcceptanceTestFramework\Exceptions\XmlException;

class TestObject
{
    /**
     * Name of the test
     * @var string $name
     */
    private $name;

    /**
     * Array which contains the final set of test steps in order after merge
     * @var array $orderedSteps
     */
    private $orderedSteps = [];

    /**
     * Array which contains steps to be merged into test flow
     * @var array $stepsToMerge
     */
    private $stepsToMerge = [];

    /**
     * Array which contains steps parsed in and are the default set
     * @var array $parsedSteps
     */
    private $parsedSteps = [];

    /**
     * Array which contains annotations indexed by name
     * @var array $annotations
     */
    private $annotations = [];

    const STEP_MISSING_ERROR_MSG =
        "Merge Error - Step could not be found in either TestXML or DeltaXML.
        \tTest = '%s'\tTestStep='%s'\tLinkedStep'%s'";

    /**
     * TestObject constructor.
     * @constructor
     * @param string $name
     * @param array $parsedSteps
     * @param array $annotations
     */
    public function __construct($name, $parsedSteps, $annotations)
    {
        $this->name = $name;
        $this->parsedSteps = $parsedSteps;
        $this->annotations = $annotations;
    }

    /**
     * Getter for the Test Name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Getter for the Test Annotations
     * @return array
     */
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
        $this->insertWaits();
        return $this->orderedSteps;
    }

    /**
     * This method takes the steps from the parser and splits steps which need merge from steps that are ordered.
     * @return void
     * @throws XmlException
     */
    private function sortActions()
    {
        foreach ($this->parsedSteps as $parsedStep) {
            $parsedStep->resolveReferences();
            if ($parsedStep->getLinkedAction()) {
                $this->stepsToMerge[$parsedStep->getMergeKey()] = $parsedStep;
            } else {
                $this->orderedSteps[$parsedStep->getMergeKey()] = $parsedStep;
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
     * @throws XmlException
     * @return void
     */
    private function mergeAction($stepToMerge)
    {
        $linkedStep = $stepToMerge->getLinkedAction();

        if (!array_key_exists($linkedStep, $this->orderedSteps)
            and
            !array_key_exists($linkedStep, $this->stepsToMerge)) {
            throw new XmlException(sprintf(
                self::STEP_MISSING_ERROR_MSG,
                $this->getName(),
                $stepToMerge->getMergeKey(),
                $linkedStep
            ));
        } elseif (!array_key_exists($linkedStep, $this->orderedSteps)) {
            $this->mergeAction($this->stepsToMerge[$linkedStep]);
        }

        $this->insertStep($stepToMerge);
    }

    /**
     * Runs through the prepared orderedSteps and calls insertWait if a step requires a wait after it.
     * @return void
     */
    private function insertWaits()
    {
        foreach ($this->orderedSteps as $step) {
            if ($step->getTimeout()) {
                $waitStepAttributes = array('timeout' => $step->getTimeout());
                $waitStep = new ActionObject(
                    $step->getMergeKey() . 'WaitForPageLoad',
                    'waitForPageLoad',
                    $waitStepAttributes,
                    $step->getMergeKey(),
                    'after'
                );
                $this->insertStep($waitStep);
            }
        }
    }

    /**
     * Inserts a step into the ordered steps array based on position and step referenced.
     * @param $stepToMerge
     * @return void
     */
    private function insertStep($stepToMerge)
    {
        $position = array_search(
            $stepToMerge->getLinkedAction(),
            array_keys($this->orderedSteps)
        ) + $stepToMerge->getOrderOffset();
        $previous_items = array_slice($this->orderedSteps, 0, $position, true);
        $next_items = array_slice($this->orderedSteps, $position, null, true);
        $this->orderedSteps = $previous_items + [$stepToMerge->getMergeKey() => $stepToMerge] + $next_items;
    }
}
