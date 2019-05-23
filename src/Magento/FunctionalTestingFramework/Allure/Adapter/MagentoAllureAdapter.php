<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Allure\Adapter;

use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Allure\Adapter\MagentoAllureStepKeyReader;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
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
use Codeception\Event\TestEvent;

/**
 * Class MagentoAllureAdapter
 *
 * Extends AllureAdapter to provide further information for allure reports
 *
 * @package Magento\FunctionalTestingFramework\Allure
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

class MagentoAllureAdapter extends AllureCodeception
{
    const STEP_PASSED = "passed";
    const SAVE_SCREENSHOT = "save screenshot";
    const ALLURE_STEPKEY_FORMAT = " ------ @stepKey=";

    /**
     * @var integer
     */
    private $stepCount;

    /**
     * @var array
     */
    private $stepKeys;

    /**
     * @var MagentoAllureStepKeyReader
     */
    private $stepKeyReader;

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
     * Override of parent method:
     *     prevent replacing of . to •
     *     strips control characters
     *
     * @param StepEvent $stepEvent
     * @return void
     * @throws \Yandex\Allure\Adapter\AllureException
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

        // Strip control characters so that report generation does not fail
        $stepName = preg_replace('/[[:cntrl:]]/', '', $stepName);

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
        // Pops top of stepStorage, need to add it back in after processing
        $rootStep = $this->getLifecycle()->getStepStorage()->pollLast();
        $formattedSteps = [];
        $actionGroupStepContainer = null;

        // Get properly ordered step keys for this test
        $this->stepKeys = $this->stepKeyReader->getSteps($this->getPassedStepCount($rootStep));

        $stepCount = -1;
        foreach ($rootStep->getSteps() as $step) {
            $stepCount += 1;
            $step->setName($this->appendStepKey($stepCount, $step->getName()));
            // if actionGroup flag, start nesting
            if (strpos($step->getName(), ActionGroupObject::ACTION_GROUP_CONTEXT_START) !== false) {
                $step->setName(str_replace(ActionGroupObject::ACTION_GROUP_CONTEXT_START, '', $step->getName()));
                $actionGroupStepContainer = $step;
                continue;
            }
            // if actionGroup ended, add stack to steps
            if (stripos($step->getName(), ActionGroupObject::ACTION_GROUP_CONTEXT_END) !== false) {
                $formattedSteps[] = $actionGroupStepContainer;
                $actionGroupStepContainer = null;
                continue;
            }

            if ($actionGroupStepContainer !== null) {
                $actionGroupStepContainer->addStep($step);
                if ($step->getStatus() !== self::STEP_PASSED) {
                    // If step didn't pass, need to end action group nesting and set overall step status
                    $actionGroupStepContainer->setStatus($step->getStatus());
                    $formattedSteps[] = $actionGroupStepContainer;
                    $actionGroupStepContainer = null;
                }
            } else {
                // Add step as normal
                $formattedSteps[] = $step;
            }
        }

        // No public function for setting the step's steps
        call_user_func(\Closure::bind(
            function () use ($rootStep, $formattedSteps) {
                $rootStep->steps = $formattedSteps;
            },
            null,
            $rootStep
        ));

        $this->getLifecycle()->getStepStorage()->put($rootStep);

        $this->getLifecycle()->fire(new TestCaseFinishedEvent());
    }

    /**
     * Aggregate to parent method to prepare collecting step keys for a test
     *
     * @return void
     */
    public function testStart(TestEvent $testEvent)
    {
        $this->stepKeys = [];
        $this->stepCount = 0;
        $this->stepKeyReader = new MagentoAllureStepKeyReader(
            $testEvent->getTest()->getFileName(),
            $testEvent->getTest()->getTestMethod()
        );
        parent::testStart($testEvent);
    }

    /**
     * Append step key to matching step
     *
     * @param integer $stepCount
     * @param string $name
     *
     * @return string
     */
    private function appendStepKey($stepCount, $name)
    {
        if (isset($this->stepKeys[$stepCount]['action']) && isset($this->stepKeys[$stepCount]['stepKey'])) {
            if ($this->stepKeys[$stepCount]['action'] == "comment"
                || strpos($name, $this->stepKeys[$stepCount]['action']) !== false) {
                $name .= self::ALLURE_STEPKEY_FORMAT . $this->stepKeys[$stepCount]['stepKey'];
            }
        }
        return $name;
    }

    /**
     * Return number of passed steps for a test
     *
     * @param Step $rootStep
     *
     * @return integer
     */
    private function getPassedStepCount($rootStep)
    {
        $counter = 0;
        foreach ($rootStep->getSteps() as $step) {
            if (trim($step->getName()) == self::SAVE_SCREENSHOT) {
                break;
            }
            $counter += 1;
        }
        return $counter;
    }
}
