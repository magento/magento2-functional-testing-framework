<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

class ActionGroupGenerationTest extends TestCase
{
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests flat generation of a hardcoded test file with no external references.
     */
    private function validateGenerateAndContents($testName)
    {
        $test = TestObjectHandler::getInstance()->getObject($testName);
        $testHandler = TestGenerator::getInstance(null, [$test]);
        $testHandler->createAllTestFiles();

        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . $testName . ".txt",
            $testHandler->getExportDir() . DIRECTORY_SEPARATOR . $test->getCodeceptionName() . ".php"
        );
    }

    /**
     * Test generation of a test referencing an action group with no arguments
     */
    public function testActionGroupWithNoArguments()
    {
        $this->validateGenerateAndContents('ActionGroupWithNoArguments');
    }

    /**
     * Test generation of a test referencing an action group with default arguments and string selector
     */
    public function testActionGroupWithDefaultArgumentAndStringSelectorParam()
    {
        $this->validateGenerateAndContents('ActionGroupWithDefaultArgumentAndStringSelectorParam');
    }

    /**
     * Test generation of a test referencing an action group with passed arguments
     */
    public function testActionGroupWithPassedArgumentAndStringSelectorParam()
    {
        $this->validateGenerateAndContents('ActionGroupWithPassedArgumentAndStringSelectorParam');
    }

    /**
     * Test generation of a test referencing an action group with single parameter selector and default arguments
     */
    public function testActionGroupWithSingleParameterSelectorFromDefaultArgument()
    {
        $this->validateGenerateAndContents('ActionGroupWithSingleParameterSelectorFromDefaultArgument');
    }

    /**
     * Test generation of test referencing an action group with single parameter from a passed arguemnt
     */
    public function testActionGroupWithSingleParameterSelectorFromPassedArgument()
    {
        $this->validateGenerateAndContents('ActionGroupWithSingleParameterSelectorFromPassedArgument');
    }

    /**
     * Test generation of a test referencing an action group with multiple parameter selectors and default arguments
     */
    public function testActionGroupWithMultipleParameterSelectorsFromDefaultArgument()
    {
        $this->validateGenerateAndContents('ActionGroupWithMultipleParameterSelectorsFromDefaultArgument');
    }
}
