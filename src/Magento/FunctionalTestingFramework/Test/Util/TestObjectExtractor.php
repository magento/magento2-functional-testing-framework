<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Test\Objects\TestObject;

/**
 * Class TestObjectExtractor
 */
class TestObjectExtractor extends BaseCestObjectExtractor
{
    const TEST_ANNOTATIONS = 'annotations';

    /**
     * Action Object Extractor object
     *
     * @var ActionObjectExtractor
     */
    private $actionObjectExtractor;

    /**
     * Annotation Extractor object
     *
     * @var AnnotationExtractor
     */
    private $annotationExtractor;

    /**
     * Test Entity Extractor object
     *
     * @var TestEntityExtractor
     */
    private $testEntityExtractor;

    /**
     * TestObjectExtractor constructor.
     */
    public function __construct()
    {
        $this->actionObjectExtractor = new ActionObjectExtractor();
        $this->annotationExtractor = new AnnotationExtractor();
        $this->testEntityExtractor = new TestEntityExtractor();
    }

    /**
     * This method takes and array of test data and strips away irrelevant tags. The data is converted into an array of
     * TestObjects.
     *
     * @param array $cestTestData
     * @return array
     */
    public function extractTestData($cestTestData)
    {
        $testObjects = [];

        // parse the tests
        foreach ($cestTestData as $testName => $testData) {
            if (!is_array($testData)) {
                continue;
            }

            // validate the test name for blacklisted char (will cause allure report issues) MQE-483
            TestNameValidationUtil::validateName($testName);

            $testAnnotations = [];
            $testActions = $this->stripDescriptorTags(
                $testData,
                self::NODE_NAME,
                self::NAME,
                self::TEST_ANNOTATIONS
            );

            if (array_key_exists(self::TEST_ANNOTATIONS, $testData)) {
                $testAnnotations = $this->annotationExtractor->extractAnnotations($testData[self::TEST_ANNOTATIONS]);
            }

            $testObjects[$testName] = new TestObject(
                $testName,
                $this->actionObjectExtractor->extractActions($testActions),
                $testAnnotations,
                $this->testEntityExtractor->extractTestEntities($testActions)
            );
        }

        return $testObjects;
    }
}
