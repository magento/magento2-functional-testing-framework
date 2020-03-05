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
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use tests\unit\Util\TestLoggingUtil;
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;

/**
 * Class ActionObjectTest
 */
class ActionObjectTest extends MagentoTestCase
{
    /**
     * Before test functionality
     * @return void
     */
    public function setUp()
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * The order offset should be 0 when the action is instantiated with 'before'
     */
    public function testConstructOrderBefore()
    {
        $actionObject = new ActionObject('stepKey', 'type', [], null, 'before');
        $this->assertEquals(0, $actionObject->getOrderOffset());
    }

    /**
     * The order offset should be 1 when the action is instantiated with 'after'
     */
    public function testConstructOrderAfter()
    {
        $actionObject = new ActionObject('stepKey', 'type', [], null, 'after');
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
        $elementObject = new ElementObject('elementObject', 'button', '#replacementSelector', null, '42', false);
        $this->mockSectionHandlerWithElement($elementObject);

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
     * {{Section.element(param)}} should replace correctly with 'stringLiterals'
     */
    public function testResolveSelectorWithOneStringLiteral()
    {
        $actionObject = new ActionObject('key123', 'fillField', [
            'selector' => "{{SectionObject.elementObject('stringliteral')}}",
            'userInput' => 'Input'
        ]);
        $elementObject = new ElementObject('elementObject', 'button', '#{{var1}}', null, '42', true);
        $this->mockSectionHandlerWithElement($elementObject);

        // Call the method under test
        $actionObject->resolveReferences();

        // Verify
        $expected = [
            'selector' => '#stringliteral',
            'userInput' => 'Input'
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * {{Section.element(param)}} should replace correctly with {{data.key}} references
     */
    public function testResolveSelectorWithOneDataReference()
    {
        $actionObject = new ActionObject('key123', 'fillField', [
            'selector' => "{{SectionObject.elementObject(dataObject.key)}}",
            'userInput' => 'Input'
        ]);

        // Mock SectionHandler
        $elementObject = new ElementObject('elementObject', 'button', '#{{var1}}', null, '42', true);
        $this->mockSectionHandlerWithElement($elementObject);

        // Mock DataHandler
        $dataObject = new EntityDataObject('dataObject', 'dataType', ["key" => 'myValue'], null, null, null);
        $this->mockDataHandlerWithData($dataObject);

        // Call the method under test
        $actionObject->resolveReferences();

        // Verify
        $expected = [
            'selector' => '#myValue',
            'userInput' => 'Input'
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * {{Section.element(param)}} should replace correctly with $data.key$ references
     */
    public function testResolveSelectorWithOnePersistedReference()
    {
        $actionObject = new ActionObject('key123', 'fillField', [
            'selector' => '{{SectionObject.elementObject($data.key$)}}',
            'userInput' => 'Input'
        ]);

        // Mock SectionHandler
        $elementObject = new ElementObject('elementObject', 'button', '#{{var1}}', null, '42', true);
        $this->mockSectionHandlerWithElement($elementObject);

        // Call the method under test
        $actionObject->resolveReferences();

        // Verify
        $expected = [
            'selector' => '#$data.key$',
            'userInput' => 'Input'
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * {{Section.element(param1,param2,param3)}} should replace correctly with all 3 data types.
     */
    public function testResolveSelectorWithManyParams()
    {
        $actionObject = new ActionObject('key123', 'fillField', [
            'selector' => "{{SectionObject.elementObject('stringLiteral', data.key, \$data.key\$)}}",
            'userInput' => 'Input'
        ]);

        // Mock SectionHandler
        $elementObject = new ElementObject('elementObject', 'button', '#{{var1}}[{{var2}},{{var3}}]', null, '42', true);
        $this->mockSectionHandlerWithElement($elementObject);

        // Mock DataHandler
        $dataObject = new EntityDataObject('dataObject', 'dataType', ["key" => 'myValue'], null, null, null);
        $this->mockDataHandlerWithData($dataObject);

        // Call the method under test
        $actionObject->resolveReferences();

        // Verify
        $expected = [
            'selector' => '#stringLiteral[myValue,$data.key$]',
            'userInput' => 'Input'
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
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
        $elementObject = new ElementObject('elementObject', 'button', '#replacementSelector', null, '42', false);
        $this->mockSectionHandlerWithElement($elementObject);

        // Call the method under test
        $actionObject->resolveReferences();

        // Verify
        $this->assertEquals(42, $actionObject->getTimeout());
    }

    /**
     * {{PageObject.url}} should be replaced with someUrl.html
     *
     * @throws /Exception
     */
    public function testResolveUrl()
    {
        // Set up mocks
        $actionObject = new ActionObject('merge123', 'amOnPage', [
            'url' => '{{PageObject.url}}'
        ]);
        $pageObject = new PageObject('PageObject', '/replacement/url.html', 'Test', [], false, "test");
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
     * {{PageObject}} should not be replaced and should elicit a warning in console
     *
     * @throws /Exception
     */
    public function testResolveUrlWithNoAttribute()
    {
        // Set up mocks
        $actionObject = new ActionObject('merge123', 'amOnPage', [
            'url' => '{{PageObject}}'
        ]);
        $pageObject = new PageObject('PageObject', '/replacement/url.html', 'Test', [], false, "test");
        $pageObjectList = ["PageObject" => $pageObject];
        $instance = AspectMock::double(
            PageObjectHandler::class,
            ['getObject' => $pageObject, 'getAllObjects' => $pageObjectList]
        )->make(); // bypass the private constructor
        AspectMock::double(PageObjectHandler::class, ['getInstance' => $instance]);

        // Call the method under test
        $actionObject->resolveReferences();

        // Expect this warning to get generated
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            "warning",
            "page url attribute not found and is required",
            ['action' => $actionObject->getType(), 'url' => '{{PageObject}}', 'stepKey' => $actionObject->getStepKey()]
        );

        // Verify
        $expected = [
            'url' => '{{PageObject}}'
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
        $this->mockDataHandlerWithData($entityDataObject);

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
     * {{EntityDataObject.values}} should be replaced with ["value1","value2"]
     */
    public function testResolveArrayData()
    {
        // Set up mocks
        $actionObject = new ActionObject('merge123', 'fillField', [
            'selector' => '#selector',
            'userInput' => '{{EntityDataObject.values}}'
        ]);
        $entityDataObject = new EntityDataObject('EntityDataObject', 'test', [
            'values' => [
                'value1',
                'value2',
                '"My" Value'
            ]
        ], [], '', '');
        $this->mockDataHandlerWithData($entityDataObject);

        // Call the method under test
        $actionObject->resolveReferences();

        // Verify
        $expected = [
            'selector' => '#selector',
            'userInput' => '["value1","value2","\"My\" Value"]'
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * Action object should throw an exception if a reference to a parameterized selector has too few given args.
     */
    public function testTooFewArgumentException()
    {
        $this->expectException(TestReferenceException::class);

        $actionObject = new ActionObject('key123', 'fillField', [
            'selector' => "{{SectionObject.elementObject('arg1')}}",
            'userInput' => 'Input'
        ]);
        $elementObject = new ElementObject('elementObject', 'button', '#{{var1}} {{var2}}', null, '42', true);
        $this->mockSectionHandlerWithElement($elementObject);

        // Call the method under test
        $actionObject->resolveReferences();
    }

    /**
     * Action object should throw an exception if a reference to a parameterized selector has too many given args.
     */
    public function testTooManyArgumentException()
    {
        $this->expectException(TestReferenceException::class);

        $actionObject = new ActionObject('key123', 'fillField', [
            'selector' => "{{SectionObject.elementObject('arg1', 'arg2', 'arg3')}}",
            'userInput' => 'Input'
        ]);
        $elementObject = new ElementObject('elementObject', 'button', '#{{var1}}', null, '42', true);
        $this->mockSectionHandlerWithElement($elementObject);

        // Call the method under test
        $actionObject->resolveReferences();
    }

    /**
     * Action object should throw an exception if the timezone provided is not valid.
     */
    public function testInvalidTimezoneException()
    {
        $this->expectException(TestReferenceException::class);

        $actionObject = new ActionObject('key123', 'generateDate', [
            'timezone' => "INVALID_TIMEZONE"
        ]);

        // Call the method under test
        $actionObject->resolveReferences();
    }

    private function mockSectionHandlerWithElement($elementObject)
    {
        $sectionObject = new SectionObject('SectionObject', ['elementObject' => $elementObject]);
        $instance = AspectMock::double(SectionObjectHandler::class, ['getObject' => $sectionObject])
            ->make(); // bypass the private constructor
        AspectMock::double(SectionObjectHandler::class, ['getInstance' => $instance]);
    }

    private function mockDataHandlerWithData($dataObject)
    {
        $dataInstance = AspectMock::double(DataObjectHandler::class, ['getObject' => $dataObject])
            ->make();
        AspectMock::double(DataObjectHandler::class, ['getInstance' => $dataInstance]);
    }

    /**
     * After class functionality
     * @return void
     */
    public static function tearDownAfterClass()
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
