<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

class LocatorFunctionGenerationTest extends TestCase
{
    const LOCATOR_FUNCTION_TEST = 'LocatorFunctionTest';
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests generation of actions using elements that have a LocatorFunction.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testLocatorFunctionGeneration()
    {
        $testObject = TestObjectHandler::getInstance()->getObject(self::LOCATOR_FUNCTION_TEST);
        $test = TestGenerator::getInstance(null, [$testObject]);
        $test->createAllTestFiles();

        $testFile = $test->getExportDir() .
            DIRECTORY_SEPARATOR .
            $testObject->getCodeceptionName() .
            ".php";

        $this->assertTrue(file_exists($testFile));

        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . self::LOCATOR_FUNCTION_TEST . ".txt",
            $testFile
        );
    }
}
