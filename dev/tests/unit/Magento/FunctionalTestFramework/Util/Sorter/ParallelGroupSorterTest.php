<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Magento\FunctionalTestFramework\Util\Sorter;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\Sorter\ParallelGroupSorter;
use tests\unit\Util\MagentoTestCase;

class ParallelGroupSorterTest extends MagentoTestCase
{
    /**
     * Test a basic sort of available tests based on size
     */
    public function testBasicTestGroupSplitByTime()
    {
        $sampleTestArray = [
            'test1' => 100,
            'test2' => 300,
            'test3' => 50,
            'test4' => 60,
            'test5' => 25,
            'test6' => 125,
            'test7' => 250,
            'test8' => 1,
            'test9' => 80,
            'test10' => 25
        ];

        $expectedResult = [
            1 => ['test2'],
            2 => ['test7'],
            3 => ['test6', 'test4', 'test8'],
            4 => ['test1', 'test9'],
            5 => ['test3', 'test5', 'test10']
        ];

        $testSorter = new ParallelGroupSorter();
        $actualResult = $testSorter->getTestsGroupedBySize([], $sampleTestArray, 200);

        $this->assertCount(5, $actualResult);

        foreach ($actualResult as $gropuNumber => $actualTests) {
            $expectedTests = $expectedResult[$gropuNumber];
            $this->assertEquals($expectedTests, array_keys($actualTests));
        }
    }

    /**
     * Test a sort of both tests and a suite which is larger than the given line limitation
     */
    public function testTestsAndSuitesSplitByTime()
    {
        // mock tests for test object handler.
        $numberOfCalls = 0;
        $mockTest1 = AspectMock::double(
            TestObject::class,
            ['getEstimatedDuration' => function () use (&$numberOfCalls) {
                $actionCount = [300, 275];
                $result = $actionCount[$numberOfCalls];
                $numberOfCalls++;

                return $result;
            }]
        )->make();

        $mockHandler = AspectMock::double(
            TestObjectHandler::class,
            ['getObject' => function () use ($mockTest1) {
                    return $mockTest1;
            }]
        )->make();

        AspectMock::double(TestObjectHandler::class, ['getInstance' => $mockHandler])->make();

        // create test to size array
        $sampleTestArray = [
            'test1' => 100,
            'test2' => 300,
            'test3' => 500,
            'test4' => 60,
            'test5' => 125
        ];

        // create mock suite references
        $sampleSuiteArray = [
            'mockSuite1' => ['mockTest1', 'mockTest2']
        ];

        // perform sort
        $testSorter = new ParallelGroupSorter();
        $actualResult = $testSorter->getTestsGroupedBySize($sampleSuiteArray, $sampleTestArray, 500);

        // verify the resulting groups
        $this->assertCount(5, $actualResult);

        $expectedResults =  [
            1 => ['mockSuite1_0_G'],
            2 => ['mockSuite1_1_G'],
            3 => ['test3'],
            4 => ['test2','test5', 'test4'],
            5 => ['test1'],
        ];

        foreach ($actualResult as $groupNum => $group) {
            $this->assertEquals($expectedResults[$groupNum], array_keys($group));
        }
    }

    /**
     * Test a basic sort of available tests based on size
     */
    public function testBasicTestGroupSplitByGroup()
    {
        $sampleTestArray = [
            'test1' => 100,
            'test2' => 300,
            'test3' => 50,
            'test4' => 60,
            'test5' => 25,
            'test6' => 125,
            'test7' => 250,
            'test8' => 1,
            'test9' => 80,
            'test10' => 25,
            'test11' => 89,
            'test12' => 69,
            'test13' => 23,
            'test14' => 15,
            'test15' => 25,
            'test16' => 71,
            'test17' => 67,
            'test18' => 34,
            'test19' => 45,
            'test20' => 58,
            'test21' => 9,
        ];

        $expectedResult = [
            0 => ['test2', 'test8'],
            1 => ['test11', 'test9', 'test17', 'test19', 'test13'],
            2 => ['test7', 'test18', 'test14', 'test21'],
            3 => ['test6', 'test12', 'test20', 'test5', 'test10'],
            4 => ['test1', 'test16', 'test4', 'test3', 'test15']
        ];

        $testSorter = new ParallelGroupSorter();
        $actualResult = $testSorter->getTestsGroupedByFixedGroupCount([], $sampleTestArray, 5);

        $this->assertCount(5, $actualResult);

        foreach ($actualResult as $gropuNumber => $actualTests) {
            $expectedTests = $expectedResult[$gropuNumber];
            $this->assertEquals($expectedTests, array_keys($actualTests));
        }
    }

    /**
     * Test a sort of both tests and a suite which is larger than the given line limitation
     */
    public function testTestsAndSuitesSplitByGroup()
    {
        // mock tests for test object handler.
        $numberOfCalls = 0;
        $mockTest1 = AspectMock::double(
            TestObject::class,
            ['getEstimatedDuration' => function () use (&$numberOfCalls) {
                $actionCount = [300, 275];
                $result = $actionCount[$numberOfCalls];
                $numberOfCalls++;

                return $result;
            }]
        )->make();

        $mockHandler = AspectMock::double(
            TestObjectHandler::class,
            ['getObject' => function () use ($mockTest1) {
                return $mockTest1;
            }]
        )->make();

        AspectMock::double(TestObjectHandler::class, ['getInstance' => $mockHandler])->make();

        // create test to size array
        $sampleTestArray = [
            'test1' => 1,
            'test2' => 125,
            'test3' => 35,
            'test4' => 111,
            'test5' => 43,
            'test6' => 321,
            'test7' => 260,
            'test8' => 5,
            'test9' => 189,
            'test10' => 246,
            'test11' => 98,
            'test12' => 96,
            'test13' => 232,
            'test14' => 51,
            'test15' => 52,
            'test16' => 127,
            'test17' => 76,
            'test18' => 43,
            'test19' => 154,
            'test20' => 85,
            'test21' => 219,
            'test22' => 87,
            'test23' => 65,
            'test24' => 216,
            'test25' => 271,
            'test26' => 99,
            'test27' => 102,
            'test28' => 179,
            'test29' => 243,
            'test30' => 93,
            'test31' => 330,
            'test32' => 85,
            'test33' => 291,
        ];

        // create mock suite references
        $sampleSuiteArray = [
            'mockSuite1' => ['mockTest1', 'mockTest2']
        ];

        // perform sort
        $testSorter = new ParallelGroupSorter();
        $actualResult = $testSorter->getTestsGroupedByFixedGroupCount($sampleSuiteArray, $sampleTestArray, 15);

        // verify the resulting groups
        $this->assertCount(15, $actualResult);

        $expectedResults =  [
            0 => ['test31', 'test8', 'test1'],
            1 => ['test6', 'test5'],
            2 => ['test33', 'test17'],
            3 => ['test25', 'test32'],
            4 => ['test7', 'test22'],
            5 => ['test10', 'test30'],
            6 => ['test29', 'test12'],
            7 => ['test13', 'test11', 'test3'],
            8 => ['test21', 'test26', 'test14'],
            9 => ['test24', 'test27', 'test18'],
            10 => ['test9', 'test4', 'test23'],
            11 => ['test28', 'test2', 'test15'],
            12 => ['test19', 'test16', 'test20'],
            13 => ['mockSuite1_0_G'],
            14 => ['mockSuite1_1_G'],
        ];

        foreach ($actualResult as $groupNum => $group) {
            $this->assertEquals($expectedResults[$groupNum], array_keys($group));
        }
    }
}
