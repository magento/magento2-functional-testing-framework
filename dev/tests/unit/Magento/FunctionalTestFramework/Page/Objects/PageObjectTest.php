<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Page\Objects;

use Magento\FunctionalTestingFramework\Page\Objects\PageObject;
use tests\unit\Util\MagentoTestCase;

/**
 * Class PageObjectTest
 */
class PageObjectTest extends MagentoTestCase
{
    /**
     * Assert that the page object has a section
     */
    public function testHasSection()
    {
        $page = new PageObject('name', 'urlPath', 'module', ['section1', 'section2'], false, 'area');
        $this->assertTrue($page->hasSection('section1'));
    }

    /**
     * Assert that the page object doesn't have a section
     */
    public function testDoesntHaveSection()
    {
        $page = new PageObject('name', 'urlPath', 'module', ['section1', 'section2'], false, 'area');
        $this->assertFalse($page->hasSection('section3'));
    }
}
