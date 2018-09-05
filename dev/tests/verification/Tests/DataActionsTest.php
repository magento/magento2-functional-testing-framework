<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class DataActionsTest extends MftfTestCase
{
    /**
     * Tests Data actions (create,update,etc) generate using correctly scoped variables
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testDataActions()
    {
        $this->generateAndCompareTest('DataActionsTest');
    }
}
