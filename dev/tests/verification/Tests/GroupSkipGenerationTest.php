<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */

namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class GroupSkipGenerationTest extends MftfTestCase
{
    /**
     * Tests group skip test generation
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testGroupSkipGenerationTest()
    {
        $this->generateAndCompareTest('GroupSkipGenerationTest');
    }
}
