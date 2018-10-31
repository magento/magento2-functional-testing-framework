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
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;

class PageObjectHandlerTest extends MagentoTestCase
{
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
        $this->setMockParserOutput($mockData);

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
        $this->setMockParserOutput($mockData);

        // get pages
        $page = PageObjectHandler::getInstance()->getObject('testPage1');

        // Empty page has been read in and gotten without an exception being thrown.
        $this->addToAssertionCount(1);
    }

    /**
     * Function used to set mock for parser return and force init method to run between tests.
     *
     * @param array $data
     */
    private function setMockParserOutput($data)
    {
        // clear section object handler value to inject parsed content
        $property = new \ReflectionProperty(PageObjectHandler::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockSectionParser = AspectMock::double(PageParser::class, ["getData" => $data])->make();
        $instance = AspectMock::double(ObjectManager::class, ['get' => $mockSectionParser])->make();
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);
    }
}
