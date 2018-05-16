<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Objects;

use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Util\ActionMergeUtil;
use Magento\FunctionalTestingFramework\Test\Util\ActionObjectExtractor;

/**
 * Class TestObject
 */
class TestObject
{
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
     * @var String
     */
    private $filename;

    /**
     * String of parent test
     *
     * @var String
     */
    private $parentTest;

    /**
     * TestObject constructor.
     *
     * @param string $name
     * @param ActionObject[] $parsedSteps
     * @param array $annotations
     * @param TestHookObject[] $hooks
     * @param String $filename
     * @param String $parentTest
     */
    public function __construct($name, $parsedSteps, $annotations, $hooks, $filename = null, $parentTest = null)
    {
        $this->name = $name;
        $this->parsedSteps = $parsedSteps;
        $this->annotations = $annotations;
        $this->hooks = $hooks;
        $this->filename = $filename;
        $this->parentTest = $parentTest;
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
     * Getter for the skip_test boolean
     *
     * @return string
     */
    public function isSkipped()
    {
        if (array_key_exists('group', $this->annotations) && (in_array("skip", $this->annotations['group']))) {
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
        if (strpos($this->name, 'Cest') && substr($this->name, -4) == 'Cest') {
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
        // if this test is skipped we do not want any before/after actions to generate as the tests will not run
        if ($this->isSkipped()) {
            return [];
        }
        return $this->hooks;
    }

    /**
     * Returns the number of a test actions contained within a single test (including before/after actions).
     *
     * @return int
     */
    public function getTestActionCount()
    {
        // a skipped action results in a single skip being appended to the beginning of the test and no execution
        if ($this->isSkipped()) {
            return 1;
        }

        $hookActions = 0;
        if (array_key_exists('before', $this->hooks)) {
            $hookActions += count($this->hooks['before']->getActions());
        }

        if (array_key_exists('after', $this->hooks)) {
            $hookActions += count($this->hooks['after']->getActions());
        }

        $testActions = count($this->getOrderedActions());
        return $hookActions + $testActions;
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
        return $this->customData;
    }

    /**
     * This method calls a function to merge custom steps and returns the resulting ordered set of steps.
     *
     * @return array
     */
    public function getOrderedActions()
    {
        $mergeUtil = new ActionMergeUtil($this->getName(), "Test");
        return $mergeUtil->resolveActionSteps($this->parsedSteps);
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
     * This method sets the test as skipped
     *
     * @return void
     */
    public function skipTest()
    {
        if (array_key_exists('group', $this->annotations) && !in_array('skip', $this->annotations['group'])) {
            array_push($this->annotations['group'], 'skip');
        } elseif (!array_key_exists('group', $this->annotations)) {
            $this->annotations['group'] = ['skip'];
        }
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
