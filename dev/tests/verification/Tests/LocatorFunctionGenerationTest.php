<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class LocatorFunctionGenerationTest extends MftfTestCase
{
    /**
     * Tests generation of actions using elements that have a LocatorFunction.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testLocatorFunctionGeneration()
    {
        $this->generateAndCompareTest('LocatorFunctionTest');
    }
}
