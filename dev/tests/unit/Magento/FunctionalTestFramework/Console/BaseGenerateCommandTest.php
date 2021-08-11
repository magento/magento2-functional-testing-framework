<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Console;

use Exception;
use Magento\FunctionalTestingFramework\Console\BaseGenerateCommand;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class BaseGenerateCommandTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        $handler = TestObjectHandler::getInstance();
        $testsProperty = new ReflectionProperty(TestObjectHandler::class, 'tests');
        $testsProperty->setAccessible(true);
        $testsProperty->setValue($handler, []);
        $testObjectHandlerProperty = new ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $testObjectHandlerProperty->setAccessible(true);
        $testObjectHandlerProperty->setValue($handler);

        $handler = SuiteObjectHandler::getInstance();
        $suiteObjectsProperty = new ReflectionProperty(SuiteObjectHandler::class, 'suiteObjects');
        $suiteObjectsProperty->setAccessible(true);
        $suiteObjectsProperty->setValue($handler, []);
        $suiteObjectHandlerProperty = new ReflectionProperty(SuiteObjectHandler::class, 'instance');
        $suiteObjectHandlerProperty->setAccessible(true);
        $suiteObjectHandlerProperty->setValue($handler);
    }

    public function testOneTestOneSuiteConfig(): void
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

    public function testOneTestTwoSuitesConfig(): void
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

    public function testOneTestOneGroup(): void
    {
        $testOne = new TestObject('Test1', [], ['group' => ['Group1']], []);

        $testArray = ['Test1' => $testOne];
        $suiteArray = [];

        $this->mockHandlers($testArray, $suiteArray);

        $actual = json_decode($this->callGroupConfig(['Group1']), true);
        $expected = ['tests' => ['Test1'], 'suites' => null];
        $this->assertEquals($expected, $actual);
    }

    public function testThreeTestsTwoGroup(): void
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

    public function testOneTestOneSuiteOneGroupConfig(): void
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

    public function testTwoTestOneSuiteTwoGroupConfig(): void
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

    public function testTwoTestTwoSuiteOneGroupConfig(): void
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
     * i.e. run:group Group1 Suite1.
     *
     * @return void
     * @throws Exception
     */
    public function testThreeTestOneSuiteOneGroupMix(): void
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

    public function testSuiteToTestSyntax(): void
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
     * Mock handlers to skip parsing.
     *
     * @param array $testArray
     * @param array $suiteArray
     *
     * @return void
     * @throws Exception
     */
    public function mockHandlers(array $testArray, array $suiteArray): void
    {
        // bypass the initTestData method
        $testObjectHandlerClass = new ReflectionClass(TestObjectHandler::class);
        $constructor = $testObjectHandlerClass->getConstructor();
        $constructor->setAccessible(true);
        $testObjectHandlerObject = $testObjectHandlerClass->newInstanceWithoutConstructor();
        $constructor->invoke($testObjectHandlerObject);

        $testObjectHandlerProperty = new ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $testObjectHandlerProperty->setAccessible(true);
        $testObjectHandlerProperty->setValue($testObjectHandlerObject);

        $handler = TestObjectHandler::getInstance();
        $property = new ReflectionProperty(TestObjectHandler::class, 'tests');
        $property->setAccessible(true);
        $property->setValue($handler, $testArray);

        // bypass the initTestData method
        $suiteObjectHandlerClass = new ReflectionClass(SuiteObjectHandler::class);
        $constructor = $suiteObjectHandlerClass->getConstructor();
        $constructor->setAccessible(true);
        $suiteObjectHandlerObject = $suiteObjectHandlerClass->newInstanceWithoutConstructor();
        $constructor->invoke($suiteObjectHandlerObject);

        $suiteObjectHandlerProperty = new ReflectionProperty(SuiteObjectHandler::class, 'instance');
        $suiteObjectHandlerProperty->setAccessible(true);
        $suiteObjectHandlerProperty->setValue($suiteObjectHandlerObject);

        $handler = SuiteObjectHandler::getInstance();
        $property = new ReflectionProperty(SuiteObjectHandler::class, 'suiteObjects');
        $property->setAccessible(true);
        $property->setValue($handler, $suiteArray);
    }

    /**
     * Changes visibility and runs getTestAndSuiteConfiguration.
     *
     * @param array $testArray
     *
     * @return string
     * @throws ReflectionException
     */
    public function callTestConfig(array $testArray): string
    {
        $command = new BaseGenerateCommand();
        $class = new ReflectionClass($command);
        $method = $class->getMethod('getTestAndSuiteConfiguration');
        $method->setAccessible(true);

        return $method->invokeArgs($command, [$testArray]);
    }

    /**
     * Changes visibility and runs getGroupAndSuiteConfiguration.
     *
     * @param array $groupArray
     *
     * @return string
     * @throws ReflectionException
     */
    public function callGroupConfig(array $groupArray): string
    {
        $command = new BaseGenerateCommand();
        $class = new ReflectionClass($command);
        $method = $class->getMethod('getGroupAndSuiteConfiguration');
        $method->setAccessible(true);

        return $method->invokeArgs($command, [$groupArray]);
    }
}
