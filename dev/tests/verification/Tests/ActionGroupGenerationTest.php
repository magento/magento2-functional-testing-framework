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

    /**
     * Test generation of a test referencing an action group that uses stepKey references (grabFrom/CreateData)
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithStepKeyReferences()
    {
        $this->generateAndCompareTest('ActionGroupWithStepKeyReferences');
    }

    /**
     * Test generation of a test referencing an action group that uses stepKey references (grabFrom/CreateData)
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithNestedArgument()
    {
        $this->generateAndCompareTest('ActionGroupUsingNestedArgument');
    }

    /**
     * Test generation of a test referencing an action group that uses stepKey references (grabFrom/CreateData)
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithPersistedAndXmlEntityArguments()
    {
        $this->generateAndCompareTest('PersistedAndXmlEntityArguments');
    }

    /**
     * Test generation of a test referencing an action group which is referenced by another action group
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupToExtend()
    {
        $this->generateAndCompareTest('ActionGroupToExtend');
    }

    /**
     * Test generation of a test referencing an action group that references another action group
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedActionGroup()
    {
        $this->generateAndCompareTest('ExtendedActionGroup');
    }

    /**
     * Test generation of a test referencing an action group that references another action group but removes an action
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedRemoveActionGroup()
    {
        $this->generateAndCompareTest('ExtendedRemoveActionGroup');
    }

    /**
     * Test generation of a test referencing an action group that uses stepKey references within the action group
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithCreateData()
    {
        $this->generateAndCompareTest('ActionGroupUsingCreateData');
    }

    /**
     * Test an action group with an arg containing stepKey text
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithArgContainingStepKey()
    {
        $this->generateAndCompareTest('ActionGroupContainsStepKeyInArgText');
    }

    /**
     * Test an action group with an arg containing stepKey text
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithSkipReadiness()
    {
        $this->generateAndCompareTest('ActionGroupSkipReadiness');
    }

    /**
     * Test an action group with an arg containing stepKey text
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithSectionAndDataArguments()
    {
        $this->generateAndCompareTest('ActionGroupWithSectionAndDataAsArguments');
    }

    /**
     * Test an action group with an arg that resolves into section.element with a hyphen in the parameter
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithHyphen()
    {
        $this->generateAndCompareTest('ActionGroupWithParameterizedElementWithHyphen');
    }
}
