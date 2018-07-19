<?php
 /**
  * Copyright © Magento, Inc. All rights reserved.
  * See COPYING.txt for license details.
  */

namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class ActionGroupMergeGenerationTest extends MftfTestCase
{
    /**
     * Tests flat generation of a hardcoded test file with no external references.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testBasicActionGroup()
    {
        $this->generateAndCompareTest('BasicActionGroupTest');
    }

    /**
     * Test an ordinary action group with data
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithData()
    {
        $this->generateAndCompareTest('ActionGroupWithDataTest');
    }

    /**
     * Test an action group with data overridden in arguments
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithDataOverride()
    {
        $this->generateAndCompareTest('ActionGroupWithDataOverrideTest');
    }

    /**
     * Test an action group with no default data
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithNoDefault()
    {
        $this->generateAndCompareTest('ActionGroupWithNoDefaultTest');
    }

    /**
     * Test an action group with persisted data
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithPersistedData()
    {
        $this->generateAndCompareTest('ActionGroupWithPersistedData');
    }

    /**
     * Test an action group with top level persisted data
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithTopLevelPersistedData()
    {
        $this->generateAndCompareTest('ActionGroupWithTopLevelPersistedData');
    }

    /**
     * Test an action group called multiple times
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMultipleActionGroups()
    {
        $this->generateAndCompareTest('MultipleActionGroupsTest');
    }

    /**
     * Test an action group with a merge counterpart
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMergedActionGroup()
    {
        $this->generateAndCompareTest('MergedActionGroupTest');
    }

    /**
     * Test an action group with arguments named similarly to elements
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testArgumentWithSameNameAsElement()
    {
        $this->generateAndCompareTest('ArgumentWithSameNameAsElement');
    }

    /**
     * Test an action group with a merge counterpart that's merged via insertBefore
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMergedActionGroupViaInsertBefore()
    {
        $this->generateAndCompareTest('ActionGroupMergedViaInsertBefore');
    }

    /**
     * Test an action group with a merge counterpart that's merged via insertAfter
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMergedActionGroupViaInsertAfter()
    {
        $this->generateAndCompareTest('ActionGroupMergedViaInsertAfter');
    }
}
