<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class ParameterArrayTest extends MftfTestCase
{
    /**
     * Tests flat generation of a hardcoded test file with no external references.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testParameterArrayGeneration()
    {
        $this->generateAndCompareTest('ParameterArrayTest');
    }
}
