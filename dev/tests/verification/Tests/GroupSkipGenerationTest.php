<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
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
