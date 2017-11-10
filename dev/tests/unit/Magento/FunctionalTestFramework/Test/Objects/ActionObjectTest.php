<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Test\Objects;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\Page\Handlers\PageObjectHandler;
use Magento\FunctionalTestingFramework\Page\Objects\ElementObject;
use Magento\FunctionalTestingFramework\Page\Objects\PageObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Page\Handlers\SectionObjectHandler;
use Magento\FunctionalTestingFramework\Page\Objects\SectionObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ActionObjectTest
 */
class ActionObjectTest extends TestCase
{
    /**
     * The order offset should be 0 when the action is instantiated with 'before'
     */
    public function testConstructOrderBefore()
    {
        $actionObject = new ActionObject('mergeKey', 'type', [], null, 'before');
        $this->assertEquals(0, $actionObject->getOrderOffset());
    }

    /**
     * The order offset should be 1 when the action is instantiated with 'after'
     */
    public function testConstructOrderAfter()
    {
        $actionObject = new ActionObject('mergeKey', 'type', [], null, 'after');
        $this->assertEquals(1, $actionObject->getOrderOffset());
    }

    /**
     * {{Section.element}} should be replaced with #theElementSelector
     */
    public function testResolveElementInSelector()
    {
        // Set up mocks
        $actionObject = new ActionObject('merge123', 'fillField', [
            'selector' => '{{SectionObject.elementObject}}',
            'userInput' => 'Hello world'
        ]);
        $elementObject = new ElementObject('elementObject', 'button', '#replacementSelector', '42', false);
        $sectionObject = new SectionObject('SectionObject', ['elementObject' => $elementObject]);
        $instance = AspectMock::double(SectionObjectHandler::class, ['getObject' => $sectionObject])
            ->make(); // bypass the private constructor
        AspectMock::double(SectionObjectHandler::class, ['getInstance' => $instance]);

        // Call the method under test
        $actionObject->resolveReferences();

        // Verify
        $expected = [
            'selector' => '#replacementSelector',
            'userInput' => 'Hello world'
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * {{Section.element(param)}} should be replaced
     */
    public function testResolveSelectorWithOneParam()
    {
        $this->markTestIncomplete('TODO');
    }

    /**
     * {{Section.element(param1,param2)}} should be replaced
     */
    public function testResolveSelectorWithManyParams()
    {
        $this->markTestIncomplete('TODO');
    }

    /**
     * Timeout property on the ActionObject should be set if the ElementObject has a timeout
     */
    public function testTimeoutFromElement()
    {
        // Set up mocks
        $actionObject = new ActionObject('merge123', 'click', [
            'selector' => '{{SectionObject.elementObject}}'
        ]);
        $elementObject = new ElementObject('elementObject', 'button', '#replacementSelector', '42', false);
        $sectionObject = new SectionObject('SectionObject', ['elementObject' => $elementObject]);
        $instance = AspectMock::double(SectionObjectHandler::class, ['getObject' => $sectionObject])
            ->make(); // bypass the private constructor
        AspectMock::double(SectionObjectHandler::class, ['getInstance' => $instance]);

        // Call the method under test
        $actionObject->resolveReferences();

        // Verify
        $this->assertEquals(42, $actionObject->getTimeout());
    }

    /**
     * {{PageObject.url}} should be replaced with someUrl.html
     */
    public function testResolveUrl()
    {
        // Set up mocks
        $actionObject = new ActionObject('merge123', 'amOnPage', [
            'url' => '{{PageObject.url}}'
        ]);
        $pageObject = new PageObject('PageObject', '/replacement/url.html', 'Test', [], false);
        $instance = AspectMock::double(PageObjectHandler::class, ['getObject' => $pageObject])
            ->make(); // bypass the private constructor
        AspectMock::double(PageObjectHandler::class, ['getInstance' => $instance]);

        // Call the method under test
        $actionObject->resolveReferences();

        // Verify
        $expected = [
            'url' => '/replacement/url.html'
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * {{PageObject.url(param)}} should be replaced
     */
    public function testResolveUrlWithOneParam()
    {
        $this->markTestIncomplete('TODO');
    }

    /**
     * {{PageObject.url(param1,param2,param3)}} should be replaced
     */
    public function testResolveUrlWithManyParams()
    {
        $this->markTestIncomplete('TODO');
    }

    /**
     * {{EntityDataObject.key}} should be replaced with someDataValue
     */
    public function testResolveDataInUserInput()
    {
        // Set up mocks
        $actionObject = new ActionObject('merge123', 'fillField', [
            'selector' => '#selector',
            'userInput' => '{{EntityDataObject.key}}'
        ]);
        $entityDataObject = new EntityDataObject('EntityDataObject', 'test', [
            'key' => 'replacementData'
        ], [], '', '');
        $instance = AspectMock::double(DataObjectHandler::class, ['getObject' => $entityDataObject])
            ->make(); // bypass the private constructor
        AspectMock::double(DataObjectHandler::class, ['getInstance' => $instance]);

        // Call the method under test
        $actionObject->resolveReferences();

        // Verify
        $expected = [
            'selector' => '#selector',
            'userInput' => 'replacementData'
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * $testScopeData$ (single dollar sign) should be replaced
     */
    public function testTestScopeDataResolution()
    {
        $this->markTestIncomplete('TODO');
    }

    /**
     * $$cestScopeData$$ (double dollar sign) should be replaced
     */
    public function testCestScopeDataResolution()
    {
        $this->markTestIncomplete('TODO');
    }
}
