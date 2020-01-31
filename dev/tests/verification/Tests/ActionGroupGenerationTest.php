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
        $this->generateAndCompareTest('ActionGroupWithNoArgumentsActionGroup');
    }

    /**
     * Test generation of a test referencing an action group with default arguments and string selector
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithDefaultArgumentAndStringSelectorParam()
    {
        $this->generateAndCompareTest('ActionGroupWithDefaultArgumentAndStringSelectorParamActionGroup');
    }

    /**
     * Test generation of a test referencing an action group with passed arguments
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithPassedArgumentAndStringSelectorParam()
    {
        $this->generateAndCompareTest('ActionGroupWithPassedArgumentAndStringSelectorParamActionGroup');
    }

    /**
     * Test generation of a test referencing an action group with single parameter selector and default arguments
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithSingleParameterSelectorFromDefaultArgument()
    {
        $this->generateAndCompareTest('ActionGroupWithSingleParameterSelectorFromDefaultArgumentActionGroup');
    }

    /**
     * Test generation of test referencing an action group with single parameter from a passed arguemnt
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithSingleParameterSelectorFromPassedArgument()
    {
        $this->generateAndCompareTest('ActionGroupWithSingleParameterSelectorFromPassedArgumentActionGroup');
    }

    /**
     * Test generation of a test referencing an action group with multiple parameter selectors and default arguments
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithMultipleParameterSelectorsFromDefaultArgument()
    {
        $this->generateAndCompareTest('ActionGroupWithMultipleParameterSelectorsFromDefaultArgumentActionGroup');
    }

    /**
     * Test generation of a test referencing an action group with simple passed data.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithSimpleDataUsageFromPassedArgument()
    {
        $this->generateAndCompareTest('ActionGroupWithSimpleDataUsageFromPassedArgumentActionGroup');
    }

    /**
     * Test generation of a test referencing an action group with default data.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithSimpleDataUsageFromDefaultArgument()
    {
        $this->generateAndCompareTest('ActionGroupWithSimpleDataUsageFromDefaultArgumentActionGroup');
    }

    /**
     * Test generation of a test referencing an action group that uses stepKey references (grabFrom/CreateData)
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithStepKeyReferences()
    {
        $this->generateAndCompareTest('ActionGroupWithStepKeyReferencesActionGroup');
    }

    /**
     * Test generation of a test referencing an action group that uses stepKey references (grabFrom/CreateData)
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithNestedArgument()
    {
        $this->generateAndCompareTest('ActionGroupUsingNestedArgumentActionGroup');
    }

    /**
     * Test generation of a test referencing an action group that uses stepKey references (grabFrom/CreateData)
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithPersistedAndXmlEntityArguments()
    {
        $this->generateAndCompareTest('PersistedAndXmlEntityArgumentsActionGroup');
    }

    /**
     * Test generation of a test referencing an action group which is referenced by another action group
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupToExtend()
    {
        $this->generateAndCompareTest('ActionGroupToExtendActionGroup');
    }

    /**
     * Test generation of a test referencing an action group that references another action group
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedActionGroup()
    {
        $this->generateAndCompareTest('ExtendedActionGroupActionGroup');
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

    /**
     * Test generation of a test referencing an action group with xml comment in arguments and action group body.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithXmlComments()
    {
        $this->generateAndCompareTest('XmlCommentedActionGroupTest');
    }

    /**
     * Test generation of a test referencing an action group with selectors referencing stepKeys.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithActionStepKeyReferencesInSelectors()
    {
        $this->generateAndCompareTest('ActionGroupWithParameterizedElementsWithStepKeyReferences');
    }
}
