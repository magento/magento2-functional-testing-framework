<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

class XmlDuplicateGerationTest extends TestCase
{
    const XML_DUPLICATE_TEST = 'XmlDuplicateTest';
    const XML_DUPLICATE_ACTIONGROUP = 'xmlDuplicateActionGroup';
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests flat generation of a hardcoded test file with no external references.
     */
    public function testDuplicatesInTest()
    {
        $testObject = TestObjectHandler::getInstance()->getObject(self::XML_DUPLICATE_TEST);
        $this->addToAssertionCount(1); // No exception thrown thus far, can assert dupes didn't cause an error.
    }

    public function testDuplicatesInActionGroup()
    {
        $actionGroup = ActionGroupObjectHandler::getInstance()->getObject(self::XML_DUPLICATE_ACTIONGROUP);
        $this->addToAssertionCount(1); // No exception thrown thus far, can assert dupes didn't cause an error.
    }
}
