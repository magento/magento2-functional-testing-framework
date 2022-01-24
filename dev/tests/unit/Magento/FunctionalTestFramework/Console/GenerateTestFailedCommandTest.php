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
    public function testSingleTestNoSuite(): void
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
}
