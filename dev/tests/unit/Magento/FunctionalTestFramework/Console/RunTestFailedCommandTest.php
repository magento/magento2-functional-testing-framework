<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Console;

use Magento\FunctionalTestingFramework\Console\RunTestFailedCommand;

class RunTestFailedCommandTest extends BaseGenerateCommandTest
{
    /**
     * @throws \ReflectionException
     */
    public function testMultipleTests(): void
    {
        $testFailedFile = [
            "tests/functional/tests/MFTF/_generated/default/SingleTestNoSuiteTest.php:SingleTestNoSuiteTest",
            "tests/functional/tests/MFTF/_generated/SomeSpecificSuite/FirstTestSuiteTest.php:SingleTestSuiteTest",
            "tests/functional/tests/MFTF/_generated/SomeSpecificSuite/SecondTestNoSuiteTest.php:SingleTestNoSuiteTest",
            "tests/functional/tests/MFTF/_generated/SomeOtherSuite/SecondTestNoSuiteTest.php:SingleTestNoSuiteTest",
        ];

        $expectedResult = [
            "tests/functional/tests/MFTF/_generated/default/SingleTestNoSuiteTest.php",
            "-g SomeSpecificSuite",
            "-g SomeOtherSuite",
        ];

        $runFailed = new RunTestFailedCommand('run:failed');
        $filter = $this->invokePrivateMethod($runFailed, 'filterTestsForExecution', [$testFailedFile]);
        $this->assertEquals($expectedResult, $filter);
    }

    /**
     * Invoking private method to be able to test it.
     * NOTE: Bad practice don't repeat it.
     *
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed
     * @throws \ReflectionException
     */
    private function invokePrivateMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    public function testSingleTestNoSuite(): void
    {
        $testFailedFile = [
            "tests/functional/tests/MFTF/_generated/default/SingleTestNoSuiteTest.php:SingleTestNoSuiteTest"
        ];

        $expectedResult = [
            'tests/functional/tests/MFTF/_generated/default/SingleTestNoSuiteTest.php'
        ];

        $runFailed = new RunTestFailedCommand('run:failed');
        $filter = $this->invokePrivateMethod($runFailed, 'filterTestsForExecution', [$testFailedFile]);
        $this->assertEquals($expectedResult, $filter);
    }

    public function testMultipleTestNoSuite(): void
    {
        $testFailedFile = [
            "tests/functional/tests/MFTF/_generated/default/SingleTestNoSuiteTest.php:SingleTestNoSuiteTest",
            "tests/functional/tests/MFTF/_generated/default/FirstTestSuiteTest.php:SingleTestSuiteTest"
        ];

        $expectedResult = [
            "tests/functional/tests/MFTF/_generated/default/SingleTestNoSuiteTest.php",
            "tests/functional/tests/MFTF/_generated/default/FirstTestSuiteTest.php"
        ];

        $runFailed = new RunTestFailedCommand('run:failed');
        $filter = $this->invokePrivateMethod($runFailed, 'filterTestsForExecution', [$testFailedFile]);
        $this->assertEquals($expectedResult, $filter);
    }

    public function testSingleSuiteNoTest(): void
    {
        $testFailedFile = [
            "tests/functional/tests/MFTF/_generated/SomeSpecificSuite/",
        ];

        $expectedResult = [
            "-g SomeSpecificSuite"
        ];

        $runFailed = new RunTestFailedCommand('run:failed');
        $filter = $this->invokePrivateMethod($runFailed, 'filterTestsForExecution', [$testFailedFile]);
        $this->assertEquals($expectedResult, $filter);
    }

    public function testSingleSuiteAndTest(): void
    {
        $testFailedFile = [
            "tests/functional/tests/MFTF/_generated/SomeSpecificSuite/FirstTestSuiteTest.php:SingleTestSuiteTest",
        ];
        $expectedResult = [
            "-g SomeSpecificSuite",
        ];

        $runFailed = new RunTestFailedCommand('run:failed');
        $filter = $this->invokePrivateMethod($runFailed, 'filterTestsForExecution', [$testFailedFile]);
        $this->assertEquals($expectedResult, $filter);
    }

    public function testMultipleSuitesWithNoTest(): void
    {
        $testFailedFile = [
            "tests/functional/tests/MFTF/_generated/SomeSpecificSuite/",
            "tests/functional/tests/MFTF/_generated/SomeSpecificSuite1/",
            "tests/functional/tests/MFTF/_generated/SomeSpecificSuite2/"
        ];
        $expectedResult = [
            "-g SomeSpecificSuite",
            "-g SomeSpecificSuite1",
            "-g SomeSpecificSuite2",
        ];

        $runFailed = new RunTestFailedCommand('run:failed');
        $filter = $this->invokePrivateMethod($runFailed, 'filterTestsForExecution', [$testFailedFile]);
        $this->assertEquals($expectedResult, $filter);
    }
}
