<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use Exception;
use Magento\FunctionalTestingFramework\Test\Util\ActionGroupAnnotationExtractor;
use PHPUnit\Framework\TestCase;
use tests\unit\Util\TestLoggingUtil;

/**
 * Class ActionGroupAnnotationExtractorTest
 */
class ActionGroupAnnotationExtractorTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Annotation extractor takes in raw array and condenses it to expected format.
     *
     * @return void
     * @throws Exception
     */
    public function testActionGroupExtractAnnotations(): void
    {
        // Test Data
        $actionGroupAnnotations = [
            'nodeName' => 'annotations',
            'description' => [
                'nodeName' => 'description',
                'value' => 'someDescription'
            ]
        ];
        // Perform Test
        $extractor = new ActionGroupAnnotationExtractor();
        $returnedAnnotations = $extractor->extractAnnotations($actionGroupAnnotations, 'fileName');

        // Asserts
        $this->assertEquals('someDescription', $returnedAnnotations['description']);
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
