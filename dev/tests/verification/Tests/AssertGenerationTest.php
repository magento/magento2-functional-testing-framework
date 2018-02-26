<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class AssertGenerationTest extends MftfTestCase
{
    /**
     * Tests assert generation.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testAssertGeneration()
    {
        $this->generateAndCompareTest('AssertTest');
    }
}
