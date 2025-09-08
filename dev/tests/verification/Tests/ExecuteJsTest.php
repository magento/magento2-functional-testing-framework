<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
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
