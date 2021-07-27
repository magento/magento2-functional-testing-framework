<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Page\Handlers;

use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Page\Handlers\SectionObjectHandler;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\ObjectHandlerUtil;
use tests\unit\Util\TestLoggingUtil;

/**
 * Class SectionObjectHandlerTest
 */
class SectionObjectHandlerTest extends MagentoTestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Validate testGetSectionObject.
     *
     * @return void
     * @throws XmlException
     */
    public function testGetSectionObject(): void
    {
        $mockData = [
            'testSection1' => [
                'element' => [
                    'testElement' => [
                        'type' => 'input',
                        'selector' => '#element'
                    ]
                ]
            ],

            'testSection2' => [
                'element' => [
                    'testElement' => [
                        'type' => 'input',
                        'selector' => '#element'
                    ]
                ]
            ]
        ];

        ObjectHandlerUtil::mockSectionObjectHandlerWithData($mockData);

        // get sections
        $sectionHandler = SectionObjectHandler::getInstance();
        $sections = $sectionHandler->getAllObjects();
        $sectionHandler->getObject('testSection1');
        $invalidSection = $sectionHandler->getObject('InvalidSection');

        // perform asserts
        $this->assertCount(2, $sections);
        $this->assertArrayHasKey('testSection1', $sections);
        $this->assertArrayHasKey('testSection2', $sections);
        $this->assertNull($invalidSection);
    }

    /**
     * Validate testDeprecatedSection.
     *
     * @return void
     * @throws XmlException
     */
    public function testDeprecatedSection(): void
    {
        $mockData = [
            'testSection1' => [
                'element' => [
                    'testElement' => [
                        'type' => 'input',
                        'selector' => '#element',
                        'deprecated' => 'element deprecation message'
                    ]
                ],
                'filename' => 'filename.xml',
                'deprecated' => 'section deprecation message'
            ]
        ];

        ObjectHandlerUtil::mockSectionObjectHandlerWithData($mockData);

        // get sections
        $sectionHandler = SectionObjectHandler::getInstance();
        $sectionHandler->getObject('testSection1');

        //validate deprecation warning
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'notice',
            'NOTICE: 1 Section name violations detected. See mftf.log for details.',
            []
        );
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
