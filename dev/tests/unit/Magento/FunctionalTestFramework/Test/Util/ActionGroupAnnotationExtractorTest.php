<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\Test\Util\ActionGroupAnnotationExtractor;
use PHPUnit\Framework\TestCase;
use tests\unit\Util\TestLoggingUtil;

class ActionGroupAnnotationExtractorTest extends TestCase
{
    /**
     * Before test functionality
     * @return void
     */
    public function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Annotation extractor takes in raw array and condenses it to expected format
     *
     * @throws \Exception
     */
    public function testActionGroupExtractAnnotations()
    {
        // Test Data
        $actionGroupAnnotations = [
            "nodeName" => "annotations",
            "description" => [
                "nodeName" => "description",
                "value" => "someDescription"
            ]
        ];
        // Perform Test
        $extractor = new ActionGroupAnnotationExtractor();
        $returnedAnnotations = $extractor->extractAnnotations($actionGroupAnnotations, "fileName");

        // Asserts
        $this->assertEquals("someDescription", $returnedAnnotations['description']);
    }

    /**
     * After class functionality
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
