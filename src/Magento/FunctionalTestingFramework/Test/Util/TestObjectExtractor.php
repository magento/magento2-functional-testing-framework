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
class TestObjectExtractor extends BaseObjectExtractor
{
    const TEST_ANNOTATIONS = 'annotations';
    const TEST_BEFORE_HOOK = 'before';
    const TEST_AFTER_HOOK = 'after';
    const TEST_FAILED_HOOK = 'failed';

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
     * Test Hook Object extractor
     *
     * @var TestHookObjectExtractor
     */
    private $testHookObjectExtractor;

    /**
     * TestObjectExtractor constructor.
     */
    public function __construct()
    {
        $this->actionObjectExtractor = new ActionObjectExtractor();
        $this->annotationExtractor = new AnnotationExtractor();
        $this->testEntityExtractor = new TestEntityExtractor();
        $this->testHookObjectExtractor = new TestHookObjectExtractor();
    }

    /**
     * This method takes and array of test data and strips away irrelevant tags. The data is converted into an array of
     * TestObjects.
     *
     * @param array $testData
     * @return TestObject
     * @throws \Magento\FunctionalTestingFramework\Exceptions\XmlException
     */
    public function extractTestData($testData)
    {
        // validate the test name for blacklisted char (will cause allure report issues) MQE-483
        TestNameValidationUtil::validateName($testData[self::NAME]);

        $testAnnotations = [];
        $testHooks = [];
        $filename = $testData['filename'] ?? null;
        $testActions = $this->stripDescriptorTags(
            $testData,
            self::NODE_NAME,
            self::NAME,
            self::TEST_ANNOTATIONS,
            self::TEST_BEFORE_HOOK,
            self::TEST_AFTER_HOOK,
            self::TEST_FAILED_HOOK,
            'filename'
        );

        if (array_key_exists(self::TEST_ANNOTATIONS, $testData)) {
            $testAnnotations = $this->annotationExtractor->extractAnnotations($testData[self::TEST_ANNOTATIONS]);
        }

        // extract before
        if (array_key_exists(self::TEST_BEFORE_HOOK, $testData)) {
            $testHooks[self::TEST_BEFORE_HOOK] = $this->testHookObjectExtractor->extractHook(
                $testData[self::NAME],
                'before',
                $testData[self::TEST_BEFORE_HOOK]
            );
        }

        // extract after
        if (array_key_exists(self::TEST_AFTER_HOOK, $testData)) {
            $testHooks[self::TEST_AFTER_HOOK] = $this->testHookObjectExtractor->extractHook(
                $testData[self::NAME],
                'after',
                $testData[self::TEST_AFTER_HOOK]
            );
        }

        // extract failed
        if (array_key_exists(self::TEST_AFTER_HOOK, $testData)) {
            $testHooks[self::TEST_FAILED_HOOK] = $this->testHookObjectExtractor->extractHook(
                $testData[self::NAME],
                'failed',
                $testData[self::TEST_AFTER_HOOK]
            );
        }

        // TODO extract filename info and store
        return new TestObject(
            $testData[self::NAME],
            $this->actionObjectExtractor->extractActions($testActions),
            $testAnnotations,
            $testHooks,
            $this->testEntityExtractor->extractTestEntities($testActions),
            $filename
        );
    }
}
