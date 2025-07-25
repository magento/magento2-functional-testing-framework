<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */

namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class PersistenceGenerationTest extends MftfTestCase
{
    /**
     * Tests complex persistence declarations in xml as they are generated to php.
     */
    public function testPersistedDeclarations()
    {
        $this->generateAndCompareTest('PersistenceCustomFieldsTest');
    }

    /**
     * Tests complex persistence declarations in xml as they are generated to php.
     */
    public function testPersistenceActionGroupAppendingTest()
    {
        $this->generateAndCompareTest('PersistenceActionGroupAppendingTest');
    }
}
