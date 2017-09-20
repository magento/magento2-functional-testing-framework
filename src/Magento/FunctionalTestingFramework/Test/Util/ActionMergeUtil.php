<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;

/**
 * Class ActionMergeUtil
 */
class ActionMergeUtil
{
    const WAIT_ATTR = 'timeout';
    const WAIT_ACTION_NAME = 'waitForPageLoad';
    const WAIT_ACTION_SUFFIX = 'WaitForPageLoad';
    const DEFAULT_WAIT_ORDER = 'after';

    /**
     * Array holding final resulting steps
     *
     * @var array
     */
    private $orderedSteps = [];

    /**
     * Array holding action to be merged
     *
     * @var array
     */
    private $stepsToMerge = [];

    /**
     * ActionMergeUtil constructor.
     */
    public function __construct()
    {
        // empty constructor
    }

    /**
     * Method to execute merge of steps and insert wait steps.
     *
     * @param array $parsedSteps
     * @param bool $skipActionGroupResolution
     * @return array
     */
    public function resolveActionSteps($parsedSteps, $skipActionGroupResolution = false)
    {
        $this->mergeActions($parsedSteps);
        $this->insertWaits();

        if ($skipActionGroupResolution) {
            return $this->orderedSteps;
        }

        return $this->resolveActionGroups($this->orderedSteps);
    }

    /**
     * Method to resolve action group references and insert relevant actions into step flow
     *
     * @param array $mergedSteps
     * @return array
     */
    private function resolveActionGroups($mergedSteps)
    {
        $newOrderedList = [];

        foreach ($mergedSteps as $key => $mergedStep) {
            /**@var ActionObject $mergedStep**/
            if ($mergedStep->getType() == ActionObjectExtractor::ACTION_GROUP_TAG) {
                $actionGroup = ActionGroupObjectHandler::getInstance()->getObject(
                    $mergedStep->getCustomActionAttributes()[ActionObjectExtractor::ACTION_GROUP_REF]
                );
                $args = $mergedStep->getCustomActionAttributes()[ActionObjectExtractor::ACTION_GROUP_ARGUMENTS] ?? null;
                $actionsToMerge = $actionGroup->getSteps($args);
                $newOrderedList = $newOrderedList + $actionsToMerge;
            } else {
                $newOrderedList[$key]  = $mergedStep;
            }
        }

        return $newOrderedList;
    }

    /**
     * This method runs a step sort, loops steps which need to be merged, and runs the mergeStep function on each one.
     *
     * @param array $parsedSteps
     * @return void
     */
    private function mergeActions($parsedSteps)
    {
        $this->sortActions($parsedSteps);

        foreach ($this->stepsToMerge as $stepName => $stepToMerge) {
            if (!array_key_exists($stepName, $this->orderedSteps)) {
                $this->mergeAction($stepToMerge);
            }
        }
        unset($stepName);
        unset($stepToMerge);
    }

    /**
     * Runs through the prepared orderedSteps and calls insertWait if a step requires a wait after it.
     *
     * @return void
     */
    private function insertWaits()
    {
        foreach ($this->orderedSteps as $step) {
            if ($step->getTimeout()) {
                $waitStepAttributes = [self::WAIT_ATTR => $step->getTimeout()];
                $waitStep = new ActionObject(
                    $step->getMergeKey() . self::WAIT_ACTION_SUFFIX,
                    self::WAIT_ACTION_NAME,
                    $waitStepAttributes,
                    $step->getMergeKey(),
                    self::DEFAULT_WAIT_ORDER
                );
                $this->insertStep($waitStep);
            }
        }
    }

    /**
     * This method takes the steps from the parser and splits steps which need merge from steps that are ordered.
     *
     * @param array $parsedSteps
     * @return void
     * @throws XmlException
     */
    private function sortActions($parsedSteps)
    {
        foreach ($parsedSteps as $parsedStep) {
            $parsedStep->resolveReferences();
            if ($parsedStep->getLinkedAction()) {
                $this->stepsToMerge[$parsedStep->getMergeKey()] = $parsedStep;
            } else {
                $this->orderedSteps[$parsedStep->getMergeKey()] = $parsedStep;
            }
        }
    }

    /**
     * Recursively merges in each step and its dependencies
     *
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
     * Inserts a step into the ordered steps array based on position and step referenced.
     *
     * @param ActionObject $stepToMerge
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
