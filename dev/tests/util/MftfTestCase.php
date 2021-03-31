<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\util;

use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Suite\SuiteGenerator;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

abstract class MftfTestCase extends TestCase
{
    const RESOURCES_PATH = __DIR__ .
    DIRECTORY_SEPARATOR .
    '..' .
    DIRECTORY_SEPARATOR .
    'verification' .
    DIRECTORY_SEPARATOR .
    'Resources';

    /**
     * Private function which takes a test name, generates the test and compares with a correspondingly named txt file
     * with expected contents.
     *
     * @param string $testName
     */
    public function generateAndCompareTest($testName)
    {
        $testObject = TestObjectHandler::getInstance()->getObject($testName);
        $test = TestGenerator::getInstance(null, [$testObject]);
        $test->createAllTestFiles();

        $cestFile = $test->getExportDir() .
            DIRECTORY_SEPARATOR .
            $testObject->getCodeceptionName() .
            ".php";

        $this->assertTrue(file_exists($cestFile));

        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . $testObject->getName() . ".txt",
            $cestFile
        );
    }

    /**
     * Private function which attempts to generate tests given an invalid shcema of a various type
     *
     * @param string[] $fileContents
     * @param string $objectType
     * @param string $expectedError
     * @throws \Exception
     */
    public function validateSchemaErrorWithTest($fileContents, $objectType ,$expectedError)
    {
        $this->clearHandler();
        $fullTestModulePath = TESTS_MODULE_PATH .
            DIRECTORY_SEPARATOR .
            'TestModule' .
            DIRECTORY_SEPARATOR .
            $objectType .
            DIRECTORY_SEPARATOR;

        foreach ($fileContents as $fileName => $fileContent) {
            $tempFile = $fullTestModulePath . $fileName;
            $handle = fopen($tempFile, 'w') or die('Cannot open file:  ' . $tempFile);
            fwrite($handle, $fileContent);
            fclose($handle);
        }
        try {
            $this->expectExceptionMessage($expectedError);
            TestObjectHandler::getInstance()->getObject("someTest");
        } finally {
            foreach (array_keys($fileContents) as $fileName) {
                unlink($fullTestModulePath . $fileName);
            }
            $this->clearHandler();
        }
    }

    /**
     * Asserts that the given callback throws the given exception
     *
     * @param string $expectClass
     * @param array $expectedMessages
     * @param callable $callback
     */
    protected function assertExceptionRegex(string $expectClass, array $expectedMessages, callable $callback)
    {
        try {
            $callback();
        } catch (\Throwable $exception) {
            $this->assertInstanceOf($expectClass, $exception, 'An invalid exception was thrown.');
            foreach ($expectedMessages as $expectedMessage) {
                $this->assertMatchesRegularExpression($expectedMessage, $exception->getMessage());
            }
            return;
        }

        $this->fail('No exception was thrown.');
    }

    /**
     * Clears test handler and object manager to force recollection of test data
     *
     * @throws \Exception
     */
    private function clearHandler()
    {
        // clear test object handler to force recollection of test data
        $property = new \ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $property->setAccessible(true);
        $property->setValue(null);

        // clear test object handler to force recollection of test data
        $property = new \ReflectionProperty(ObjectManager::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null);

        // clear suite generator to force recollection of test data
        $property = new \ReflectionProperty(SuiteGenerator::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null);

        // clear suite object handler to force recollection of test data
        $property = new \ReflectionProperty(SuiteObjectHandler::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null);
    }
}
