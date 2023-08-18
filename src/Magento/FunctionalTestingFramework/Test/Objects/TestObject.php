<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Objects;

use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Util\ActionMergeUtil;
use Magento\FunctionalTestingFramework\Test\Util\ActionObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\TestHookObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;

/**
 * Class TestObject
 */
class TestObject
{
    const WAIT_TIME_ATTRIBUTE = 'time';

    const TEST_ACTION_WEIGHT = [
        'waitForPageLoad' => 1500,
        'amOnPage' => 1500,
        'waitForLoadingMaskToDisappear' => 500,
        'wait' => self::WAIT_TIME_ATTRIBUTE,
        'waitForAjaxLoad' => 500,
        'waitForElementNotVisible' => 500,
        'waitForElementVisible' => 500,
        'waitForText' => 500,
        'waitForElement' => 500,
        'waitForJS' => 500,
        'comment' => 5,
        'assertCount' => 5,
        'closeAdminNotification' => 10,
        'magentoCLI' => 1000,
        'magentoCron' => 3000,
        'createData' =>  500,
        'deleteData' =>  200,
        'updateData' =>  200,
        'getOTP' => 1000,
        'startMessageQueue' => 700,
    ];

    const WEBAPI_AUTH_TEST_ACTIONS = [
        'createData',
        'deleteData',
        'updateData',
        'getData',
    ];

    const WEBAPI_AUTH_TEST_ACTION_WEIGHT = 6000;

    /**
     * Name of the test
     *
     * @var string
     */
    private $name;

    /**
     * Array which contains steps parsed in and are the default set
     *
     * @var ActionObject[]
     */
    private $parsedSteps = [];

    /**
     * Array which contains annotations indexed by name
     *
     * @var array
     */
    private $annotations = [];

    /**
     * Array which contains before and after actions to be executed in scope of a single test.
     *
     * @var array
     */
    private $hooks = [];

    /**
     * String of filename of test
     *
     * @var string
     */
    private $filename;

    /**
     * String of parent test
     *
     * @var string
     */
    private $parentTest;

    /**
     * Holds on to the result of getOrderedActions() to increase test generation performance.
     *
     * @var ActionObject[]
     */
    private $cachedOrderedActions = null;

    /**
     * Deprecation message.
     *
     * @var string|null
     */
    private $deprecated;

    /**
     * Indicates if a test contains an action that requires Web API authentication.
     *
     * @var boolean
     */
    private $hasWebApiAuthAction;

    /**
     * TestObject constructor.
     *
     * @param string           $name
     * @param ActionObject[]   $parsedSteps
     * @param array            $annotations
     * @param TestHookObject[] $hooks
     * @param string           $filename
     * @param string           $parentTest
     * @param string|null      $deprecated
     */
    public function __construct(
        $name,
        $parsedSteps,
        $annotations,
        $hooks,
        $filename = null,
        $parentTest = null,
        $deprecated = null
    ) {
        $this->name = $name;
        $this->parsedSteps = $parsedSteps;
        $this->annotations = $annotations;
        $this->hooks = $hooks;
        $this->filename = $filename;
        $this->parentTest = $parentTest;
        $this->deprecated = $deprecated;
        $this->hasWebApiAuthAction = false;
    }

    /**
     * Getter for the Test Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Getter for the Test Filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Getter for the Parent Test Name
     *
     * @return string
     */
    public function getParentName()
    {
        return $this->parentTest;
    }

    /**
     * Returns deprecated messages.
     *
     * @return string|null
     */
    public function getDeprecated()
    {
        return $this->deprecated;
    }

    /**
     * Getter for the skip_test boolean
     *
     * @return string
     */
    public function isSkipped()
    {
        if (array_key_exists('skip', $this->annotations)) {
            return true;
        }
        return false;
    }

    /**
     * Getter for Codeception format name
     *
     * @return string
     */
    public function getCodeceptionName()
    {
        if (strpos($this->name, 'Cest') && substr($this->name, -4) === 'Cest') {
            return $this->name;
        }

        return $this->name . 'Cest';
    }

    /**
     * Getter for the Test Annotations
     *
     * @return array
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * Returns hooks.
     *
     * @return TestHookObject[]
     */
    public function getHooks()
    {
        return $this->hooks;
    }

    /**
     * Returns the estimated duration of a single test (including before/after actions).
     *
     * @return integer
     */
    public function getEstimatedDuration()
    {
        // a skipped action results in a single skip being appended to the beginning of the test and no execution
        if ($this->isSkipped()) {
            return 1;
        }

        $hookTime = 0;
        foreach ([TestObjectExtractor::TEST_BEFORE_HOOK, TestObjectExtractor::TEST_AFTER_HOOK] as $hookName) {
            if (array_key_exists($hookName, $this->hooks)) {
                $hookTime += $this->calculateWeightedActionTimes($this->hooks[$hookName]->getActions());
            }
        }

        $testTime = $this->calculateWeightedActionTimes($this->getOrderedActions());

        if ($this->hasWebApiAuthAction) {
            return $hookTime + $testTime + self::WEBAPI_AUTH_TEST_ACTION_WEIGHT;
        } else {
            return $hookTime + $testTime;
        }
    }

    /**
     * Function to return credentials
     * @return array
     */
    public function getCredentials()
    {
        $requiredCredentials = [];
        foreach ($this->hooks as $hookObject) {
            foreach ($hookObject->getActions() as $action) {
                if (isset($action->getCustomActionAttributes()['requiredCredentials'])
                    && !empty($action->getCustomActionAttributes()['requiredCredentials'])) {
                    $requiredCredentials[] = $action->getCustomActionAttributes()['requiredCredentials'];
                }
            }
        }
        foreach ($this->getOrderedActions() as $action) {
            if (isset($action->getCustomActionAttributes()['requiredCredentials'])
                && !empty($action->getCustomActionAttributes()['requiredCredentials'])) {
                $requiredCredentials[] = $action->getCustomActionAttributes()['requiredCredentials'];
            }
        }
        return array_unique($requiredCredentials);
    }
    /**
     * Function which takes a set of actions and estimates time for completion based on action type.
     *
     * @param ActionObject[] $actions
     * @return integer
     */
    private function calculateWeightedActionTimes($actions)
    {
        $actionTime = 0;
        // search for any actions of special type
        foreach ($actions as $action) {
            /** @var ActionObject $action */

            if (!$this->hasWebApiAuthAction && in_array($action->getType(), self::WEBAPI_AUTH_TEST_ACTIONS)) {
                $this->hasWebApiAuthAction = true;
            }

            if (array_key_exists($action->getType(), self::TEST_ACTION_WEIGHT)) {
                $weight = self::TEST_ACTION_WEIGHT[$action->getType()];
                if ($weight === self::WAIT_TIME_ATTRIBUTE) {
                    $weight = intval($action->getCustomActionAttributes()[$weight]) * 1000;
                }

                $actionTime += $weight;
                continue;
            }

            $actionTime += 50;
        }

        return $actionTime;
    }

    /**
     * Method to return the value(s) of a corresponding annotation such as group.
     *
     * @param string $name
     * @return array
     */
    public function getAnnotationByName($name)
    {
        if (array_key_exists($name, $this->annotations)) {
            return $this->annotations[$name];
        }

        return [];
    }

    /**
     * Getter for the custom data
     * @return array|null
     * @deprecated because no usages where found and property does not exist. Will be removed next major release.
     */
    public function getCustomData()
    {
        return null;
    }

    /**
     * This method calls a function to merge custom steps and returns the resulting ordered set of steps.
     *
     * @return array
     */
    public function getOrderedActions()
    {
        if ($this->cachedOrderedActions === null) {
            $mergeUtil = new ActionMergeUtil($this->getName(), "Test");
            $this->cachedOrderedActions = $mergeUtil->resolveActionSteps($this->parsedSteps);
        }

        return $this->cachedOrderedActions;
    }

    /**
     * This method returns currently parsed steps
     *
     * @return array
     */
    public function getUnresolvedSteps()
    {
        return $this->parsedSteps;
    }

    /**
     * Get information about actions and steps in test.
     *
     * @return array
     */
    public function getDebugInformation()
    {
        $debugInformation = [];
        $orderList = $this->getOrderedActions();

        foreach ($orderList as $action) {
            $debugInformation[] = "\t" . $action->getType() . ' ' . $action->getStepKey();
        }

        return $debugInformation;
    }
}
