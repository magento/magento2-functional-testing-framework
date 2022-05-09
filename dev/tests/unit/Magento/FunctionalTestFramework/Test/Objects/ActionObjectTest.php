<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Test\Objects;

use Exception;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Page\Handlers\PageObjectHandler;
use Magento\FunctionalTestingFramework\Page\Handlers\SectionObjectHandler;
use Magento\FunctionalTestingFramework\Page\Objects\ElementObject;
use Magento\FunctionalTestingFramework\Page\Objects\PageObject;
use Magento\FunctionalTestingFramework\Page\Objects\SectionObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\TestLoggingUtil;

/**
 * Class ActionObjectTest
 */
class ActionObjectTest extends MagentoTestCase
{
    /**
     * Before test functionality.
     *
     * @return void
     */
    protected function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * The order offset should be 0 when the action is instantiated with 'before'.
     *
     * @return void
     */
    public function testConstructOrderBefore(): void
    {
        $actionObject = new ActionObject('stepKey', 'type', [], null, 'before');
        $this->assertEquals(0, $actionObject->getOrderOffset());
    }

    /**
     * The order offset should be 1 when the action is instantiated with 'after'.
     *
     * @return void
     */
    public function testConstructOrderAfter(): void
    {
        $actionObject = new ActionObject('stepKey', 'type', [], null, 'after');
        $this->assertEquals(1, $actionObject->getOrderOffset());
    }

    /**
     * {{Section.element}} should be replaced with #theElementSelector.
     *
     * @return void
     * @throws TestReferenceException
     * @throws XmlException
     */
    public function testResolveElementInSelector(): void
    {
        // Set up mocks
        $actionObject = new ActionObject('merge123', 'fillField', [
            'selector' => '{{SectionObject.elementObject}}',
            'userInput' => 'Hello world',
            'requiredCredentials' => ''
        ]);
        $elementObject = new ElementObject('elementObject', 'button', '#replacementSelector', null, '42', false);
        $this->mockSectionHandlerWithElement($elementObject);

        // Call the method under test
        $actionObject->resolveReferences();

        // Verify
        $expected = [
            'selector' => '#replacementSelector',
            'userInput' => 'Hello world',
            'requiredCredentials' => ''
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * {{Section.element(param)}} should replace correctly with 'stringLiterals'.
     *
     * @return void
     * @throws TestReferenceException
     * @throws XmlException
     */
    public function testResolveSelectorWithOneStringLiteral(): void
    {
        $actionObject = new ActionObject('key123', 'fillField', [
            'selector' => "{{SectionObject.elementObject('stringliteral')}}",
            'userInput' => 'Input',
            'requiredCredentials' => ''
        ]);
        $elementObject = new ElementObject('elementObject', 'button', '#{{var1}}', null, '42', true);
        $this->mockSectionHandlerWithElement($elementObject);

        // Call the method under test
        $actionObject->resolveReferences();

        // Verify
        $expected = [
            'selector' => '#stringliteral',
            'userInput' => 'Input',
            'requiredCredentials' => ''
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * {{Section.element(param)}} should replace correctly with {{data.key}} references.
     *
     * @return void
     * @throws TestReferenceException
     * @throws XmlException
     */
    public function testResolveSelectorWithOneDataReference(): void
    {
        $actionObject = new ActionObject('key123', 'fillField', [
            'selector' => "{{SectionObject.elementObject(dataObject.key)}}",
            'userInput' => 'Input',
            'requiredCredentials' => ''
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
            'userInput' => 'Input',
            'requiredCredentials' => ''
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * {{Section.element(param)}} should replace correctly with $data.key$ references.
     *
     * @return void
     * @throws TestReferenceException
     * @throws XmlException
     */
    public function testResolveSelectorWithOnePersistedReference(): void
    {
        $actionObject = new ActionObject('key123', 'fillField', [
            'selector' => '{{SectionObject.elementObject($data.key$)}}',
            'userInput' => 'Input',
            'requiredCredentials' => ''
        ]);

        // Mock SectionHandler
        $elementObject = new ElementObject('elementObject', 'button', '#{{var1}}', null, '42', true);
        $this->mockSectionHandlerWithElement($elementObject);

        // Call the method under test
        $actionObject->resolveReferences();

        // Verify
        $expected = [
            'selector' => '#$data.key$',
            'userInput' => 'Input',
            'requiredCredentials' => ''
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * {{Section.element(param1,param2,param3)}} should replace correctly with all 3 data types.
     *
     * @return void
     * @throws TestReferenceException
     * @throws XmlException
     */
    public function testResolveSelectorWithManyParams(): void
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
            'userInput' => 'Input',
            'requiredCredentials' => ''
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * Timeout property on the ActionObject should be set if the ElementObject has a timeout.
     *
     * @return void
     * @throws TestReferenceException
     * @throws XmlException
     */
    public function testTimeoutFromElement(): void
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
     * {{PageObject.url}} should be replaced with someUrl.html.
     *
     * @return void
     * @throws Exception
     */
    public function testResolveUrl(): void
    {
        // Set up mocks
        $actionObject = new ActionObject('merge123', 'amOnPage', [
            'url' => '{{PageObject.url}}'
        ]);
        $pageObject = new PageObject('PageObject', '/replacement/url.html', 'Test', [], false, "test");

        $instance = $this->createMock(PageObjectHandler::class);
        $instance
            ->method('getObject')
            ->willReturn($pageObject);
        // bypass the private constructor
        $property = new ReflectionProperty(PageObjectHandler::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue($instance);

        // Call the method under test
        $actionObject->resolveReferences();

        // Verify
        $expected = [
            'url' => '/replacement/url.html','requiredCredentials' => ''
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * {{PageObject}} should not be replaced and should elicit a warning in console.
     *
     * @return void
     * @throws Exception
     */
    public function testResolveUrlWithNoAttribute(): void
    {
        $this->expectException(TestReferenceException::class);

        // Set up mocks
        $actionObject = new ActionObject('merge123', 'amOnPage', [
            'url' => '{{PageObject}}'
        ]);
        $pageObject = new PageObject('PageObject', '/replacement/url.html', 'Test', [], false, "test");
        $pageObjectList = ["PageObject" => $pageObject];

        $instance = $this->createMock(PageObjectHandler::class);
        $instance
            ->method('getObject')
            ->willReturn($pageObject);
        $instance
            ->method('getAllObjects')
            ->willReturn($pageObjectList);
        // bypass the private constructor
        $property = new ReflectionProperty(PageObjectHandler::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue($instance);

        // Call the method under test
        $actionObject->resolveReferences();
    }

    /**
     * {{PageObject.url(param)}} should be replaced.
     *
     * @return void
     */
    public function testResolveUrlWithOneParam(): void
    {
        $this->markTestIncomplete('TODO');
    }

    /**
     * {{PageObject.url(param1,param2,param3)}} should be replaced.
     *
     * @return void
     */
    public function testResolveUrlWithManyParams(): void
    {
        $this->markTestIncomplete('TODO');
    }

    /**
     * {{EntityDataObject.key}} should be replaced with someDataValue.
     *
     * @return void
     * @throws TestReferenceException
     * @throws XmlException
     */
    public function testResolveDataInUserInput(): void
    {
        // Set up mocks
        $actionObject = new ActionObject('merge123', 'fillField', [
            'selector' => '#selector',
            'userInput' => '{{EntityDataObject.key}}',
            'requiredCredentials' => ''
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
            'userInput' => 'replacementData',
            'requiredCredentials' => ''
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * {{EntityDataObject.values}} should be replaced with ["value1","value2"].
     *
     * @return void
     * @throws TestReferenceException
     * @throws XmlException
     */
    public function testResolveArrayData(): void
    {
        // Set up mocks
        $actionObject = new ActionObject('merge123', 'fillField', [
            'selector' => '#selector',
            'userInput' => '{{EntityDataObject.values}}',
            'requiredCredentials' => ''
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
       //Verify
        $expected = [
            'selector' => '#selector',
            'userInput' => '["value1","value2","\"My\" Value"]',
            'requiredCredentials' => ''
        ];
        $this->assertEquals($expected, $actionObject->getCustomActionAttributes());
    }

    /**
     * Action object should throw an exception if a reference to a parameterized selector has too few given args.
     *
     * @return void
     * @throws TestReferenceException
     * @throws XmlException
     */
    public function testTooFewArgumentException(): void
    {
        $this->expectException(TestReferenceException::class);

        $actionObject = new ActionObject('key123', 'fillField', [
            'selector' => "{{SectionObject.elementObject('arg1')}}",
            'userInput' => 'Input',
            'requiredCredentials' => ''
        ]);
        $elementObject = new ElementObject('elementObject', 'button', '#{{var1}} {{var2}}', null, '42', true);
        $this->mockSectionHandlerWithElement($elementObject);

        // Call the method under test
        $actionObject->resolveReferences();
    }

    /**
     * Action object should throw an exception if a reference to a parameterized selector has too many given args.
     *
     * @return void
     * @throws TestReferenceException
     * @throws XmlException
     */
    public function testTooManyArgumentException(): void
    {
        $this->expectException(TestReferenceException::class);

        $actionObject = new ActionObject('key123', 'fillField', [
            'selector' => "{{SectionObject.elementObject('arg1', 'arg2', 'arg3')}}",
            'userInput' => 'Input',
            'requiredCredentials' => ''
        ]);
        $elementObject = new ElementObject('elementObject', 'button', '#{{var1}}', null, '42', true);
        $this->mockSectionHandlerWithElement($elementObject);

        // Call the method under test
        $actionObject->resolveReferences();
    }

    /**
     * Action object should throw an exception if the timezone provided is not valid.
     *
     * @return void
     * @throws TestReferenceException
     * @throws XmlException
     */
    public function testInvalidTimezoneException(): void
    {
        $this->expectException(TestReferenceException::class);

        $actionObject = new ActionObject('key123', 'generateDate', [
            'timezone' => "INVALID_TIMEZONE",
            'requiredCredentials' => ''
        ]);

        // Call the method under test
        $actionObject->resolveReferences();
    }

    /**
     * Mock section handler with the specified ElementObject.
     *
     * @param ElementObject $elementObject
     *
     * @return void
     * @throws Exception
     */
    private function mockSectionHandlerWithElement(ElementObject $elementObject): void
    {
        $sectionObject = new SectionObject('SectionObject', ['elementObject' => $elementObject]);
        $instance = $this->createMock(SectionObjectHandler::class);
        $instance
            ->method('getObject')
            ->willReturn($sectionObject);
        // bypass the private constructor
        $property = new ReflectionProperty(SectionObjectHandler::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue($instance);
    }

    /**
     * Mock data handler with the specified EntityDataObject.
     *
     * @param EntityDataObject $dataObject
     *
     * @return void
     * @throws Exception
     */
    private function mockDataHandlerWithData(EntityDataObject $dataObject): void
    {
        $dataInstance = $this->createMock(DataObjectHandler::class);
        $dataInstance
            ->method('getObject')
            ->willReturn($dataObject);
        // bypass the private constructor
        $property = new ReflectionProperty(DataObjectHandler::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue($dataInstance);
    }

    /**
     * After class functionality.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
