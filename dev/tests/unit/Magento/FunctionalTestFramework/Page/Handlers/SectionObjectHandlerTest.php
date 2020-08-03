<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Page\Handlers;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Page\Handlers\SectionObjectHandler;
use Magento\FunctionalTestingFramework\XmlParser\SectionParser;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\TestLoggingUtil;

class SectionObjectHandlerTest extends MagentoTestCase
{
    /**
     * Setup method
     */
    public function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    public function testGetSectionObject()
    {
        $mockData = [
            "testSection1" => [
                "element" => [
                    "testElement" => [
                        "type" => "input",
                        "selector" => "#element"
                    ]
                ]
            ],

            "testSection2" => [
                "element" => [
                    "testElement" => [
                        "type" => "input",
                        "selector" => "#element"
                    ]
                ]
            ]
        ];

        $this->setMockParserOutput($mockData);

        // get sections
        $sectionHandler = SectionObjectHandler::getInstance();
        $sections = $sectionHandler->getAllObjects();
        $section = $sectionHandler->getObject("testSection1");
        $invalidSection = $sectionHandler->getObject("InvalidSection");

        // perform asserts
        $this->assertCount(2, $sections);
        $this->assertArrayHasKey("testSection1", $sections);
        $this->assertArrayHasKey("testSection2", $sections);
        $this->assertNull($invalidSection);
    }

    public function testDeprecatedSection()
    {
        $mockData = [
            "testSection1" => [
                "element" => [
                    "testElement" => [
                        "type" => "input",
                        "selector" => "#element",
                        "deprecated" => "element deprecation message"
                    ]
                ],
                "filename" => "filename.xml",
                "deprecated" => "section deprecation message"
            ]
        ];

        $this->setMockParserOutput($mockData);

        // get sections
        $sectionHandler = SectionObjectHandler::getInstance();
        $section = $sectionHandler->getObject("testSection1");

        //validate deprecation warning
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'notice',
            "NOTICE: 1 Section name violations detected. See mftf.log for details.",
            []
        );
    }

    /**
     * Set the mock parser return value
     *
     * @param array $data
     */
    private function setMockParserOutput($data)
    {
        // clear section object handler value to inject parsed content
        $property = new \ReflectionProperty(SectionObjectHandler::class, "INSTANCE");
        $property->setAccessible(true);
        $property->setValue(null);

        $mockSectionParser = AspectMock::double(SectionParser::class, ["getData" => $data])->make();
        $instance = AspectMock::double(ObjectManager::class, ["get" => $mockSectionParser])->make();
        AspectMock::double(ObjectManagerFactory::class, ["getObjectManager" => $instance]);
    }

    /**
     * clean up function runs after all tests
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
