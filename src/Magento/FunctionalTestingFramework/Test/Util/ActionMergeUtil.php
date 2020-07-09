<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;

/**
 * Class ActionMergeUtil
 */
class ActionMergeUtil
{
    const STEP_MISSING_ERROR_MSG =
        "Merge Error - Step could not be found in either TestXML or DeltaXML.
        \t%s: '%s'\tTestStep: '%s'\tLinkedStep: '%s'";

    const WAIT_ATTR = 'timeout';
    const WAIT_ACTION_NAME = 'waitForPageLoad';
    const WAIT_ACTION_SUFFIX = 'WaitForPageLoad';
    const DEFAULT_SKIP_ON_ORDER = 'before';
    const DEFAULT_SKIP_OFF_ORDER = 'after';
    const DEFAULT_WAIT_ORDER = 'after';
    const APPROVED_ACTIONS = ['fillField', 'magentoCLI', 'field'];
    const SECRET_MAPPING = ['fillField' => 'fillSecretField', 'magentoCLI' => 'magentoCLISecret'];
    const CREDS_REGEX = "/{{_CREDS\.([\w|\/]+)}}/";

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
     * Name of calling context.
     *
     * @var string
     */
    private $name;

    /**
     * Type of calling context.
     *
     * @var string
     */
    private $type;

    /**
     * ActionMergeUtil constructor.
     *
     * @param string $contextName
     * @param string $contextType
     */
    public function __construct($contextName, $contextType)
    {
        $this->name = $contextName;
        $this->type = $contextType;
    }

    /**
     * Method to execute merge of steps and insert wait steps.
     *
     * @param array   $parsedSteps
     * @param boolean $skipActionGroupResolution
     * @return array
     * @throws TestReferenceException
     * @throws XmlException
     */
    public function resolveActionSteps($parsedSteps, $skipActionGroupResolution = false)
    {
        $this->mergeActions($parsedSteps);
        $this->insertWaits();

        if ($skipActionGroupResolution) {
            return $this->orderedSteps;
        }

        $resolvedActions = $this->resolveActionGroups($this->orderedSteps);
        return $this->resolveSecretFieldAccess($resolvedActions);
    }

    /**
     * Takes an array of actions and resolves any references to secret fields. The function then validates whether the
     * reference is valid and replaces the function name accordingly to hide arguments at runtime.
     *
     * @param ActionObject[] $resolvedActions
     * @return ActionObject[]
     * @throws TestReferenceException
     */
    private function resolveSecretFieldAccess($resolvedActions)
    {
        $actions = [];
        foreach ($resolvedActions as $resolvedAction) {
            $action = $resolvedAction;
            $actionHasSecretRef = $this->actionAttributeContainsSecretRef($resolvedAction->getCustomActionAttributes());
            $actionType = $resolvedAction->getType();

            if ($actionHasSecretRef && !(in_array($actionType, self::APPROVED_ACTIONS))) {
                throw new TestReferenceException("You cannot reference secret data outside " .
                    "of the fillField, magentoCLI and createData actions");
            }

            // Do NOT remap actions that don't need it.
            if (isset(self::SECRET_MAPPING[$actionType]) && $actionHasSecretRef) {
                $actionType = self::SECRET_MAPPING[$actionType];
            }

            $action = new ActionObject(
                $action->getStepKey(),
                $actionType,
                $action->getCustomActionAttributes(),
                $action->getLinkedAction(),
                $action->getOrderOffset(),
                $action->getActionOrigin(),
                $action->getDeprecatedUsages()
            );

            $actions[$action->getStepKey()] = $action;
        }

        return $actions;
    }

    /**
     * Returns a boolean based on whether or not the action attributes contain a reference to a secret field.
     *
     * @param array $actionAttributes
     * @return boolean
     */
    private function actionAttributeContainsSecretRef($actionAttributes)
    {
        foreach ($actionAttributes as $actionAttribute) {
            if (is_array($actionAttribute)) {
                return $this->actionAttributeContainsSecretRef($actionAttribute);
            }

            preg_match_all(self::CREDS_REGEX, $actionAttribute, $matches);

            if (!empty($matches[0])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Method to resolve action group references and insert relevant actions into step flow
     *
     * @param array $mergedSteps
     * @throws TestReferenceException
     * @return array
     */
    private function resolveActionGroups($mergedSteps)
    {
        $newOrderedList = [];

        foreach ($mergedSteps as $key => $mergedStep) {
            /**@var ActionObject $mergedStep**/
            if ($mergedStep->getType() == ActionObjectExtractor::ACTION_GROUP_TAG) {
                $actionGroupRef = $mergedStep->getCustomActionAttributes()[ActionObjectExtractor::ACTION_GROUP_REF];
                $actionGroup = ActionGroupObjectHandler::getInstance()->getObject($actionGroupRef);
                if ($actionGroup == null) {
                    throw new TestReferenceException("Could not find ActionGroup by ref \"{$actionGroupRef}\"");
                }
                $args = $mergedStep->getCustomActionAttributes()[ActionObjectExtractor::ACTION_GROUP_ARGUMENTS] ?? null;
                $actionsToMerge = $actionGroup->getSteps($args, $key);
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
     * @throws XmlException
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
                    $step->getStepKey() . self::WAIT_ACTION_SUFFIX,
                    self::WAIT_ACTION_NAME,
                    $waitStepAttributes,
                    $step->getStepKey(),
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
     * @throws TestReferenceException
     */
    private function sortActions($parsedSteps)
    {
        foreach ($parsedSteps as $parsedStep) {
            try {
                $parsedStep->resolveReferences();

                if ($parsedStep->getLinkedAction()) {
                    $this->stepsToMerge[$parsedStep->getStepKey()] = $parsedStep;
                } else {
                    $this->orderedSteps[$parsedStep->getStepKey()] = $parsedStep;
                }
            } catch (\Exception $e) {
                throw new TestReferenceException(
                    $e->getMessage() .
                    ".\nException occurred parsing action at StepKey \"" . $parsedStep->getStepKey() . "\""
                );
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
                $this->type,
                $this->name,
                $stepToMerge->getStepKey(),
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
        $this->orderedSteps = $previous_items + [$stepToMerge->getStepKey() => $stepToMerge] + $next_items;
    }
}
