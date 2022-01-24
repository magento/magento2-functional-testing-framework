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
}
