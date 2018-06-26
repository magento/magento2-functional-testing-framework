<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Suite\Objects;

use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;

/**
 * Class SuiteObject
 */
class SuiteObject
{
    /**
     * Name of the Suite.
     *
     * @var string
     */
    private $name;

    /**
     * Array of Tests to include for the suite.
     *
     * @var TestObject[]
     */
    private $includeTests = [];

    /**
     * Array of Tests to exclude for the suite.
     *
     * @var TestObject[]
     */
    private $excludeTests = [];

    /**
     * Array of before/after hooks to be executed for a suite.
     *
     * @var TestHookObject[]
     */
    private $hooks;

    /**
     * SuiteObject constructor.
     * @param string           $name
     * @param TestObject[]     $includeTests
     * @param TestObject[]     $excludeTests
     * @param TestHookObject[] $hooks
     */
    public function __construct($name, $includeTests, $excludeTests, $hooks)
    {
        $this->name = $name;
        $this->includeTests = $includeTests;
        $this->excludeTests = $excludeTests;
        $this->hooks = $hooks;
    }

    /**
     * Getter for suite name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns an array of Test Objects based on specifications in exclude and include arrays.
     *
     * @return array
     */
    public function getTests()
    {
        return $this->resolveTests($this->includeTests, $this->excludeTests);
    }

    /**
     * Takes an array of Test Objects to include and an array of Test Objects to exlucde. Loops through each Test
     * and determines any overlapping tests. Returns a resulting array of Test Objects based on this logic. Exclusion is
     * preferred to exclusiong (i.e. a test is specified in both include and exclude, it will be excluded).
     *
     * @param TestObject[] $includeTests
     * @param TestObject[] $excludeTests
     * @return TestObject[]
     */
    private function resolveTests($includeTests, $excludeTests)
    {
        $finalTestList = $includeTests;
        $matchingTests = array_intersect(array_keys($includeTests), array_keys($excludeTests));

        // filter out tests for exclusion here
        foreach ($matchingTests as $testName) {
            unset($finalTestList[$testName]);
        }

        if (empty($finalTestList)) {
            trigger_error(
                "Current suite configuration for " .
                $this->name . " contains no tests.",
                E_USER_WARNING
            );
        }

        return $finalTestList;
    }

    /**
     * Convenience method for determining if a Suite will require group file generation.
     * A group file will only be generated when the user specifies a before/after statement.
     *
     * @return boolean
     */
    public function requiresGroupFile()
    {
        return !empty($this->hooks);
    }

    /**
     * Getter for the Hook Array which contains the before/after objects.
     *
     * @return TestHookObject[]
     */
    public function getHooks()
    {
        return $this->hooks;
    }

    /**
     * Getter for before hooks.
     *
     * @return TestHookObject
     */
    public function getBeforeHook()
    {
        return $this->hooks['before'] ?? null;
    }

    /**
     * Getter for after hooks.
     *
     * @return TestHookObject
     */
    public function getAfterHook()
    {
        return $this->hooks['after'] ?? null;
    }
}
