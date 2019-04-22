<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class ExecuteInSeleniumTest extends MftfTestCase
{
    /**
     * Tests generation of executeInSelenium action.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExecuteInSeleniumTest()
    {
        $this->generateAndCompareTest('ExecuteInSeleniumTest');
    }
}
