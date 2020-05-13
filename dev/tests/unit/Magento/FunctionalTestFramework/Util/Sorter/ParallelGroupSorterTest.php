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
    public function testBasicTestGroupSort()
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
    public function testSortWithSuites()
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
}
