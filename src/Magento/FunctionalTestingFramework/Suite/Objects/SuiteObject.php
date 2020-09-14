<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Suite\Objects;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Filter\FilterInterface;
use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

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
     * Filename of where the suite came from
     *
     * @var string
     */
    private $filename;

    /**
     * SuiteObject constructor.
     * @param string           $name
     * @param TestObject[]     $includeTests
     * @param TestObject[]     $excludeTests
     * @param TestHookObject[] $hooks
     * @param string           $filename
     */
    public function __construct($name, $includeTests, $excludeTests, $hooks, $filename = null)
    {
        $this->name = $name;
        $this->includeTests = $includeTests;
        $this->excludeTests = $excludeTests;
        $this->hooks = $hooks;
        $this->filename = $filename;
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
     * @throws TestFrameworkException
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
     * @throws TestFrameworkException
     */
    private function resolveTests($includeTests, $excludeTests)
    {
        $finalTestList = $includeTests;
        $matchingTests = array_intersect(array_keys($includeTests), array_keys($excludeTests));

        // filter out tests for exclusion here
        foreach ($matchingTests as $testName) {
            unset($finalTestList[$testName]);
        }

        $filters = MftfApplicationConfig::getConfig()->getFilterList()->getFilters();
        /** @var FilterInterface $filter */
        foreach ($filters as $filter) {
            $filter->filter($finalTestList);
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

    /**
     * Getter for the Suite Filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }
}
