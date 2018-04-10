<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Util\Sorter;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;

class ParallelGroupSorter
{
    /**
     * An array of newly split suite object names mapped to their corresponding objects.
     *
     * @var array
     */
    private $suiteConfig = [];

    /**
     * ParallelGroupSorter constructor.
     */
    public function __construct()
    {
        // empty constructor
    }

    /**
     * Function which returns tests and suites split according to desired number of lines divded into groups.
     *
     * @param array $suiteConfiguration
     * @param array $testNameToSize
     * @param integer $lines
     * @return array
     * @throws TestFrameworkException
     */
    public function getTestsGroupedBySize($suiteConfiguration, $testNameToSize, $lines)
    {
        // we must have the lines argument in order to create the test groups
        if ($lines == 0) {
            throw new TestFrameworkException(
                "Please provide the argument '--lines' to the robo command in order to".
                " generate grouped tests manifests for a parallel execution"
            );
        }

        $testGroups = [];
        $splitSuiteNamesToTests = $this->createGroupsWithinSuites($suiteConfiguration, $lines);
        $splitSuiteNamesToSize = $this->getSuiteToSize($splitSuiteNamesToTests);
        $entriesForGeneration = array_merge($testNameToSize, $splitSuiteNamesToSize);
        arsort($entriesForGeneration);

        $testNameToSizeForUse = $entriesForGeneration;
        $nodeNumber = 1;
        foreach ($entriesForGeneration as $testName => $testSize) {
            if (!array_key_exists($testName, $testNameToSizeForUse)) {
                // skip tests which have already been added to a group
                continue;
            }

            $testGroup = $this->createTestGroup($lines, $testName, $testSize, $testNameToSizeForUse);
            $testGroups[$nodeNumber] = $testGroup;

            // unset the test which have been used.
            $testNameToSizeForUse = array_diff($testNameToSizeForUse, $testGroup);
            $nodeNumber++;
        }

        return $testGroups;
    }

    /**
     * Function which returns the newly formed suite objects created as a part of the sort
     *
     * @return array
     */
    public function getResultingSuiteConfig()
    {
        if (empty($this->suiteConfig)) {
            return null;
        }

        return $this->suiteConfig;
    }

    /**
     * Function which constructs a group of tests to be run together based on the desired number of lines per group,
     * a test to be used as a starting point, the size of a starting test, an array of tests available to be added to
     * the group.
     *
     * @param integer $lineMaximum
     * @param string $testName
     * @param integer $testSize
     * @param array $testNameToSizeForUse
     * @return array
     */
    private function createTestGroup($lineMaximum, $testName, $testSize, $testNameToSizeForUse)
    {
        $group[$testName] = $testSize;

        if ($testSize < $lineMaximum) {
            while (array_sum($group) < $lineMaximum && !empty($testNameToSizeForUse)) {
                $groupSize = array_sum($group);
                $lineGoal = $lineMaximum - $groupSize;

                $testNameForUse = $this->getClosestLineCount($testNameToSizeForUse, $lineGoal);
                $testSizeForUse = $testNameToSizeForUse[$testNameForUse];
                unset($testNameToSizeForUse[$testNameForUse]);

                $group[$testNameForUse] = $testSizeForUse;
            }
        }

        return $group;
    }

    /**
     * Function which takes a group of available tests mapped to size and a desired number of lines matching with the
     * test of closest size and returning.
     *
     * @param array $testGroup
     * @param integer $desiredValue
     * @return string
     */
    private function getClosestLineCount($testGroup, $desiredValue)
    {
        $winner = key($testGroup);
        $closestThreshold = $desiredValue;
        foreach ($testGroup as $testName => $testValue) {
            $testThreshold =  abs($desiredValue - $testValue);
            if ($closestThreshold > $testThreshold) {
                $closestThreshold = $testThreshold;
                $winner = $testName;
            }
        }

        return $winner;
    }

    /**
     * Function which takes an array of test names mapped to suite name and a size limitation for each group of tests.
     * The function divides suites that are over the specified limit and returns the resulting suites in an array.
     *
     * @param array $suiteConfiguration
     * @param integer $lineLimit
     * @return array
     */
    private function createGroupsWithinSuites($suiteConfiguration, $lineLimit)
    {
        $suiteNameToTestSize = $this->getSuiteNameToTestSize($suiteConfiguration);
        $suiteNameToSize = $this->getSuiteToSize($suiteNameToTestSize);

        // divide the suites up within the array
        $suitesForResize = array_filter($suiteNameToSize, function ($val) use ($lineLimit) {
            return $val > $lineLimit;
        });

        // remove the suites for resize from the original list
        $remainingSuites = array_diff_key($suiteNameToTestSize, $suitesForResize);

        foreach ($remainingSuites as $remainingSuite => $tests) {
            $this->addSuiteToConfig($remainingSuite, null, $tests);
        }

        $resultingGroups = [];
        foreach ($suitesForResize as $suiteName => $suiteSize) {
            $resultingGroups = array_merge(
                $resultingGroups,
                $this->splitTestSuite($suiteName, $suiteNameToTestSize[$suiteName], $lineLimit)
            );
        }

        // merge the resulting divisions with the appropriately sized suites
        return array_merge($remainingSuites, $resultingGroups);
    }

    /**
     * Function which takes the given suite configuration and returns an array of suite to test size.
     *
     * @param array $suiteConfiguration
     * @return array
     */
    private function getSuiteNameToTestSize($suiteConfiguration)
    {
        $suiteNameToTestSize = [];
        foreach ($suiteConfiguration as $suite => $test) {
            foreach ($test as $testName) {
                $suiteNameToTestSize[$suite][$testName] = TestObjectHandler::getInstance()
                    ->getObject($testName)
                    ->getTestActionCount();
            }
        }

        return $suiteNameToTestSize;
    }

    /**
     * Function which takes a multidimensional array containing a suite name mapped to an array of tests names as keys
     * with their sizes as values. The function returns an array of suite name to size of the corresponding mapped
     * tests.
     *
     * @param array $suiteNamesToTests
     * @return array
     */
    private function getSuiteToSize($suiteNamesToTests)
    {
        $suiteNamesToSize = [];
        foreach ($suiteNamesToTests as $name => $tests) {
            $size = array_sum($tests);
            $suiteNamesToSize[$name] = $size;
        }

        return $suiteNamesToSize;
    }

    /**
     * Function which takes a suite name, an array of tests affiliated with that suite, and a maximum number of lines.
     * The function uses the limit to split up the oversized suite and returns an array of suites representative of the
     * previously oversized suite.
     *
     * E.g.
     * Input {suitename = 'sample', tests = ['test1' => 100,'test2' => 150, 'test3' => 300], linelimit = 275}
     * Result { ['sample_01' => ['test3' => 300], 'sample_02' => ['test2' => 150, 'test1' => 100]] }
     *
     * @param string $suiteName
     * @param array $tests
     * @param integer $lineLimit
     * @return array
     */
    private function splitTestSuite($suiteName, $tests, $lineLimit)
    {
        arsort($tests);
        $split_suites = [];
        $availableTests = $tests;
        $split_count = 0;

        foreach ($tests as $test => $size) {
            if (!array_key_exists($test, $availableTests)) {
                continue;
            }

            $group = $this->createTestGroup($lineLimit, $test, $size, $availableTests);
            $split_suites["{$suiteName}_${split_count}"] = $group;
            $this->addSuiteToConfig($suiteName, "{$suiteName}_${split_count}", $group);

            $availableTests = array_diff($availableTests, $group);
            $split_count++;
        }

        return $split_suites;
    }

    /**
     * Function which takes a new suite, the original suite from which it was sourced, and an array of tests now
     * associated with thew new suite. The function takes this information and creates a new suite object stored in
     * the sorter for later retrieval, copying the pre/post conditions from the original suite.
     *
     * @param string $originalSuiteName
     * @param string $newSuiteName
     * @param array $tests
     * @return void
     */
    private function addSuiteToConfig($originalSuiteName, $newSuiteName, $tests)
    {
        if ($newSuiteName == null) {
            $this->suiteConfig[$originalSuiteName] = array_keys($tests);
            return;
        }

        $this->suiteConfig[$originalSuiteName][$newSuiteName] = array_keys($tests);
    }
}
