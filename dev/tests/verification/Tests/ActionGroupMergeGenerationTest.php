<?php
 /**
  * Copyright © Magento, Inc. All rights reserved.
  * See COPYING.txt for license details.
  */

namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

class ActionGroupMergeGenerationTest extends TestCase
{
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests flat generation of a hardcoded test file with no external references.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testBasicActionGroup()
    {
        $this->runComparisonTest('BasicActionGroupTest');
    }

    /**
     * Test an ordinary action group with data
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithData()
    {
        $this->runComparisonTest('ActionGroupWithDataTest');
    }

    /**
     * Test an action group with data overridden in arguments
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithDataOverride()
    {
        $this->runComparisonTest('ActionGroupWithDataOverrideTest');
    }

    /**
     * Test an action group with no default data
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithNoDefault()
    {
        $this->runComparisonTest('ActionGroupWithNoDefaultTest');
    }

    /**
     * Test an action group with persisted data
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithPersistedData()
    {
        $this->runComparisonTest('ActionGroupWithPersistedData');
    }

    /**
     * Test an action group with top level persisted data
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testActionGroupWithTopLevelPersistedData()
    {
        $this->runComparisonTest('ActionGroupWithTopLevelPersistedData');
    }

    /**
     * Test an action group called multiple times
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMultipleActionGroups()
    {
        $this->runComparisonTest('MultipleActionGroupsTest');
    }

    /**
     * Test an action group with a merge counterpart
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMergedActionGroup()
    {
        $this->runComparisonTest('MergedActionGroupTest');
    }

    /**
     * Test an action group with arguments named similarly to elements
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testArgumentWithSameNameAsElement()
    {
        $this->runComparisonTest('ArgumentWithSameNameAsElement');
    }

    /**
     * Generate a Test by name and assert that it equals the corresponding .txt source of truth
     *
     * @param string $testName
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    private function runComparisonTest($testName)
    {
        $test = TestObjectHandler::getInstance()->getObject($testName);
        $testHandler = TestGenerator::getInstance(null, [$test]);
        $testHandler->createAllTestFiles();

        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . $test->getName() . ".txt",
            $testHandler->getExportDir() . DIRECTORY_SEPARATOR . $test->getCodeceptionName() . ".php"
        );
    }
}
