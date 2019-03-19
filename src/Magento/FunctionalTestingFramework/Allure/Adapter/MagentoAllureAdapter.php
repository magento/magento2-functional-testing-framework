<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Allure\Adapter;

use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Yandex\Allure\Adapter\Model\Step;
use Yandex\Allure\Codeception\AllureCodeception;
use Yandex\Allure\Adapter\Event\StepStartedEvent;
use Yandex\Allure\Adapter\Event\StepFinishedEvent;
use Yandex\Allure\Adapter\Event\StepFailedEvent;
use Yandex\Allure\Adapter\Event\TestCaseFailedEvent;
use Yandex\Allure\Adapter\Event\TestCaseFinishedEvent;
use Codeception\Event\FailEvent;
use Codeception\Event\SuiteEvent;
use Codeception\Event\StepEvent;

/**
 * Class MagentoAllureAdapter
 *
 * Extends AllureAdapter to provide further information for allure reports
 *
 * @package Magento\FunctionalTestingFramework\Allure
 */

class MagentoAllureAdapter extends AllureCodeception
{
    const STEP_PASSED = "passed";

    /**
     * Array of group values passed to test runner command
     *
     * @return string
     */
    private function getGroup()
    {
        if ($this->options['groups'] != null) {
            return $this->options['groups'][0];
        }
        return null;
    }

    /**
     * Override of parent method to set suitename as suitename and group name concatenated
     *
     * @param SuiteEvent $suiteEvent
     * @return void
     */
    public function suiteBefore(SuiteEvent $suiteEvent)
    {
        $changeSuiteEvent = $suiteEvent;

        if ($this->getGroup() != null) {
            $suite = $suiteEvent->getSuite();
            $suiteName = ($suite->getName()) . "\\" . $this->sanitizeGroupName($this->getGroup());

            call_user_func(\Closure::bind(
                function () use ($suite, $suiteName) {
                    $suite->name = $suiteName;
                },
                null,
                $suite
            ));

            //change suiteEvent
            $changeSuiteEvent = new SuiteEvent(
                $suiteEvent->getSuite(),
                $suiteEvent->getResult(),
                $suiteEvent->getSettings()
            );
        }
        // call parent function
        parent::suiteBefore($changeSuiteEvent);
    }

    /**
     * Function which santizes any group names changed by the framework for execution in order to consolidate reporting.
     *
     * @param string $group
     * @return string
     */
    private function sanitizeGroupName($group)
    {
        $suiteNames = array_keys(SuiteObjectHandler::getInstance()->getAllObjects());
        $exactMatch = in_array($group, $suiteNames);

        // if this is an existing suite name we dont' need to worry about changing it
        if ($exactMatch || strpos($group, "_") === false) {
            return $group;
        }

        // if we can't find this group in the generated suites we have to assume that the group was split for generation
        $groupNameSplit = explode("_", $group);
        array_pop($groupNameSplit);
        $originalName = implode("_", $groupNameSplit);

        // confirm our original name is one of the existing suite names otherwise just return the original group name
        $originalName = in_array($originalName, $suiteNames) ? $originalName : $group;
        return $originalName;
    }

    /**
     * Override of parent method, only different to prevent replacing of . to •
     *
     * @param StepEvent $stepEvent
     * @return void
     */
    public function stepBefore(StepEvent $stepEvent)
    {
        //Hard set to 200; we don't expose this config in MFTF
        $argumentsLength = 200;

        // DO NOT alter action if actionGroup is starting, need the exact actionGroup name for good logging
        if (strpos($stepEvent->getStep()->getAction(), ActionGroupObject::ACTION_GROUP_CONTEXT_START) !== false) {
            $stepAction = $stepEvent->getStep()->getAction();
        } else {
            $stepAction = $stepEvent->getStep()->getHumanizedActionWithoutArguments();
        }
        $stepArgs = $stepEvent->getStep()->getArgumentsAsString($argumentsLength);

        if (!trim($stepAction)) {
            $stepAction = $stepEvent->getStep()->getMetaStep()->getHumanizedActionWithoutArguments();
            $stepArgs = $stepEvent->getStep()->getMetaStep()->getArgumentsAsString($argumentsLength);
        }

        $stepName = $stepAction . ' ' . $stepArgs;

        $this->emptyStep = false;
        $this->getLifecycle()->fire(new StepStartedEvent($stepName));
    }

    /**
     * Override of parent method, fires StepFailedEvent if step has failed (for xml output)
     * @param StepEvent $stepEvent
     * @throws \Yandex\Allure\Adapter\AllureException
     * @return void
     */
    public function stepAfter(StepEvent $stepEvent = null)
    {
        if ($stepEvent->getStep()->hasFailed()) {
            $this->getLifecycle()->fire(new StepFailedEvent());
        }
        $this->getLifecycle()->fire(new StepFinishedEvent());
    }

    /**
     * Override of parent method, fires a TestCaseFailedEvent if a test is marked as incomplete.
     *
     * @param FailEvent $failEvent
     * @return void
     */
    public function testIncomplete(FailEvent $failEvent)
    {
        $event = new TestCaseFailedEvent();
        $e = $failEvent->getFail();
        $message = $e->getMessage();
        $this->getLifecycle()->fire($event->withException($e)->withMessage($message));
    }

    /**
     * Override of parent method, polls stepStorage for testcase and formats it according to actionGroup nesting.
     *
     * @return void
     */
    public function testEnd()
    {
        $rootStep = $this->getLifecycle()->getStepStorage()->pollLast();
        $formattedStep = new Step();
        $formattedStep->setName($rootStep->getName());
        $formattedStep->setStart($rootStep->getStart());
        $formattedStep->setStatus($rootStep->getStatus());

        $actionGroupStepContainer = null;

        foreach ($rootStep->getSteps() as $step) {
            // if actionGroup flag, start nesting
            if (strpos($step->getName(), ActionGroupObject::ACTION_GROUP_CONTEXT_START) !== false) {
                $step->setName(str_replace(ActionGroupObject::ACTION_GROUP_CONTEXT_START, '', $step->getName()));
                $actionGroupStepContainer = $step;
                continue;
            }
            // if actionGroup ended, add stack to steps
            if (stripos($step->getName(), ActionGroupObject::ACTION_GROUP_CONTEXT_END) !== false) {
                $formattedStep->addStep($actionGroupStepContainer);
                $actionGroupStepContainer = null;
                continue;
            }

            if ($actionGroupStepContainer !== null) {
                $actionGroupStepContainer->addStep($step);
                if ($step->getStatus() !== self::STEP_PASSED) {
                    // If step didn't pass, need to end action group nesting and set overall step status
                    $actionGroupStepContainer->setStatus($step->getStatus());
                    $formattedStep->addStep($actionGroupStepContainer);
                    $actionGroupStepContainer = null;                    
                }
            } else {
                // Add step as normal
                $formattedStep->addStep($step);
            }
        }

        // Reset storage with new formatted nested steps
        $this->getLifecycle()->getStepStorage()->clear();
        $this->getLifecycle()->getStepStorage()->put($formattedStep);

        $this->getLifecycle()->fire(new TestCaseFinishedEvent());
    }
}
