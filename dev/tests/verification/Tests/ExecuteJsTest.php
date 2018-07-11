<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class ExecuteJsTest extends MftfTestCase
{
    /**
     * Tests escaping of $javascriptVariable => \$javascriptVariable in the executeJs function
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExecuteJsTest()
    {
        $this->generateAndCompareTest('ExecuteJsEscapingTest');
    }
}
