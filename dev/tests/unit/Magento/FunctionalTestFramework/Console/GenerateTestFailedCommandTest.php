<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Magento\FunctionalTestFramework\Console;

use Magento\FunctionalTestingFramework\Console\GenerateTestFailedCommand;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\TestCase;
use Magento\FunctionalTestingFramework\Exceptions\FastFailException;
use Magento\FunctionalTestingFramework\Console\GenerateTestsCommand;
use ReflectionClass;

class GenerateTestFailedCommandTest extends BaseGenerateCommandTest
{
    public function testSingleTestWithNoSuite(): void
    {
        $testFileReturn = [
            "tests/functional/tests/MFTF/_generated/default/SingleTestNoSuiteTest.php:SingleTestNoSuiteTest"
        ];
        $expectedConfiguration = '{"tests":["SingleTestNoSuiteTest"],"suites":null}';

        // Create a stub for the SomeClass class.
        $stub = $this->getMockBuilder(GenerateTestFailedCommand::class)
            ->onlyMethods(["readFailedTestFile", "writeFailedTestToFile"])
            ->getMock();
        // Configure the stub.
        $stub
            ->method('readFailedTestFile')
            ->willReturn($testFileReturn);
        $stub
            ->method('writeFailedTestToFile')
            ->willReturn(null);

        // Run the real code
        $configuration = $stub->getFailedTestList("", "");
        $this->assertEquals($expectedConfiguration, $configuration);
    }

    public function testMultipleTestsWithSuites(): void
    {
        $testFileReturn = [
            "tests/functional/tests/MFTF/_generated/SomeSpecificSuite/FirstTestSuiteTest.php:SingleTestSuiteTest",
            "tests/functional/tests/MFTF/_generated/SomeSpecificSuite/SecondTestNoSuiteTest.php:SingleTestNoSuiteTest"
        ];
        $expectedConfiguration =
        '{"tests":null,"suites":{"SomeSpecificSuite":["SingleTestSuiteTest","SingleTestNoSuiteTest"]}}';

        // Create a stub for the SomeClass class.
        $stub = $this->getMockBuilder(GenerateTestFailedCommand::class)
            ->onlyMethods(["readFailedTestFile", "writeFailedTestToFile"])
            ->getMock();
        // Configure the stub.
        $stub
            ->method('readFailedTestFile')
            ->willReturn($testFileReturn);
        $stub
            ->method('writeFailedTestToFile')
            ->willReturn(null);

        // Run the real code
        $configuration = $stub->getFailedTestList("", "");
        $this->assertEquals($expectedConfiguration, $configuration);
    }

    public function testMultipleTestFailureWithNoSuites(): void
    {
        $testFileReturn = [
            "tests/functional/tests/MFTF/_generated/default/SingleTestNoSuiteTest.php:SingleTestNoSuiteTest",
            "tests/functional/tests/MFTF/_generated/default/FirstTestSuiteTest.php:SingleTestSuiteTest"
        ];
        $expectedConfiguration = '{"tests":["SingleTestNoSuiteTest","SingleTestSuiteTest"],"suites":null}';

        // Create a stub for the SomeClass class.
        $stub = $this->getMockBuilder(GenerateTestFailedCommand::class)
            ->onlyMethods(["readFailedTestFile", "writeFailedTestToFile"])
            ->getMock();
        // Configure the stub.
        $stub
            ->method('readFailedTestFile')
            ->willReturn($testFileReturn);
        $stub
            ->method('writeFailedTestToFile')
            ->willReturn(null);

        // Run the real code
        $configuration = $stub->getFailedTestList("", "");
        $this->assertEquals($expectedConfiguration, $configuration);
    }

    public function testSingleSuiteAndNoTest(): void
    {
        $testFileReturn = [
            "tests/functional/tests/MFTF/_generated/SomeSpecificSuite/",
        ];
        $expectedConfiguration = '{"tests":null,"suites":{"SomeSpecificSuite":[[]]}}';

        // Create a stub for the SomeClass class.
        $stub = $this->getMockBuilder(GenerateTestFailedCommand::class)
            ->onlyMethods(["readFailedTestFile", "writeFailedTestToFile"])
            ->getMock();
        // Configure the stub.
        $stub
            ->method('readFailedTestFile')
            ->willReturn($testFileReturn);
        $stub
            ->method('writeFailedTestToFile')
            ->willReturn(null);

        // Run the real code
        $configuration = $stub->getFailedTestList("", "");
        $this->assertEquals($expectedConfiguration, $configuration);
    }

    public function testSingleSuiteWithTest(): void
    {
        $testFileReturn = [
            "tests/functional/tests/MFTF/_generated/SomeSpecificSuite/FirstTestSuiteTest.php:SingleTestSuiteTest",
        ];
        $expectedConfiguration = '{"tests":null,"suites":{"SomeSpecificSuite":["SingleTestSuiteTest"]}}';

        // Create a stub for the SomeClass class.
        $stub = $this->getMockBuilder(GenerateTestFailedCommand::class)
            ->onlyMethods(["readFailedTestFile", "writeFailedTestToFile"])
            ->getMock();
        // Configure the stub.
        $stub
            ->method('readFailedTestFile')
            ->willReturn($testFileReturn);
        $stub
            ->method('writeFailedTestToFile')
            ->willReturn(null);

        // Run the real code
        $configuration = $stub->getFailedTestList("", "");
        $this->assertEquals($expectedConfiguration, $configuration);
    }

    public function testMultipleSuitesWithNoTests(): void
    {
        $testFileReturn = [
            "tests/functional/tests/MFTF/_generated/SomeSpecificSuite/",
            "tests/functional/tests/MFTF/_generated/SomeSpecificSuite1/",

        ];
        $expectedConfiguration = '{"tests":null,"suites":{"SomeSpecificSuite":[[]],"SomeSpecificSuite1":[[]]}}';

        // Create a stub for the SomeClass class.
        $stub = $this->getMockBuilder(GenerateTestFailedCommand::class)
            ->onlyMethods(["readFailedTestFile", "writeFailedTestToFile"])
            ->getMock();
        // Configure the stub.
        $stub
            ->method('readFailedTestFile')
            ->willReturn($testFileReturn);
        $stub
            ->method('writeFailedTestToFile')
            ->willReturn(null);

        // Run the real code
        $configuration = $stub->getFailedTestList("", "");
        $this->assertEquals($expectedConfiguration, $configuration);
    }
}
