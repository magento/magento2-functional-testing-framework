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
    public function setUp()
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
     * Annotation extractor should throw warning when required annotations are missing
     *
     * @throws \Exception
     */
    public function testActionGroupMissingAnnotations()
    {
        // Action Group Data, missing description
        $testAnnotations = [];
        // Perform Test
        $extractor = new ActionGroupAnnotationExtractor();
        AspectMock::double($extractor, ['isCommandDefined' => true]);
        $extractor->extractAnnotations($testAnnotations, "fileName");

        // Asserts
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'warning',
            'DEPRECATION: Action Group File fileName is missing required annotations.',
            [
                'actionGroup' => 'fileName',
                'missingAnnotations' => "description"
            ]
        );
    }

    /**
     * Annotation extractor should not throw warning when required
     * annotations are missing if command is not generate:docs
     *
     * @throws \Exception
     */
    public function testActionGroupMissingAnnotationsNoWarning()
    {
        // Action Group Data, missing description
        $testAnnotations = [];
        // Perform Test
        $extractor = new ActionGroupAnnotationExtractor();
        $extractor->extractAnnotations($testAnnotations, "fileName");

        // Asserts
        TestLoggingUtil::getInstance()->validateMockLogEmpty();
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
