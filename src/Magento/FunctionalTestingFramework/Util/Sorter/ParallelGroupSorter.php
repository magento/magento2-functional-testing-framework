<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Util\Sorter;

use Magento\FunctionalTestingFramework\Exceptions\FastFailException;
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
     * Function which returns tests and suites split according to desired number of lines divided into groups.
     *
     * @param array   $suiteConfiguration
     * @param array   $testNameToSize
     * @param integer $time
     * @return array
     * @throws FastFailException
     */
    public function getTestsGroupedBySize($suiteConfiguration, $testNameToSize, $time)
    {
        // we must have the lines argument in order to create the test groups
        if ($time == 0) {
            throw new FastFailException(
                "Please provide the argument '--time' to the robo command in order to".
                " generate grouped tests manifests for a parallel execution"
            );
        }

        $testGroups = [];
        $splitSuiteNamesToTests = $this->createGroupsWithinSuites($suiteConfiguration, $time);
        $splitSuiteNamesToSize = $this->getSuiteToSize($splitSuiteNamesToTests);
        arsort($testNameToSize);
        arsort($splitSuiteNamesToSize);

        $testNameToSizeForUse = $testNameToSize;
        $nodeNumber = 1;

        foreach ($splitSuiteNamesToSize as $testName => $testSize) {
            $testGroups[$nodeNumber] = [$testName => $testSize];
            $nodeNumber++;
        }

        foreach ($testNameToSize as $testName => $testSize) {
            if (!array_key_exists($testName, $testNameToSizeForUse)) {
                // skip tests which have already been added to a group
                continue;
            }

            $testGroup = $this->createTestGroup($time, $testName, $testSize, $testNameToSizeForUse);
            $testGroups[$nodeNumber] = $testGroup;

            // unset the test which have been used.
            $testNameToSizeForUse = array_diff_key($testNameToSizeForUse, $testGroup);
            $nodeNumber++;
        }

        return $testGroups;
    }

    /**
     * Function which returns tests and suites split according to desired number of groups.
     *
     * @param array   $suiteConfiguration
     * @param array   $testNameToSize
     * @param integer $groupTotal
     * @return array
     * @throws FastFailException
     */
    public function getTestsGroupedByFixedGroupCount($suiteConfiguration, $testNameToSize, $groupTotal)
    {
        if (empty($suiteConfiguration)) {
            return $this->convertArrayIndexStartingAtOne($this->splitTestsIntoGroups($testNameToSize, $groupTotal));
        }

        $suiteNameToTestSize = $this->getSuiteNameToTestSize($suiteConfiguration);

        // Calculate suite group totals
        $suiteNameToGroupCount = $this->getSuiteGroupCounts($suiteNameToTestSize, $testNameToSize, $groupTotal);

        $suitesGroupTotal = array_sum($suiteNameToGroupCount);

        // Calculate minimum required groups
        $minSuiteGroupTotal = count($suiteNameToTestSize);
        $minTestGroupTotal = empty($testNameToSize) ? 0 : 1;
        $minRequiredGroupTotal = $minSuiteGroupTotal + $minTestGroupTotal;

        if ($groupTotal < $minRequiredGroupTotal) {
            throw new FastFailException(
                "Invalid parameter 'groupTotal': must be equal or greater than {$minRequiredGroupTotal}"
            );
        } elseif ($groupTotal < $suitesGroupTotal + $minTestGroupTotal) {
            // Split in savvy mode when $groupTotal requested is very small
            $testGroupTotal = $minTestGroupTotal;
            // Reduce suite group total
            $suiteNameToGroupCount = $this->reduceSuiteGroupTotal(
                $suiteNameToGroupCount,
                $groupTotal - $minTestGroupTotal
            );
        } else {
            // Calculate test group total
            $testGroupTotal = $groupTotal - $suitesGroupTotal;
        }

        // Split tests and suites
        $testGroups = $this->splitTestsIntoGroups($testNameToSize, $testGroupTotal);
        $testGroups = array_merge(
            $testGroups,
            $this->splitSuitesIntoGroups($suiteNameToTestSize, $suiteNameToGroupCount)
        );

        return $this->convertArrayIndexStartingAtOne($testGroups);
    }

    /**
     * Return suite's group counts from a group total
     *
     * @param array   $suiteNameToTestSize
     * @param array   $testNameToSize
     * @param integer $groupTotal
     * @return array
     */
    private function getSuiteGroupCounts($suiteNameToTestSize, $testNameToSize, $groupTotal)
    {
        if (empty($suiteNameToTestSize)) {
            return [];
        }

        // Calculate the minimum possible group time
        $suiteNameToSize = $this->getSuiteToSize($suiteNameToTestSize);
        $minGroupTime = ceil((array_sum($testNameToSize) + array_sum($suiteNameToSize)) / $groupTotal);

        // Find maximum suite time
        $maxSuiteTime = max($suiteNameToSize);

        // Calculate 2 possible suite group times
        $ceilSuiteGroupNumber = (int)ceil($maxSuiteTime / $minGroupTime);
        $ceilSuiteGroupTime = max(ceil($maxSuiteTime / $ceilSuiteGroupNumber), $minGroupTime);
        $floorSuiteGroupNumber = (int)floor($maxSuiteTime / $minGroupTime);
        if ($floorSuiteGroupNumber != 0) {
            $floorSuiteGroupTime = max(ceil($maxSuiteTime / $floorSuiteGroupNumber), $minGroupTime);
        }

        // Calculate test group time for ceiling
        $ceilSuiteNameToGroupCount = $this->getSuiteGroupCountFromGroupTime($suiteNameToTestSize, $ceilSuiteGroupTime);
        $ceilSuiteGroupTotal = array_sum($ceilSuiteNameToGroupCount);
        $ceilTestGroupTotal = $groupTotal - $ceilSuiteGroupTotal;

        if ($ceilTestGroupTotal == 0) {
            $ceilTestGroupTime = 0;
        } else {
            $ceilTestGroupTime = ceil(array_sum($testNameToSize) / $ceilTestGroupTotal);
        }

        // Set suite group total to ceiling
        $suiteNameToGroupCount = $ceilSuiteNameToGroupCount;

        if (isset($floorSuiteGroupTime) && $ceilSuiteGroupTime != $floorSuiteGroupTime) {
            // Calculate test group time for floor
            $floorSuiteNameToGroupCount = $this->getSuiteGroupCountFromGroupTime(
                $suiteNameToTestSize,
                $floorSuiteGroupTime
            );
            $floorSuiteGroupTotal = array_sum($floorSuiteNameToGroupCount);
            $floorTestGroupTotal = $groupTotal - $floorSuiteGroupTotal;
            if ($floorTestGroupTotal == 0) {
                $floorTestGroupTime = 0;
            } else {
                $floorTestGroupTime = ceil(array_sum($testNameToSize) / $floorTestGroupTotal);
            }

            // Choose the closer value between test group time and suite group time
            $ceilDiff = abs($ceilTestGroupTime - $ceilSuiteGroupTime);
            $floorDiff = abs($floorTestGroupTime - $floorSuiteGroupTime);
            if ($ceilDiff > $floorDiff) {
                // Adjust suite group total to floor
                $suiteNameToGroupCount = $floorSuiteNameToGroupCount;
            }
        }

        return $suiteNameToGroupCount;
    }

    /**
     * Reduce total suite groups to a given $total.
     * This method will reduce 1 from a suite that's greater than 1 repeatedly until sum of all groups reaches $total.
     *
     * @param array   $suiteNameToGroupCount
     * @param integer $total
     * @return array
     * @throws FastFailException
     */
    private function reduceSuiteGroupTotal($suiteNameToGroupCount, $total)
    {
        if (count($suiteNameToGroupCount) > $total) {
            throw new FastFailException(
                "Invalid parameter 'total': must be equal or greater than {count($suiteNameToGroupCount)}"
            );
        }

        $done = false;
        while (!$done) {
            foreach ($suiteNameToGroupCount as $suite => $count) {
                if (array_sum($suiteNameToGroupCount) == $total) {
                    $done = true;
                    break;
                }
                if ($count > 1) {
                    $suiteNameToGroupCount[$suite] -= 1;
                }
            }
        }

        return $suiteNameToGroupCount;
    }

    /**
     * Return array contains suitename to number of groups to be split based on time.
     *
     * @param array   $suiteNameToTestSize
     * @param integer $time
     * @return array
     */
    private function getSuiteGroupCountFromGroupTime($suiteNameToTestSize, $time)
    {
        $suiteNameToGroupCount = [];
        foreach ($suiteNameToTestSize as $suiteName => $tests) {
            $maxCount = count($tests);
            $suiteTime = array_sum($tests);
            if ($suiteTime <= $time) {
                $suiteNameToGroupCount[$suiteName] = 1;
            } else {
                $suiteNameToGroupCount[$suiteName] = min((int)ceil($suiteTime/$time), $maxCount);
            }
        }
        return $suiteNameToGroupCount;
    }

    /**
     * Split tests into given number of groups.
     *
     * @param array   $tests
     * @param integer $groupCnt
     * @return array
     */
    private function splitTestsIntoGroups($tests, $groupCnt)
    {
        if (empty($tests)) {
            return [];
        }

        // Reverse sort the test array by size
        uasort($tests, function ($a, $b) {
            return $a >= $b ? -1 : 1;
        });
        $groups = array_fill(0, $groupCnt, []);
        $sums = array_fill(0, $groupCnt, 0);

        foreach ($tests as $test => $size) {
            // Always add the next test to the group with the smallest sum
            $key = array_search(min($sums), $sums);
            $groups[$key][$test] = $size;
            $sums[$key] += $size;
        }
        // Filter empty array
        return array_filter($groups);
    }

    /**
     * Split suites into given number of groups.
     *
     * @param array $suiteNameToTestSize
     * @param array $suiteNameToGroupCount
     * @return array
     */
    private function splitSuitesIntoGroups($suiteNameToTestSize, $suiteNameToGroupCount)
    {
        $groups = [];
        foreach ($suiteNameToTestSize as $suiteName => $suiteTests) {
            $suiteCnt = $suiteNameToGroupCount[$suiteName];
            if ($suiteCnt == 1) {
                $groups[][$suiteName] = array_sum($suiteTests);
                $this->addSuiteToConfig($suiteName, null, $suiteTests);
            } elseif ($suiteCnt > 1) {
                $suiteGroups = $this->splitTestsIntoGroups($suiteTests, $suiteCnt);
                foreach ($suiteGroups as $index => $tests) {
                    $newSuiteName = $suiteName . '_' . strval($index) . '_G';
                    $groups[][$newSuiteName] = array_sum($tests);
                    $this->addSuiteToConfig($suiteName, $newSuiteName, $tests);
                }
            }
        }
        return $groups;
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
     * @param integer $timeMaximum
     * @param string  $testName
     * @param integer $testSize
     * @param array   $testNameToSizeForUse
     * @return array
     */
    private function createTestGroup($timeMaximum, $testName, $testSize, $testNameToSizeForUse)
    {
        $group[$testName] = $testSize;

        if ($testSize < $timeMaximum) {
            while (array_sum($group) < $timeMaximum && !empty($testNameToSizeForUse)) {
                $groupSize = array_sum($group);
                $lineGoal = $timeMaximum - $groupSize;

                $testNameForUse = $this->getClosestLineCount($testNameToSizeForUse, $lineGoal);
                if ($testNameToSizeForUse[$testNameForUse] < $lineGoal) {
                    $testSizeForUse = $testNameToSizeForUse[$testNameForUse];
                    $group[$testNameForUse] = $testSizeForUse;
                }

                unset($testNameToSizeForUse[$testNameForUse]);
            }
        }

        return $group;
    }

    /**
     * Function which takes a group of available tests mapped to size and a desired number of lines matching with the
     * test of closest size and returning.
     *
     * @param array   $testGroup
     * @param integer $desiredValue
     * @return string
     */
    private function getClosestLineCount($testGroup, $desiredValue)
    {
        $winner = key($testGroup);
        $closestThreshold = $desiredValue;
        foreach ($testGroup as $testName => $testValue) {
            // find the difference between the desired value and test candidate for the group
            $testThreshold =  $desiredValue - $testValue;

            // if we see that the gap between the desired value is non-negative and lower than the current closest make
            // the test the winner.
            if ($closestThreshold > $testThreshold && $testThreshold > 0) {
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
     * @param array   $suiteConfiguration
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
                    ->getEstimatedDuration();
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
     * Result { ['sample_01_G' => ['test3' => 300], 'sample_02_G' => ['test2' => 150, 'test1' => 100]] }
     *
     * @param string  $suiteName
     * @param array   $tests
     * @param integer $maxTime
     * @return array
     */
    private function splitTestSuite($suiteName, $tests, $maxTime)
    {
        arsort($tests);
        $splitSuites = [];
        $availableTests = $tests;
        $splitCount = 0;

        foreach ($tests as $test => $size) {
            if (!array_key_exists($test, $availableTests)) {
                continue;
            }

            $group = $this->createTestGroup($maxTime, $test, $size, $availableTests);
            $splitSuites["{$suiteName}_{$splitCount}_G"] = $group;
            $this->addSuiteToConfig($suiteName, "{$suiteName}_{$splitCount}_G", $group);

            $availableTests = array_diff_key($availableTests, $group);
            $splitCount++;
        }

        return $splitSuites;
    }

    /**
     * Function which takes a new suite, the original suite from which it was sourced, and an array of tests now
     * associated with thew new suite. The function takes this information and creates a new suite object stored in
     * the sorter for later retrieval, copying the pre/post conditions from the original suite.
     *
     * @param string $originalSuiteName
     * @param string $newSuiteName
     * @param array  $tests
     * @return void
     */
    private function addSuiteToConfig($originalSuiteName, $newSuiteName, $tests)
    {
        if ($newSuiteName === null) {
            $this->suiteConfig[$originalSuiteName] = array_keys($tests);
            return;
        }

        $this->suiteConfig[$originalSuiteName][$newSuiteName] = array_keys($tests);
    }

    /**
     * Convert array index starting at 1
     *
     * @param array $inArray
     * @return array
     */
    private function convertArrayIndexStartingAtOne($inArray)
    {
        $outArray = [];
        $index = 1;
        foreach ($inArray as $value) {
            $outArray[$index] = $value;
            $index += 1;
        }
        return $outArray;
    }
}
