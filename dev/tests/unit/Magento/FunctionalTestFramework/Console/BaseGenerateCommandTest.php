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

    /**
     * One test in one suite
     */
    public function testSimpleTestConfig()
    {
        $testOne = new TestObject('Test1', [], [], []);
        $suiteOne = new SuiteObject('Suite1', ['Test1' => $testOne], [], []);

        $testArray = ['Test1' => $testOne];
        $suiteArray = ['Suite1' => $suiteOne];

        $this->mockHandlers($testArray, $suiteArray);

        $actual = json_decode($this->callConfig(['Test1']), true);
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
        AspectMock::double(TestObjectHandler::class,['initTestData' => ''])->make();
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
    public function callConfig($testArray)
    {
        $command = new BaseGenerateCommand();
        $class = new \ReflectionClass($command);
        $method = $class->getMethod('getTestAndSuiteConfiguration');
        $method->setAccessible(true);
        return $method->invokeArgs($command, [$testArray]);
    }
}
