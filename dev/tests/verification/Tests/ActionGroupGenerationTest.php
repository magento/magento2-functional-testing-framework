<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class ActionGroupGenerationTest extends MftfTestCase
{
    /**
     * Test generation of a test referencing an action group with no arguments
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithNoArguments()
    {
        $this->generateAndCompareTest('ActionGroupWithNoArguments');
    }

    /**
     * Test generation of a test referencing an action group with default arguments and string selector
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithDefaultArgumentAndStringSelectorParam()
    {
        $this->generateAndCompareTest('ActionGroupWithDefaultArgumentAndStringSelectorParam');
    }

    /**
     * Test generation of a test referencing an action group with passed arguments
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithPassedArgumentAndStringSelectorParam()
    {
        $this->generateAndCompareTest('ActionGroupWithPassedArgumentAndStringSelectorParam');
    }

    /**
     * Test generation of a test referencing an action group with single parameter selector and default arguments
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithSingleParameterSelectorFromDefaultArgument()
    {
        $this->generateAndCompareTest('ActionGroupWithSingleParameterSelectorFromDefaultArgument');
    }

    /**
     * Test generation of test referencing an action group with single parameter from a passed arguemnt
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithSingleParameterSelectorFromPassedArgument()
    {
        $this->generateAndCompareTest('ActionGroupWithSingleParameterSelectorFromPassedArgument');
    }

    /**
     * Test generation of a test referencing an action group with multiple parameter selectors and default arguments
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithMultipleParameterSelectorsFromDefaultArgument()
    {
        $this->generateAndCompareTest('ActionGroupWithMultipleParameterSelectorsFromDefaultArgument');
    }

    /**
     * Test generation of a test referencing an action group with simple passed data.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithSimpleDataUsageFromPassedArgument()
    {
        $this->generateAndCompareTest('ActionGroupWithSimpleDataUsageFromPassedArgument');
    }

    /**
     * Test generation of a test referencing an action group with default data.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithSimpleDataUsageFromDefaultArgument()
    {
        $this->generateAndCompareTest('ActionGroupWithSimpleDataUsageFromDefaultArgument');
    }
}
