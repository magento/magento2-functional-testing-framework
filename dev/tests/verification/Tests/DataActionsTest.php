<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
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
