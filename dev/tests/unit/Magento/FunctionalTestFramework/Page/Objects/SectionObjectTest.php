<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Page\Objects;

use Magento\FunctionalTestingFramework\Page\Objects\ElementObject;
use Magento\FunctionalTestingFramework\Page\Objects\SectionObject;
use tests\unit\Util\MagentoTestCase;

/**
 * Class SectionObjectTest
 */
class SectionObjectTest extends MagentoTestCase
{
    /**
     * Assert that the section object has an element
     */
    public function testHasElement()
    {
        $element1 = new ElementObject('element1', 'type', '#selector', null, '41', false);
        $element2 = new ElementObject('element2', 'type', '#selector', null, '42', true);
        $elements = [
            'element1' => $element1,
            'element2' => $element2
        ];
        $section = new SectionObject('test', $elements);
        $this->assertTrue($section->hasElement('element1'));
    }

    /**
     * Assert that the section object doesn't have an element
     */
    public function testDoesntHaveElement()
    {
        $element2 = new ElementObject('element2', 'type', '#selector', null, '42', true);
        $elements = [
            'element2' => $element2
        ];
        $section = new SectionObject('test', $elements);
        $this->assertFalse($section->hasElement('element1'));
    }

    /**
     * Assert that an element object is returned
     */
    public function testGetElement()
    {
        $element1 = new ElementObject('element1', 'type', '#selector', null, '41', false);
        $element2 = new ElementObject('element2', 'type', '#selector', null, '42', true);
        $elements = [
            'element1' => $element1,
            'element2' => $element2
        ];
        $section = new SectionObject('test', $elements);
        $gotElement = $section->getElement('element2');
        $this->assertInstanceOf(ElementObject::class, $gotElement);
        $this->assertEquals($gotElement, $element2);
    }

    /**
     * Assert that null is returned if no such element
     */
    public function testNullGetElement()
    {
        $element1 = new ElementObject('element1', 'type', '#selector', null, '41', false);
        $elements = [
            'element1' => $element1
        ];
        $section = new SectionObject('test', $elements);
        $this->assertNull($section->getElement('element2'));
    }
}
