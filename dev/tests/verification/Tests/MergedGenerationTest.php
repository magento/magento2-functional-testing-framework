<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use tests\util\MftfTestCase;

class MergedGenerationTest extends MftfTestCase
{
    /**
     * Tests generation of a test merge file with no external references.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMergeGeneration()
    {
        $this->generateAndCompareTest('BasicMergeTest');
    }

    /**
     * Tests generation of a test merge file with only external references.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMergedReferences()
    {
        $this->generateAndCompareTest('MergedReferencesTest');
    }

    /**
     * Tests the merging of requiredEntity elements in Data, MQE-838
     */
    public function testParsedArray()
    {
        $entity = DataObjectHandler::getInstance()->getObject('testEntity');
        $this->assertCount(3, $entity->getLinkedEntities());
    }

    /**
     * Tests generation of a test merge file via insertBefore
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMergeMassViaInsertBefore()
    {
        $this->generateAndCompareTest('MergeMassViaInsertBefore');
    }

    /**
     * Tests generation of a test merge file via insertBefore
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMergeMassViaInsertAfter()
    {
        $this->generateAndCompareTest('MergeMassViaInsertAfter');
    }

    /**
     * Tests generation of a test skipped in merge.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMergeSkipGeneration()
    {
        $this->generateAndCompareTest('MergeSkip');
    }
}
