<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
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
