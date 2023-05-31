<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Util\Sorter;

use Magento\FunctionalTestingFramework\Exceptions\FastFailException;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\Sorter\ParallelGroupSorter;
use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;

class ParallelGroupSorterTest extends MagentoTestCase
{
    /**
     * Test a basic sort of available tests based on size.
     *
     * @return void
     * @throws FastFailException
     */
    public function testBasicTestsSplitByTime(): void
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

        foreach ($actualResult as $groupNumber => $actualTests) {
            $expectedTests = $expectedResult[$groupNumber];
            $this->assertEquals($expectedTests, array_keys($actualTests));
        }
    }

    /**
     * Test a sort of both tests and a suite which is larger than the given line limitation.
     *
     * @return void
     * @throws FastFailException
     */
    public function testTestsAndSuitesSplitByTime(): void
    {
        // mock tests for test object handler.
        $this->createMockForTest(0, [300, 275]);

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
     * Test splitting tests based on a fixed group number.
     *
     * @return void
     * @throws FastFailException
     */
    public function testBasicTestsSplitByGroup(): void
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
            'test21' => 9
        ];

        $expectedResult = [
            1 => ['test2', 'test8'],
            2 => ['test7', 'test18', 'test14', 'test21'],
            3 => ['test6', 'test12', 'test20', 'test5', 'test10'],
            4 => ['test1', 'test16', 'test4', 'test3', 'test15'],
            5 => ['test11', 'test9', 'test17', 'test19', 'test13'],
        ];

        $testSorter = new ParallelGroupSorter();
        $actualResult = $testSorter->getTestsGroupedByFixedGroupCount([], $sampleTestArray, 5);
        $this->assertCount(5, $actualResult);

        foreach ($actualResult as $groupNumber => $actualTests) {
            $expectedTests = $expectedResult[$groupNumber];
            $this->assertEquals($expectedTests, array_keys($actualTests));
        }
    }

    /**
     * Test splitting tests based a group number bigger than ever needed.
     *
     * @return void
     * @throws FastFailException
     */
    public function testBasicTestsSplitByBigGroupNumber(): void
    {
        $sampleTestArray = [
            'test1' => 100,
            'test2' => 300,
            'test3' => 50,
            'test4' => 60,
            'test5' => 25
        ];

        $expectedResult = [
            1 => ['test2'],
            2 => ['test1'],
            3 => ['test4'],
            4 => ['test3'],
            5 => ['test5']
        ];

        $testSorter = new ParallelGroupSorter();
        $actualResult = $testSorter->getTestsGroupedByFixedGroupCount([], $sampleTestArray, 10);
        $this->assertCount(5, $actualResult);

        foreach ($actualResult as $groupNumber => $actualTests) {
            $expectedTests = $expectedResult[$groupNumber];
            $this->assertEquals($expectedTests, array_keys($actualTests));
        }
    }

    /**
     * Test splitting tests based a minimum group number.
     *
     * @return void
     * @throws FastFailException
     */
    public function testBasicTestsSplitByMinGroupNumber(): void
    {
        $sampleTestArray = [
            'test1' => 100,
            'test2' => 300,
            'test3' => 50,
            'test4' => 60,
            'test5' => 25,
        ];

        $expectedResult = [
            1 => ['test2', 'test1', 'test4', 'test3', 'test5']
        ];

        $testSorter = new ParallelGroupSorter();
        $actualResult = $testSorter->getTestsGroupedByFixedGroupCount([], $sampleTestArray, 1);
        $this->assertCount(1, $actualResult);

        foreach ($actualResult as $groupNumber => $actualTests) {
            $expectedTests = $expectedResult[$groupNumber];
            $this->assertEquals($expectedTests, array_keys($actualTests));
        }
    }

    /**
     * Test splitting tests and suites based on a fixed group number.
     *
     * @return void
     * @throws FastFailException
     */
    public function testTestsAndSuitesSplitByGroup(): void
    {
        // mock tests for test object handler.
        $this->createMockForTest(0);

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
            'test33' => 291
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
            1 => ['test31', 'test3'],
            2 => ['test6', 'test5'],
            3 => ['test33', 'test17'],
            4 => ['test25', 'test32'],
            5 => ['test7', 'test22'],
            6 => ['test10', 'test30'],
            7 => ['test29', 'test12'],
            8 => ['test13', 'test11', 'test8', 'test1'],
            9 => ['test21', 'test26', 'test14'],
            10 => ['test24', 'test27', 'test18'],
            11 => ['test9', 'test4', 'test23'],
            12 => ['test28', 'test2', 'test15'],
            13 => ['test19', 'test16', 'test20'],
            14 => ['mockSuite1_0_G'],
            15 => ['mockSuite1_1_G']
        ];

        foreach ($actualResult as $groupNum => $group) {
            $this->assertEquals($expectedResults[$groupNum], array_keys($group));
        }
    }

    /**
     * Test splitting tests and suites based a group number bigger than ever needed.
     *
     * @return void
     * @throws FastFailException
     */
    public function testTestsAndSuitesSplitByBigGroupNumber(): void
    {
        // mock tests for test object handler.
        $this->createMockForTest(0);

        // create test to size array
        $sampleTestArray = [
            'test1' => 275,
            'test2' => 190,
            'test3' => 200
        ];

        // create mock suite references
        $sampleSuiteArray = [
            'mockSuite1' => ['mockTest1', 'mockTest2']
        ];

        // perform sort
        $testSorter = new ParallelGroupSorter();
        $actualResult = $testSorter->getTestsGroupedByFixedGroupCount($sampleSuiteArray, $sampleTestArray, 10);
        // verify the resulting groups
        $this->assertCount(5, $actualResult);

        $expectedResults =  [
            1 => ['test1'],
            2 => ['test3'],
            3 => ['test2'],
            4 => ['mockSuite1_0_G'],
            5 => ['mockSuite1_1_G'],
        ];

        foreach ($actualResult as $groupNum => $group) {
            $this->assertEquals($expectedResults[$groupNum], array_keys($group));
        }
    }

    /**
     * Test splitting tests and suites based a minimum group number.
     *
     * @return void
     * @throws FastFailException
     */
    public function testTestsAndSuitesSplitByMinGroupNumber(): void
    {
        // mock tests for test object handler.
        $this->createMockForTest(0);

        // create test to size array
        $sampleTestArray = [
            'test1' => 1,
            'test2' => 125,
            'test3' => 35
        ];

        // create mock suite references
        $sampleSuiteArray = [
            'mockSuite1' => ['mockTest1', 'mockTest2']
        ];

        // perform sort
        $testSorter = new ParallelGroupSorter();
        $actualResult = $testSorter->getTestsGroupedByFixedGroupCount($sampleSuiteArray, $sampleTestArray, 3);
        // verify the resulting groups
        $this->assertCount(3, $actualResult);

        $expectedResults =  [
            1 => ['test2', 'test3', 'test1'],
            2 => ['mockSuite1_0_G'],
            3 => ['mockSuite1_1_G']
        ];

        foreach ($actualResult as $groupNum => $group) {
            $this->assertEquals($expectedResults[$groupNum], array_keys($group));
        }
    }

    /**
     * Test splitting tests and suites when none of the suites has test.
     * For example, this can happen when --filter option is used.
     *
     * @return void
     * @throws FastFailException
     */
    public function testTestsAndSuitesSplitByGroupNumberSuiteNoTest(): void
    {
        // mock tests for test object handler.
        $this->createMockForTest(0);

        // create test to size array
        $sampleTestArray = [
            'test1' => 1,
            'test2' => 125,
            'test3' => 35
        ];

        // create mock suite references
        $sampleSuiteArray = [
            'mockSuite1' => [],
            'mockSuite2' => [],
        ];

        // perform sort
        $testSorter = new ParallelGroupSorter();
        $actualResult = $testSorter->getTestsGroupedByFixedGroupCount($sampleSuiteArray, $sampleTestArray, 3);
        // verify the resulting groups
        $this->assertCount(3, $actualResult);

        $expectedResults =  [
            1 => ['test2'],
            2 => ['test3'],
            3 => ['test1']
        ];

        foreach ($actualResult as $groupNum => $group) {
            $this->assertEquals($expectedResults[$groupNum], array_keys($group));
        }
    }

    /**
     * Test splitting into minimum groups.
     *
     * @return void
     */
    public function testSplitMinGroups(): void
    {
        // mock tests for test object handler.
        $this->createMockForTest(0);

        // create test to size array
        $sampleTestArray = [
            'test1' => 1,
            'test2' => 125,
            'test3' => 35
        ];
        // create mock suite references
        $sampleSuiteArray = [
            'mockSuite1' => ['mockTest1', 'mockTest2'],
            'mockSuite2' => ['mockTest3', 'mockTest4'],
        ];

        // perform sort
        $testSorter = new ParallelGroupSorter();
        $actualResult = $testSorter->getTestsGroupedByFixedGroupCount($sampleSuiteArray, $sampleTestArray, 3);
        // verify the resulting groups
        $this->assertCount(3, $actualResult);

        $expectedResults =  [
            1 => ['test2', 'test3', 'test1'],
            2 => ['mockSuite1'],
            3 => ['mockSuite2'],
        ];

        foreach ($actualResult as $groupNum => $group) {
            $this->assertEquals($expectedResults[$groupNum], array_keys($group));
        }
    }

    /**
     * Test splitting tests and suites with invalid group number.
     *
     * @return void
     */
    public function testTestsAndSuitesSplitByInvalidGroupNumber(): void
    {
        // mock tests for test object handler.
        $this->createMockForTest(0);

        // create test to size array
        $sampleTestArray = [
            'test1' => 1,
            'test2' => 125,
            'test3' => 35
        ];
        // create mock suite references
        $sampleSuiteArray = [
            'mockSuite1' => ['mockTest1', 'mockTest2'],
            'mockSuite2' => ['mockTest3', 'mockTest4'],
        ];

        $this->expectException(FastFailException::class);
        $this->expectExceptionMessage("Invalid parameter 'groupTotal': must be equal or greater than 3");

        // perform sort
        $testSorter = new ParallelGroupSorter();
        $testSorter->getTestsGroupedByFixedGroupCount($sampleSuiteArray, $sampleTestArray, 2);
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        $instanceProperty = new ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(null);
    }

    /**
     * Mock test object and test object handler.
     *
     * @param int $numberOfCalls
     * @param array $actionCount
     *
     * @return void
     */
    private function createMockForTest(int $numberOfCalls, array $actionCount = [300, 275, 300, 275]): void
    {
        $mockTest1 = $this->createMock(TestObject::class);
        $mockTest1
            ->method('getEstimatedDuration')
            ->willReturnCallback(
                function () use (&$numberOfCalls, $actionCount) {
                    $result = $actionCount[$numberOfCalls];
                    $numberOfCalls++;

                    return $result;
                }
            );

        $mockHandler = $this->createMock(TestObjectHandler::class);
        $mockHandler
            ->method('getObject')
            ->willReturn($mockTest1);

        $instanceProperty = new ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue($mockHandler, $mockHandler);
    }
}
