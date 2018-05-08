<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\Validation\NameValidationUtil;

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
        $this->testHookObjectExtractor = new TestHookObjectExtractor();
    }

    /**
     * This method takes and array of test data and strips away irrelevant tags. The data is converted into an array of
     * TestObjects.
     *
     * @param array $testData
     * @return TestObject
     * @throws \Exception
     */
    public function extractTestData($testData)
    {
        // validate the test name for blacklisted char (will cause allure report issues) MQE-483
        NameValidationUtil::validateName($testData[self::NAME], "Test");

        $testAnnotations = [];
        $testHooks = [];
        $filename = $testData['filename'] ?? null;
        $testReference = $testData['extends'] ?? null;
        $testActions = $this->stripDescriptorTags(
            $testData,
            self::NODE_NAME,
            self::NAME,
            self::TEST_ANNOTATIONS,
            self::TEST_BEFORE_HOOK,
            self::TEST_AFTER_HOOK,
            self::TEST_FAILED_HOOK,
            'filename',
            'extends'
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

        if (array_key_exists(self::TEST_AFTER_HOOK, $testData)) {
            // extract after
            $testHooks[self::TEST_AFTER_HOOK] = $this->testHookObjectExtractor->extractHook(
                $testData[self::NAME],
                'after',
                $testData[self::TEST_AFTER_HOOK]
            );

            // extract failed
            $testHooks[self::TEST_FAILED_HOOK] = $this->testHookObjectExtractor->createDefaultFailedHook(
                $testData[self::NAME]
            );
        }

        // TODO extract filename info and store
        try {
            return new TestObject(
                $testData[self::NAME],
                $this->actionObjectExtractor->extractActions($testActions, $testData[self::NAME]),
                $testAnnotations,
                $testHooks,
                $filename,
                $testReference
            );
        } catch (XmlException $exception) {
            throw new XmlException($exception->getMessage() . ' in Test ' . $filename);
        }
    }
}
