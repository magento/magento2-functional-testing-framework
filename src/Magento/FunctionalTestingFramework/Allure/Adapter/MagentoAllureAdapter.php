<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Allure\Adapter;

use Codeception\Step\Comment;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Yandex\Allure\Adapter\Model\Status;
use Yandex\Allure\Adapter\Model\Step;
use Yandex\Allure\Codeception\AllureCodeception;
use Yandex\Allure\Adapter\Event\StepStartedEvent;
use Yandex\Allure\Adapter\Event\StepFinishedEvent;
use Yandex\Allure\Adapter\Event\StepFailedEvent;
use Yandex\Allure\Adapter\Event\TestCaseFailedEvent;
use Yandex\Allure\Adapter\Event\TestCaseFinishedEvent;
use Yandex\Allure\Adapter\Event\TestCaseBrokenEvent;
use Codeception\Event\FailEvent;
use Codeception\Event\SuiteEvent;
use Codeception\Event\StepEvent;

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
    /**
     * Test files cache.
     *
     * @var array
     */
    private $testFiles = [];

    /**
     * Boolean value to indicate if steps are invisible steps
     *
     * @var boolean
     */
    private $atInvisibleSteps = false;

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
     *     inserts stepKey into step name
     *
     * @param StepEvent $stepEvent
     * @return void
     * @throws \Yandex\Allure\Adapter\AllureException
     */
    public function stepBefore(StepEvent $stepEvent)
    {
        $stepAction = $stepEvent->getStep()->getAction();

        // Set atInvisibleSteps flag and return if step is in INVISIBLE_STEP_ACTIONS
        if (in_array($stepAction, ActionObject::INVISIBLE_STEP_ACTIONS)) {
            $this->atInvisibleSteps = true;
            return;
        }

        // Set back atInvisibleSteps flag
        if ($this->atInvisibleSteps && !in_array($stepAction, ActionObject::INVISIBLE_STEP_ACTIONS)) {
                $this->atInvisibleSteps = false;
        }

        //Hard set to 200; we don't expose this config in MFTF
        $argumentsLength = 200;
        $stepKey = null;

        if (!($stepEvent->getStep() instanceof Comment)) {
            $stepKey = $this->retrieveStepKey($stepEvent->getStep()->getLine());
        }

        // DO NOT alter action if actionGroup is starting, need the exact actionGroup name for good logging
        if (strpos($stepAction, ActionGroupObject::ACTION_GROUP_CONTEXT_START) === false
            && !($stepEvent->getStep() instanceof Comment)
        ) {
            $stepAction = $stepEvent->getStep()->getHumanizedActionWithoutArguments();
        }
        $stepArgs = $stepEvent->getStep()->getArgumentsAsString($argumentsLength);

        if (!trim($stepAction)) {
            $stepAction = $stepEvent->getStep()->getMetaStep()->getHumanizedActionWithoutArguments();
            $stepArgs = $stepEvent->getStep()->getMetaStep()->getArgumentsAsString($argumentsLength);
        }

        $stepName = '';
        if ($stepKey !== null) {
            $stepName .= '[' . $stepKey . '] ';
        }
        $stepName .= $stepAction . ' ' . $stepArgs;

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
        // Simply return if step is INVISIBLE_STEP_ACTIONS
        if ($this->atInvisibleSteps) {
            if ($stepEvent->getStep()->hasFailed()) {
                $this->atInvisibleSteps = false;
            }
            return;
        }

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
     * Override of parent method. Adds in steps for hard PHP Errors if they arrise.
     *
     * @param FailEvent $failEvent
     * @return void
     */
    public function testError(FailEvent $failEvent)
    {
        $event = new TestCaseBrokenEvent();
        $e = $failEvent->getFail();
        $message = $e->getMessage();

        // Create new step with an error for Allure
        $failStep = new Step();
        $failStep->setName("ERROR");
        $failStep->setTitle($message);
        $failStep->setStatus(Status::BROKEN);

        // Retrieve Allure Steps and add in the new BROKEN step
        $rootStep = $this->getLifecycle()->getStepStorage()->pollLast();
        $rootStep->addStep($failStep);
        $this->getLifecycle()->getStepStorage()->put($rootStep);

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

        $actionGroupStepKey = null;
        foreach ($rootStep->getSteps() as $step) {
            $stepKey = str_replace($actionGroupStepKey, '', $step->getName());
            if ($stepKey !== '[]' && $stepKey !== null) {
                $step->setName($stepKey);
            }
            // if actionGroup flag, start nesting
            if (strpos($step->getName(), ActionGroupObject::ACTION_GROUP_CONTEXT_START) !== false) {
                if ($actionGroupStepContainer !== null) {
                    //actionGroup still being nested, need to close out and finish it.
                    $formattedSteps[] = $actionGroupStepContainer;
                    $actionGroupStepContainer = null;
                    $actionGroupStepKey = null;
                }

                $step->setName(str_replace(ActionGroupObject::ACTION_GROUP_CONTEXT_START, '', $step->getName()));
                $actionGroupStepContainer = $step;
                $actionGroupStepKey = $this->retrieveActionGroupStepKey($step);
                continue;
            }

            // if actionGroup ended, add stack to steps
            if (stripos($step->getName(), ActionGroupObject::ACTION_GROUP_CONTEXT_END) !== false) {
                $formattedSteps[] = $actionGroupStepContainer;
                $actionGroupStepContainer = null;
                $actionGroupStepKey = null;
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
     * Reads action group stepKey from step.
     *
     * @param Step $step
     * @return string|null
     */
    private function retrieveActionGroupStepKey($step)
    {
        $actionGroupStepKey = null;

        preg_match(TestGenerator::ACTION_GROUP_STEP_KEY_REGEX, $step->getName(), $matches);

        if (!empty($matches['actionGroupStepKey'])) {
            $actionGroupStepKey = ucfirst($matches['actionGroupStepKey']);
        }

        return $actionGroupStepKey;
    }

    /**
     * Reading stepKey from file.
     *
     * @param string $stepLine
     * @return string|null
     */
    private function retrieveStepKey($stepLine)
    {
        $stepKey = null;
        list($filePath, $stepLine) = explode(":", $stepLine);
        $stepLine = $stepLine - 1;

        if (!array_key_exists($filePath, $this->testFiles)) {
            $this->testFiles[$filePath] = explode(PHP_EOL, file_get_contents($filePath));
        }

        preg_match(TestGenerator::ACTION_STEP_KEY_REGEX, $this->testFiles[$filePath][$stepLine], $matches);
        if (!empty($matches['stepKey'])) {
            $stepKey = $matches['stepKey'];
        }

        return $stepKey;
    }
}
