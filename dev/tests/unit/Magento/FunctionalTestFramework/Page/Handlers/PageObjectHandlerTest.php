<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Page\Handlers;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Page\Handlers\PageObjectHandler;
use Magento\FunctionalTestingFramework\XmlParser\PageParser;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\ObjectHandlerUtil;
use tests\unit\Util\TestLoggingUtil;

class PageObjectHandlerTest extends MagentoTestCase
{
    /**
     * Setup method
     */
    public function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    public function testGetPageObject()
    {
        $mockData = [
            "testPage1" => [
                "url" => "testURL1",
                "module" => "testModule1",
                "section" => [
                    "someSection1" => [],
                    "someSection2" => []
                ],
                "area" => "test"
            ],
            "testPage2" => [
                "url" => "testURL2",
                "module" => "testModule2",
                "parameterized" => true,
                "section" => [
                    "someSection1" => []
                ],
                "area" => "test"
            ]];
        ObjectHandlerUtil::mockPageObjectHandlerWithData($mockData);

        // get pages
        $pageHandler = PageObjectHandler::getInstance();
        $pages = $pageHandler->getAllObjects();
        $page = $pageHandler->getObject('testPage1');
        $invalidPage = $pageHandler->getObject('someInvalidPage');

        // perform asserts
        $this->assertCount(2, $pages);
        $this->assertArrayHasKey("testPage1", $pages);
        $this->assertArrayHasKey("testPage2", $pages);
        $this->assertNull($invalidPage);
    }

    public function testGetEmptyPage()
    {
        $mockData = [
            "testPage1" => [
                "url" => "testURL1",
                "module" => "testModule1",
                "section" => [
                ],
                "area" => "test"
            ]];
        ObjectHandlerUtil::mockPageObjectHandlerWithData($mockData);

        // get pages
        $page = PageObjectHandler::getInstance()->getObject('testPage1');

        // Empty page has been read in and gotten without an exception being thrown.
        $this->addToAssertionCount(1);
    }

    public function testDeprecatedPage()
    {
        $mockData = [
            "testPage1" => [
                "url" => "testURL1",
                "module" => "testModule1",
                "section" => [
                ],
                "area" => "test",
                "deprecated" => "deprecation message",
                "filename" => "filename.xml"
            ]];
        ObjectHandlerUtil::mockPageObjectHandlerWithData($mockData);

        // get pages
        $page = PageObjectHandler::getInstance()->getObject('testPage1');

        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'notice',
            "NOTICE: 1 Page name violations detected. See mftf.log for details.",
            []
        );
    }

    /**
     * clean up function runs after all tests
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
