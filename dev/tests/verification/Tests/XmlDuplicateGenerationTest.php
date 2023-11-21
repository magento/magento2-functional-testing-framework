<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use tests\util\MftfTestCase;

class XmlDuplicateGenerationTest extends MftfTestCase
{
    const XML_DUPLICATE_TEST = 'XmlDuplicateTest';
    const XML_DUPLICATE_ACTIONGROUP = 'xmlDuplicateActionGroup';
    const XML_DUPLICATE_MERGE_TEST = 'BasicDupedActionTest';
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests flat generation of a hardcoded test file with no external references.
     */
    public function testDuplicatesInTest()
    {
        TestObjectHandler::getInstance()->getObject(self::XML_DUPLICATE_TEST);
        $this->addToAssertionCount(1); // No exception thrown thus far, can assert dupes didn't cause an error.
    }

    public function testDuplicatesInActionGroup()
    {
        ActionGroupObjectHandler::getInstance()->getObject(self::XML_DUPLICATE_ACTIONGROUP);
        $this->addToAssertionCount(1); // No exception thrown thus far, can assert dupes didn't cause an error.
    }

    /**
     * Parser testing, makes sure test action nodes are marked as array.
     */
    public function testDuplicatesInMergeTest()
    {
        TestObjectHandler::getInstance()->getObject(self::XML_DUPLICATE_MERGE_TEST);
        $this->addToAssertionCount(1); // No exception thrown thus far, can assert dupes didn't cause an error.
    }
}
