<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

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
     * Tests generation of test that extends based on another test
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedTestGeneration()
    {
        $this->generateAndCompareTest('ChildExtendedTest');
    }
}
