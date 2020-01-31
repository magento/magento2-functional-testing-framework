<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tests\unit\Magento\FunctionalTestingFramework\Console;

use AspectMock\Test as AspectMock;
use PHPUnit\Framework\TestCase;
use Magento\FunctionalTestingFramework\Console\BaseGenerateCommand;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;

class BaseGenerateCommandTest extends TestCase
{
    public function tearDown()
    {
        AspectMock::clean();
    }

    public function testOneTestOneSuiteConfig()
    {
        $testOne = new TestObject('Test1', [], [], []);
        $suiteOne = new SuiteObject('Suite1', ['Test1' => $testOne], [], []);

        $testArray = ['Test1' => $testOne];
        $suiteArray = ['Suite1' => $suiteOne];

        $this->mockHandlers($testArray, $suiteArray);

        $actual = json_decode($this->callTestConfig(['Test1']), true);
        $expected = ['tests' => null, 'suites' => ['Suite1' => ['Test1']]];
        $this->assertEquals($expected, $actual);
    }

    public function testOneTestTwoSuitesConfig()
    {
        $testOne = new TestObject('Test1', [], [], []);
        $suiteOne = new SuiteObject('Suite1', ['Test1' => $testOne], [], []);
        $suiteTwo = new SuiteObject('Suite2', ['Test1' => $testOne], [], []);

        $testArray = ['Test1' => $testOne];
        $suiteArray = ['Suite1' => $suiteOne, 'Suite2' => $suiteTwo];

        $this->mockHandlers($testArray, $suiteArray);

        $actual = json_decode($this->callTestConfig(['Test1']), true);
        $expected = ['tests' => null, 'suites' => ['Suite1' => ['Test1'], 'Suite2' => ['Test1']]];
        $this->assertEquals($expected, $actual);
    }

    public function testOneTestOneGroup()
    {
        $testOne = new TestObject('Test1', [], ['group' => ['Group1']], []);

        $testArray = ['Test1' => $testOne];
        $suiteArray = [];

        $this->mockHandlers($testArray, $suiteArray);

        $actual = json_decode($this->callGroupConfig(['Group1']), true);
        $expected = ['tests' => ['Test1'], 'suites' => null];
        $this->assertEquals($expected, $actual);
    }

    public function testThreeTestsTwoGroup()
    {
        $testOne = new TestObject('Test1', [], ['group' => ['Group1']], []);
        $testTwo = new TestObject('Test2', [], ['group' => ['Group1']], []);
        $testThree = new TestObject('Test3', [], ['group' => ['Group2']], []);

        $testArray = ['Test1' => $testOne, 'Test2' => $testTwo, 'Test3' => $testThree];
        $suiteArray = [];

        $this->mockHandlers($testArray, $suiteArray);

        $actual = json_decode($this->callGroupConfig(['Group1', 'Group2']), true);
        $expected = ['tests' => ['Test1', 'Test2', 'Test3'], 'suites' => null];
        $this->assertEquals($expected, $actual);
    }

    public function testOneTestOneSuiteOneGroupConfig()
    {
        $testOne = new TestObject('Test1', [], ['group' => ['Group1']], []);
        $suiteOne = new SuiteObject('Suite1', ['Test1' => $testOne], [], []);

        $testArray = ['Test1' => $testOne];
        $suiteArray = ['Suite1' => $suiteOne];

        $this->mockHandlers($testArray, $suiteArray);

        $actual = json_decode($this->callGroupConfig(['Group1']), true);
        $expected = ['tests' => null, 'suites' => ['Suite1' => ['Test1']]];
        $this->assertEquals($expected, $actual);
    }

    public function testTwoTestOneSuiteTwoGroupConfig()
    {
        $testOne = new TestObject('Test1', [], ['group' => ['Group1']], []);
        $testTwo = new TestObject('Test2', [], ['group' => ['Group2']], []);
        $suiteOne = new SuiteObject('Suite1', ['Test1' => $testOne, 'Test2' => $testTwo], [], []);

        $testArray = ['Test1' => $testOne, 'Test2' => $testTwo];
        $suiteArray = ['Suite1' => $suiteOne];

        $this->mockHandlers($testArray, $suiteArray);

        $actual = json_decode($this->callGroupConfig(['Group1', 'Group2']), true);
        $expected = ['tests' => null, 'suites' => ['Suite1' => ['Test1', 'Test2']]];
        $this->assertEquals($expected, $actual);
    }

    public function testTwoTestTwoSuiteOneGroupConfig()
    {
        $testOne = new TestObject('Test1', [], ['group' => ['Group1']], []);
        $testTwo = new TestObject('Test2', [], ['group' => ['Group1']], []);
        $suiteOne = new SuiteObject('Suite1', ['Test1' => $testOne], [], []);
        $suiteTwo = new SuiteObject('Suite2', ['Test2' => $testTwo], [], []);

        $testArray = ['Test1' => $testOne, 'Test2' => $testTwo];
        $suiteArray = ['Suite1' => $suiteOne, 'Suite2' => $suiteTwo];

        $this->mockHandlers($testArray, $suiteArray);

        $actual = json_decode($this->callGroupConfig(['Group1']), true);
        $expected = ['tests' => null, 'suites' => ['Suite1' => ['Test1'], 'Suite2' => ['Test2']]];
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test specific usecase of a test that is in a group with the group being called along with the suite
     * i.e. run:group Group1 Suite1
     * @throws \Exception
     */
    public function testThreeTestOneSuiteOneGroupMix()
    {
        $testOne = new TestObject('Test1', [], [], []);
        $testTwo = new TestObject('Test2', [], [], []);
        $testThree = new TestObject('Test3', [], ['group' => ['Group1']], []);
        $suiteOne = new SuiteObject(
            'Suite1',
            ['Test1' => $testOne, 'Test2' => $testTwo, 'Test3' => $testThree],
            [],
            []
        );

        $testArray = ['Test1' => $testOne, 'Test2' => $testTwo, 'Test3' => $testThree];
        $suiteArray = ['Suite1' => $suiteOne];

        $this->mockHandlers($testArray, $suiteArray);

        $actual = json_decode($this->callGroupConfig(['Group1', 'Suite1']), true);
        $expected = ['tests' => null, 'suites' => ['Suite1' => []]];
        $this->assertEquals($expected, $actual);
    }

    public function testSuiteToTestSyntax()
    {
        $testOne = new TestObject('Test1', [], [], []);
        $suiteOne = new SuiteObject(
            'Suite1',
            ['Test1' => $testOne],
            [],
            []
        );

        $testArray = ['Test1' => $testOne];
        $suiteArray = ['Suite1' => $suiteOne];
        $this->mockHandlers($testArray, $suiteArray);
        $actual = json_decode($this->callTestConfig(['Suite1:Test1']), true);
        $expected = ['tests' => null, 'suites' => ['Suite1' => ['Test1']]];
        $this->assertEquals($expected, $actual);
    }

    /**
     * Mock handlers to skip parsing
     * @param array $testArray
     * @param array $suiteArray
     * @throws \Exception
     */
    public function mockHandlers($testArray, $suiteArray)
    {
        AspectMock::double(TestObjectHandler::class, ['initTestData' => ''])->make();
        $handler = TestObjectHandler::getInstance();
        $property = new \ReflectionProperty(TestObjectHandler::class, 'tests');
        $property->setAccessible(true);
        $property->setValue($handler, $testArray);

        AspectMock::double(SuiteObjectHandler::class, ['initSuiteData' => ''])->make();
        $handler = SuiteObjectHandler::getInstance();
        $property = new \ReflectionProperty(SuiteObjectHandler::class, 'suiteObjects');
        $property->setAccessible(true);
        $property->setValue($handler, $suiteArray);
    }

    /**
     * Changes visibility and runs getTestAndSuiteConfiguration
     * @param array $testArray
     * @return string
     */
    public function callTestConfig($testArray)
    {
        $command = new BaseGenerateCommand();
        $class = new \ReflectionClass($command);
        $method = $class->getMethod('getTestAndSuiteConfiguration');
        $method->setAccessible(true);
        return $method->invokeArgs($command, [$testArray]);
    }

    /**
     * Changes visibility and runs getGroupAndSuiteConfiguration
     * @param array $groupArray
     * @return string
     */
    public function callGroupConfig($groupArray)
    {
        $command = new BaseGenerateCommand();
        $class = new \ReflectionClass($command);
        $method = $class->getMethod('getGroupAndSuiteConfiguration');
        $method->setAccessible(true);
        return $method->invokeArgs($command, [$groupArray]);
    }
}
