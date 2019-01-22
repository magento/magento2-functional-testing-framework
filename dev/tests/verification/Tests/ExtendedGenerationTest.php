<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use tests\util\MftfTestCase;

class ExtendedGenerationTest extends MftfTestCase
{
    /**
     * Tests flat generation of a test that is referenced by another test
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedParentTestGeneration()
    {
        $this->generateAndCompareTest('ParentExtendedTest');
    }

    /**
     * Tests generation of test that extends based on another test when replacing actions
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedTestGenerationReplaceStepKey()
    {
        $this->generateAndCompareTest('ChildExtendedTestReplace');
    }

    /**
     * Tests generation of test that extends based on another test when replacing actions in hooks
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedTestGenerationReplaceHook()
    {
        $this->generateAndCompareTest('ChildExtendedTestReplaceHook');
    }

    /**
     * Tests generation of test that extends based on another test when merging actions
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedTestGenerationMergeActions()
    {
        $this->generateAndCompareTest('ChildExtendedTestMerging');
    }

    /**
     * Tests generation of test that extends based on another test when adding hooks
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedTestGenerationAddHooks()
    {
        $this->generateAndCompareTest('ChildExtendedTestAddHooks');
    }

    /**
     * Tests generation of test that extends based on another test when removing an action
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedTestGenerationRemoveAction()
    {
        $this->generateAndCompareTest('ChildExtendedTestRemoveAction');
    }

    /**
     * Tests generation of test that extends based on another test when removing an action
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedTestGenerationRemoveHookAction()
    {
        $this->generateAndCompareTest('ChildExtendedTestRemoveHookAction');
    }

    /**
     * Tests to ensure extended tests with no parents are not generated
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedTestGenerationNoParent()
    {
        $testObject = TestObjectHandler::getInstance()->getObject('ChildExtendedTestNoParent');
        $test = TestGenerator::getInstance(null, [$testObject]);
        $test->createAllTestFiles();

        $cestFile = $test->getExportDir() .
            DIRECTORY_SEPARATOR .
            $testObject->getCodeceptionName() .
            ".php";

        $this->assertFalse(file_exists($cestFile));
    }

    /**
     * Tests extending a skipped test generation.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendingSkippedGeneration()
    {
        $this->generateAndCompareTest('ExtendingSkippedTest');
    }

    /**
     * Tests extending and removing parent steps test generation.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendingAndRemovingStepsGeneration()
    {
        $this->generateAndCompareTest('ExtendedChildTestNotInSuite');
    }
}
