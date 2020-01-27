<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class DeprecatedTest extends MftfTestCase
{
    /**
     * Tests flat generation of a deprecated test which uses deprecated entities.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testDeprecatedTestEntitiesGeneration()
    {
        $this->generateAndCompareTest('DeprecatedTest');
    }

    /**
     * Tests flat generation of a test which uses deprecated entities.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testDeprecatedEntitiesOnlyGeneration()
    {
        $this->generateAndCompareTest('DeprecatedEntitiesTest');
    }
}
