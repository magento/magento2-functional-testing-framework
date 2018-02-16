<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

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
}
