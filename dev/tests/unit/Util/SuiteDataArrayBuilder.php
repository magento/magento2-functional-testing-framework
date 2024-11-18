<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Util;

use Magento\FunctionalTestingFramework\Suite\Util\SuiteObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\ActionObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;

class SuiteDataArrayBuilder
{
    /**
     * Mock test name
     *
     * @var string
     */
    private $name;

    /**
     * Mock before action name
     *
     * @var string
     */
    private $testActionBeforeName = 'testActionBefore';

    /**
     * Mock after action name
     *
     * @var string
     */
    private $testActionAfterName = 'testActionAfter';

    /**
     * Array containing before hook actions
     *
     * @var array
     */
    private $beforeHook = [];

    /**
     * Arrat containing after hook actions
     *
     * @var array
     */
    private $afterHook = [];

    /**
     * Array which contains tests, groups, modules included as part of suite
     *
     * @var array
     */
    private $includes = [];

    /**
     * Array which contains, tests, groups, module excluded from suite
     *
     * @var array
     */
    private $excludes = [];

    /**
     * Mock test action type
     *
     * @var string
     */
    public $testActionType = 'testAction';

    /**
     * Function which sets the name of the mock suite array
     *
     * @param string $name
     * @return $this
     */
    public function withName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Function which takes an array of test names and formats them as included raw suite data
     *
     * @param array $tests
     * @return $this
     */
    public function includeTests($tests)
    {
        $this->includes = $this->appendEntriesToSuiteContents($this->includes, 'test', $tests);
        return $this;
    }

    /**
     * Function which takes an array of test names and formats them as excluded raw suite data
     *
     * @param array $tests
     * @return $this
     */
    public function excludeTests($tests)
    {
        $this->excludes = $this->appendEntriesToSuiteContents($this->excludes, 'test', $tests);
        return $this;
    }

    /**
     * Function which takes an array of group names and formats them as included raw suite data
     *
     * @param array $groups
     * @return $this
     */
    public function includeGroups($groups)
    {
        $this->includes = $this->appendEntriesToSuiteContents($this->includes, 'group', $groups);
        return $this;
    }

    /**
     * Function which takes an array of group names and formats them as excluded raw suite data
     *
     * @param array $groups
     * @return $this
     */
    public function excludeGroups($groups)
    {
        $this->excludes = $this->appendEntriesToSuiteContents($this->excludes, 'groups', $groups);
        return $this;
    }

    /**
     * Function which takes an array of module names and formats them as included raw suite data
     *
     * @param array $modules
     * @return $this
     */
    public function includeModules($modules)
    {
        $this->includes = $this->appendEntriesToSuiteContents($this->includes, 'module', $modules);
        return $this;
    }

    /**
     * Function which takes an array of group names and formats them as excluded raw suite data
     *
     * @param array $modules
     * @return $this
     */
    public function excludeModules($modules)
    {
        $this->excludes = $this->appendEntriesToSuiteContents($this->excludes, 'module', $modules);
        return $this;
    }

    /**
     * Function which takes an array of current include/exclude contents, a type (group, module, or test) and contents
     * to be appended to the array and returns a propelry formatted array representative of parsed suite data.
     *
     * @param array $currentContents
     * @param string $type
     * @param array $contents
     * @return array
     */
    private function appendEntriesToSuiteContents($currentContents, $type, $contents)
    {
        $newContents = $currentContents;
        foreach ($contents as $entry) {
            $newContents[$entry] = [
                SuiteObjectExtractor::NODE_NAME => $type,
                SuiteObjectExtractor::NAME => $entry
            ];
        }

        return $newContents;
    }

    /**
     * Add an after hook passed in by arg (or default if no arg)
     *
     * @param null $afterHook
     * @return $this
     */
    public function withAfterHook(?array $afterHook = null)
    {
        if ($afterHook === null) {
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
     * Add a before hook passed in by arg (or default if no arg)
     *
     * @param null $beforeHook
     * @return $this
     */
    public function withBeforeHook(?array $beforeHook = null)
    {
        if ($beforeHook === null) {
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
     * Function which takes all class properties set and generates an array representing suite data as parsed from xml.
     *
     * @return array
     */
    public function build()
    {
        return ['suites' => [
            $this->name => [
                SuiteObjectExtractor::NAME => $this->name,
                TestObjectExtractor::TEST_BEFORE_HOOK => $this->beforeHook,
                TestObjectExtractor::TEST_AFTER_HOOK => $this->afterHook,
                SuiteObjectExtractor::INCLUDE_TAG_NAME => $this->includes,
                SuiteObjectExtractor::EXCLUDE_TAG_NAME => $this->excludes
            ]
        ]];
    }
}
