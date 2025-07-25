<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
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
