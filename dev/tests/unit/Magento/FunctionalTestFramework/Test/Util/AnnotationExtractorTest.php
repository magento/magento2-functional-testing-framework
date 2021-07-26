<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use Exception;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Test\Util\AnnotationExtractor;
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;
use PHPUnit\Framework\TestCase;
use tests\unit\Util\TestLoggingUtil;

/**
 * Class AnnotationExtractorTest
 */
class AnnotationExtractorTest extends TestCase
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
    public function testExtractAnnotations(): void
    {
        // Test Data
        $testAnnotations = [
            'nodeName' => 'annotations',
            'features' => [
                [
                    'nodeName' => 'features',
                    'value' => 'TestFeatures'
                ]
            ],
            'stories' => [
                [
                    'nodeName' => 'stories',
                    'value' => 'TestStories'
                ]
            ],
            'description' => [
                [
                    'nodeName' => 'description',
                    'value' => 'TestDescription'
                ]
            ],
            'severity' => [
                [
                    'nodeName' => 'severity',
                    'value' => 'CRITICAL'
                ]
            ],
            'group' => [
                [
                    'nodeName' => 'group',
                    'value' => 'TestGroup'
                ]
            ],
        ];
        // Perform Test
        $extractor = new AnnotationExtractor();
        $returnedAnnotations = $extractor->extractAnnotations($testAnnotations, 'testFileName');

        // Asserts

        $this->assertEquals('TestFeatures', $returnedAnnotations['features'][0]);
        $this->assertEquals('TestStories', $returnedAnnotations['stories'][0]);
        $this->assertEquals('TestDescription', $returnedAnnotations['description'][0]);
        $this->assertEquals('CRITICAL', $returnedAnnotations['severity'][0]);
        $this->assertEquals('TestGroup', $returnedAnnotations['group'][0]);
    }

    /**
     * Annotation extractor should throw warning when required annotations are missing.
     *
     * @return void
     * @throws Exception
     */
    public function testMissingAnnotations(): void
    {
        // Test Data, missing title, description, and severity
        $testAnnotations = [
            'nodeName' => 'annotations',
            'features' => [
                [
                    'nodeName' => 'features',
                    'value' => 'TestFeatures'
                ]
            ],
            'stories' => [
                [
                    'nodeName' => 'stories',
                    'value' => 'TestStories'
                ]
            ],
            'group' => [
                [
                    'nodeName' => 'group',
                    'value' => 'TestGroup'
                ]
            ],
        ];
        // Perform Test
        $extractor = new AnnotationExtractor();
        $extractor->extractAnnotations($testAnnotations, 'testFileName');

        // Asserts
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'warning',
            'DEPRECATION: Test testFileName is missing required annotations.',
            [
                'testName' => 'testFileName',
                'missingAnnotations' => 'title, description, severity'
            ]
        );
    }

    /**
     * Annotation extractor should throw warning when required annotations are empty.
     *
     * @return void
     * @throws Exception
     */
    public function testEmptyRequiredAnnotations(): void
    {
        // Test Data, missing title, description, and severity
        $testAnnotations = [
            'nodeName' => 'annotations',
            'features' => [
                [
                    'nodeName' => 'features',
                    'value' => ''
                ]
            ],
            'stories' => [
                [
                    'nodeName' => 'stories',
                    'value' => 'TestStories'
                ]
            ],
            'title' => [
                [
                    'nodeName' => 'title',
                    'value' => ' '
                ]
            ],
            'description' => [
                [
                    'nodeName' => 'description',
                    'value' => "\t"
                ]
            ],
            'severity' => [
                [
                    'nodeName' => 'severity',
                    'value' => ''
                ]
            ],
            'group' => [
                [
                    'nodeName' => 'group',
                    'value' => 'TestGroup'
                ]
            ],
        ];
        // Perform Test
        $extractor = new AnnotationExtractor();
        $returnedAnnotations = $extractor->extractAnnotations($testAnnotations, 'testFileName');

        // Asserts
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'warning',
            'DEPRECATION: Test testFileName is missing required annotations.',
            [
                'testName' => 'testFileName',
                'missingAnnotations' => 'title, description, severity'
            ]
        );
    }

    /**
     * Validate testTestCaseIdUniqueness.
     *
     * @return void
     * @throws TestFrameworkException|XmlException
     */
    public function testTestCaseIdUniqueness(): void
    {
        // Test Data
        $firstTestAnnotation = [
            'nodeName' => 'annotations',
            'features' => [
                [
                    'nodeName' => 'features',
                    'value' => 'TestFeatures'
                ]
            ],
            'stories' => [
                [
                    'nodeName' => 'stories',
                    'value' => 'TestStories'
                ]
            ],
            'title' => [
                [
                    'nodeName' => 'title',
                    'value' => 'TEST TITLE'
                ]
            ],
            'severity' => [
                [
                    'nodeName' => 'severity',
                    'value' => 'CRITICAL'
                ]
            ],
            'testCaseId' => [
                [
                    'nodeName' => 'testCaseId',
                    'value' => 'MQE-0001'
                ]
            ],
        ];
        $secondTestannotation = [
            'nodeName' => 'annotations',
            'features' => [
                [
                    'nodeName' => 'features',
                    'value' => 'TestFeatures'
                ]
            ],
            'stories' => [
                [
                    'nodeName' => 'stories',
                    'value' => 'TestStories'
                ]
            ],
            'title' => [
                [
                    'nodeName' => 'title',
                    'value' => 'TEST TITLE'
                ]
            ],
            'severity' => [
                [
                    'nodeName' => 'severity',
                    'value' => 'CRITICAL'
                ]
            ],
            'testCaseId' => [
                [
                    'nodeName' => 'testCaseId',
                    'value' => 'MQE-0001'
                ]
            ],
        ];
        // Perform Test
        $extractor = new AnnotationExtractor();
        $extractor->extractAnnotations($firstTestAnnotation, 'firstTest');
        $extractor->extractAnnotations($secondTestannotation, 'secondTest');
        $extractor->validateTestCaseIdTitleUniqueness();

        // assert that no exception for validateTestCaseIdTitleUniqueness
        // and validation error is stored in GenerationErrorHandler
        $errorMessage = '/'
            . preg_quote('TestCaseId and Title pairs is not unique in Tests \'firstTest\', \'secondTest\'')
            . '/';
        TestLoggingUtil::getInstance()->validateMockLogStatmentRegex('error', $errorMessage, []);
        $testErrors = GenerationErrorHandler::getInstance()->getErrorsByType('test');
        $this->assertArrayHasKey('firstTest', $testErrors);
        $this->assertArrayHasKey('secondTest', $testErrors);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        GenerationErrorHandler::getInstance()->reset();
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
