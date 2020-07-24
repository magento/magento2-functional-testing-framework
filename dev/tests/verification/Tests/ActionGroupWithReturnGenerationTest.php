<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class ActionGroupWithReturnGenerationTest extends MftfTestCase
{
    /**
     * Test generation of a test referencing an action group that returns a value.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupReturningValue()
    {
        $this->generateAndCompareTest('ActionGroupReturningValueTest');
    }
    /**
     * Test generation of a test referencing a merged action group that returns a value.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMergedActionGroupReturningValue()
    {
        $this->generateAndCompareTest('MergedActionGroupReturningValueTest');
    }
    /**
     * Test generation of a test referencing an extended action group that returns a value.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedActionGroupReturningValue()
    {
        $this->generateAndCompareTest('ExtendedActionGroupReturningValueTest');
    }
    /**
     * Test generation of a test referencing an extending child action group that returns a value.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedChildActionGroupReturningValue()
    {
        $this->generateAndCompareTest('ExtendedChildActionGroupReturningValueTest');
    }
}
