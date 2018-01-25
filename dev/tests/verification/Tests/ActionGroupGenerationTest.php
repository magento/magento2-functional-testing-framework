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
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
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
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithNoArguments()
    {
        $this->validateGenerateAndContents('ActionGroupWithNoArguments');
    }

    /**
     * Test generation of a test referencing an action group with default arguments and string selector
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithDefaultArgumentAndStringSelectorParam()
    {
        $this->validateGenerateAndContents('ActionGroupWithDefaultArgumentAndStringSelectorParam');
    }

    /**
     * Test generation of a test referencing an action group with passed arguments
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithPassedArgumentAndStringSelectorParam()
    {
        $this->validateGenerateAndContents('ActionGroupWithPassedArgumentAndStringSelectorParam');
    }

    /**
     * Test generation of a test referencing an action group with single parameter selector and default arguments
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithSingleParameterSelectorFromDefaultArgument()
    {
        $this->validateGenerateAndContents('ActionGroupWithSingleParameterSelectorFromDefaultArgument');
    }

    /**
     * Test generation of test referencing an action group with single parameter from a passed arguemnt
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithSingleParameterSelectorFromPassedArgument()
    {
        $this->validateGenerateAndContents('ActionGroupWithSingleParameterSelectorFromPassedArgument');
    }

    /**
     * Test generation of a test referencing an action group with multiple parameter selectors and default arguments
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithMultipleParameterSelectorsFromDefaultArgument()
    {
        $this->validateGenerateAndContents('ActionGroupWithMultipleParameterSelectorsFromDefaultArgument');
    }

    /**
     * Test generation of a test referencing an action group with simple passed data.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithSimpleDataUsageFromPassedArgument()
    {
        $this->validateGenerateAndContents('ActionGroupWithSimpleDataUsageFromPassedArgument');
    }

    /**
     * Test generation of a test referencing an action group with default data.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithSimpleDataUsageFromDefaultArgument()
    {
        $this->validateGenerateAndContents('ActionGroupWithSimpleDataUsageFromDefaultArgument');
    }
}
