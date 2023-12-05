<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Page\Handlers;

use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Page\Handlers\PageObjectHandler;
use Magento\FunctionalTestingFramework\XmlParser\PageParser;
use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\TestLoggingUtil;

/**
 * Class PageObjectHandlerTest
 */
class PageObjectHandlerTest extends MagentoTestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Validate testGetPageObject.
     *
     * @return void
     * @throws XmlException
     */
    public function testGetPageObject(): void
    {
        $mockData = [
            'testPage1' => [
                'url' => 'testURL1',
                'module' => 'testModule1',
                'section' => [
                    'someSection1' => [],
                    'someSection2' => []
                ],
                'area' => 'test'
            ],
            'testPage2' => [
                'url' => 'testURL2',
                'module' => 'testModule2',
                'parameterized' => true,
                'section' => [
                    'someSection1' => []
                ],
                'area' => 'test'
            ]];

        $this->mockPageObjectHandlerWithData($mockData);
        $pageHandler = PageObjectHandler::getInstance();
        $pages = $pageHandler->getAllObjects();
        $pageHandler->getObject('testPage1');
        $invalidPage = $pageHandler->getObject('someInvalidPage');

        // perform asserts
        $this->assertCount(2, $pages);
        $this->assertArrayHasKey('testPage1', $pages);
        $this->assertArrayHasKey('testPage2', $pages);
        $this->assertNull($invalidPage);
    }

    /**
     * Validate testGetEmptyPage.
     *
     * @return void
     * @throws XmlException
     */
    public function testGetEmptyPage(): void
    {
        $mockData = [
            'testPage1' => [
                'url' => 'testURL1',
                'module' => 'testModule1',
                'section' => [
                ],
                'area' => 'test'
            ]];

        $this->mockPageObjectHandlerWithData($mockData);
        PageObjectHandler::getInstance()->getObject('testPage1');

        // Empty page has been read in and gotten without an exception being thrown.
        $this->addToAssertionCount(1);
    }

    /**
     * Validate testDeprecatedPage.
     *
     * @return void
     * @throws XmlException
     */
    public function testDeprecatedPage(): void
    {
        $mockData = [
            'testPage1' => [
                'url' => 'testURL1',
                'module' => 'testModule1',
                'section' => [
                ],
                'area' => 'test',
                'deprecated' => 'deprecation message',
                'filename' => 'filename.xml'
            ]];

        $this->mockPageObjectHandlerWithData($mockData);
        PageObjectHandler::getInstance()->getObject('testPage1');

        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'notice',
            'NOTICE: 1 Page name violations detected. See mftf.log for details.',
            []
        );
    }

    /**
     * Create mock page object handler with data.
     *
     * @param array $mockData
     *
     * @return void
     */
    private function mockPageObjectHandlerWithData(array $mockData): void
    {
        $pageObjectHandlerProperty = new ReflectionProperty(PageObjectHandler::class, 'INSTANCE');
        $pageObjectHandlerProperty->setAccessible(true);
        $pageObjectHandlerProperty->setValue(null, null);

        $mockSectionParser =  $this->createMock(PageParser::class);
        $mockSectionParser
            ->method('getData')
            ->willReturn($mockData);

        $objectManager = ObjectManagerFactory::getObjectManager();
        $mockObjectManagerInstance = $this->createMock(ObjectManager::class);
        $mockObjectManagerInstance
            ->method('get')
            ->will(
                $this->returnCallback(
                    function (
                        string $class,
                        array $arguments = []
                    ) use (
                        $objectManager,
                        $mockSectionParser
                    ) {
                        if ($class === PageParser::class) {
                            return $mockSectionParser;
                        }

                        return $objectManager->create($class, $arguments);
                    }
                )
            );

        $property = new ReflectionProperty(ObjectManager::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null, $mockObjectManagerInstance);
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $pageObjectHandlerProperty = new ReflectionProperty(PageObjectHandler::class, 'INSTANCE');
        $pageObjectHandlerProperty->setAccessible(true);
        $pageObjectHandlerProperty->setValue(null, null);

        $objectManagerProperty = new ReflectionProperty(ObjectManager::class, 'instance');
        $objectManagerProperty->setAccessible(true);
        $objectManagerProperty->setValue(null, null);

        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
