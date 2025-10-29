<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */

namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class ExtendedDataTest extends MftfTestCase
{
    /**
     * Tests flat generation of a hardcoded test file with no external references.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testExtendedParameterArrayGeneration()
    {
        $this->generateAndCompareTest('ExtendParentDataTest');
    }
}
