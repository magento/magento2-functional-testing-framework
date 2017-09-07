<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Test\Objects\CestObject;

/**
 * Class CestObjectExtractor
 */
class CestObjectExtractor extends BaseCestObjectExtractor
{
    const CEST_ROOT = 'config';
    const CEST_ANNOTATIONS = 'annotations';
    const CEST_BEFORE_HOOK = 'before';
    const CEST_AFTER_HOOK = 'after';
    const CEST_TEST_TAG = 'test';

    /**
     * CestObjectExtractor constructor.
     */
    public function __construct()
    {
        //empty constructor
    }

    /**
     * Method receives an array parsed from xml and returns a CestObject representative of the array.
     *
     * @param array $cestData
     * @return CestObject
     */
    public function extractCest($cestData)
    {
        $annotationExtractor = new AnnotationExtractor();
        $cestHookObjectExtractor = new CestHookObjectExtractor();
        $testObjectExtractor = new TestObjectExtractor();

        $hooks = [];
        $annotations = [];

        $tests = $this->stripDescriptorTags(
            $cestData,
            self::NODE_NAME,
            self::NAME
        );

        if (array_key_exists(self::CEST_BEFORE_HOOK, $cestData)) {
            $hooks[self::CEST_BEFORE_HOOK] = $cestHookObjectExtractor->extractHook(
                self::CEST_BEFORE_HOOK,
                $cestData[self::CEST_BEFORE_HOOK]
            );

            $tests = $this->stripDescriptorTags($tests, self::CEST_BEFORE_HOOK);
        }

        if (array_key_exists(self::CEST_AFTER_HOOK, $cestData)) {
            $hooks[self::CEST_AFTER_HOOK] = $cestHookObjectExtractor->extractHook(
                self::CEST_AFTER_HOOK,
                $cestData[self::CEST_AFTER_HOOK]
            );

            $tests = $this->stripDescriptorTags($tests, self::CEST_AFTER_HOOK);
        }

        if (array_key_exists(self::CEST_ANNOTATIONS, $cestData)) {
            $annotations = $annotationExtractor->extractAnnotations($cestData[self::CEST_ANNOTATIONS]);

            $tests = $this->stripDescriptorTags($tests, self::CEST_ANNOTATIONS);
        }

        return new CestObject(
            $cestData[self::NAME],
            $annotations,
            $testObjectExtractor->extractTestData($tests),
            $hooks
        );
    }
}
