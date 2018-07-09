<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Util;

use Magento\FunctionalTestingFramework\Test\Util\ActionObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\AnnotationExtractor;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;

class TestDataArrayBuilder
{
    /**
     * Mock test name
     *
     * @var string
     */
    public $testName = 'testTest';

    /**
     * Mock file name
     *
     * @var string
     */
    public $filename = null;

    /**
     * Mock before action name
     *
     * @var string
     */
    public $testActionBeforeName = 'testActionBefore';

    /**
     * Mock after action name
     *
     * @var string
     */
    public $testActionAfterName = 'testActionAfter';

    /**
     * Mock failed action name
     *
     * @var string
     */
    public $testActionFailedName = 'testActionFailed';

    /**
     * Mock test action in test name
     *
     * @var string
     */
    public $testTestActionName = 'testActionInTest';

    /**
     * Mock test action type
     *
     * @var string
     */
    public $testActionType = 'testAction';

    /**
     * @var array
     */
    private $annotations = [];

    /**
     * @var array
     */
    private $beforeHook = [];

    /**
     * @var array
     */
    private $afterHook = [];

    /**
     * @var array
     */
    private $failedHook = [];

    /**
     * @var array
     */
    private $testActions = [];

    /**
     * @var array
     */
    private $testReference = null;

    /**
     * @param string $name
     * @return $this
     */
    public function withName($name)
    {
        $this->testName = $name;
        return $this;
    }

    /**
     * Add annotations passed in by arg (or default if no arg)
     *
     * @param array $annotations
     * @return $this
     */
    public function withAnnotations($annotations = null)
    {
        if ($annotations == null) {
            $this->annotations = ['group' => [['value' => 'test']]];
        } else {
            $this->annotations = $annotations;
        }

        return $this;
    }

    /**
     * Add a before hook passed in by arg (or default if no arg)
     *
     * @param null $beforeHook
     * @return $this
     */
    public function withBeforeHook($beforeHook = null)
    {
        if ($beforeHook == null) {
            $this->beforeHook = [$this->testActionBeforeName => [
                ActionObjectExtractor::NODE_NAME => $this->testActionType,
                ActionObjectExtractor::TEST_STEP_MERGE_KEY => $this->testActionBeforeName
            ]];
        } else {
            $this->beforeHook = $beforeHook;
        }

        return $this;
    }

    /**
     * Add an after hook passed in by arg (or default if no arg)
     *
     * @param null $afterHook
     * @return $this
     */
    public function withAfterHook($afterHook = null)
    {
        if ($afterHook == null) {
            $this->afterHook = [$this->testActionAfterName => [
                    ActionObjectExtractor::NODE_NAME => $this->testActionType,
                    ActionObjectExtractor::TEST_STEP_MERGE_KEY => $this->testActionAfterName

            ]];
        } else {
            $this->afterHook = $afterHook;
        }

        return $this;
    }

    /**
     * Add a failed hook passed in by arg (or default if no arg)
     *
     * @param null $failedHook
     * @return $this
     */
    public function withFailedHook($failedHook = null)
    {
        if ($failedHook == null) {
            $this->failedHook = [$this->testActionFailedName => [
                ActionObjectExtractor::NODE_NAME => $this->testActionType,
                ActionObjectExtractor::TEST_STEP_MERGE_KEY => $this->testActionFailedName

            ]];
        } else {
            $this->failedHook = $failedHook;
        }

        return $this;
    }

    /**
     * Add test actions passed in by arg (or default if no arg)
     *
     * @param array $actions
     * @return $this
     */
    public function withTestActions($actions = null)
    {
        if ($actions == null) {
            $this->testActions = [$this->testTestActionName => [
                ActionObjectExtractor::NODE_NAME => $this->testActionType,
                ActionObjectExtractor::TEST_STEP_MERGE_KEY => $this->testTestActionName
            ]];
        } else {
            $this->testActions = $actions;
        }

        return $this;
    }

    /**
     * Add file name passe in by arg (or default if no arg)
     * @param string $filename
     * @return $this
     */
    public function withFileName($filename = null)
    {
        if ($filename == null) {
            $this->filename =
                "/magento2-functional-testing-framework/dev/tests/verification/TestModule/Test/BasicFunctionalTest.xml";
        } else {
            $this->filename = $filename;
        }

        return $this;
    }

    /**
     * Add test reference passed in by arg (or default if no arg)
     *
     * @param string $reference
     * @return $this
     */
    public function withTestReference($reference = null)
    {
        if ($reference != null) {
            $this->testReference = $reference;
        }

        return $this;
    }

    /**
     * Output the resulting test data array based on parameters set in the object
     *
     * @return array
     */
    public function build()
    {
        // return a static data array representing a single test
        return [$this->testName => array_merge(
            [
                TestObjectExtractor::NAME => $this->testName,
                TestObjectExtractor::TEST_ANNOTATIONS => $this->annotations,
                TestObjectExtractor::TEST_BEFORE_HOOK => $this->beforeHook,
                TestObjectExtractor::TEST_AFTER_HOOK => $this->afterHook,
                TestObjectExtractor::TEST_FAILED_HOOK => $this->failedHook,
                "filename" => $this->filename,
                "extends" => $this->testReference
            ],
            $this->testActions
        )];
    }
}
