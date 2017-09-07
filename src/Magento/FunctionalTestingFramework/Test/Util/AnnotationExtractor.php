<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

/**
 * Class AnnotationExtractor
 */
class AnnotationExtractor extends BaseCestObjectExtractor
{
    const ANNOTATION_VALUE = 'value';

    /**
     * AnnotationExtractor constructor.
     */
    public function __construct()
    {
        // empty constructor
    }

    /**
     * This method trims away irrelevant tags and returns annotations used in the array passed. The annotations
     * can be found in both Cests and their child element tests.
     *
     * @param array $cestAnnotations
     * @return array
     */
    public function extractAnnotations($cestAnnotations)
    {
        $annotationObjects = [];
        $annotations = $this->stripDescriptorTags($cestAnnotations, self::NODE_NAME);

        // parse the Cest annotations
        foreach ($annotations as $annotationKey => $annotationData) {
            $annotationValues = [];
            foreach ($annotationData as $annotationValue) {
                $annotationValues[] = $annotationValue[self::ANNOTATION_VALUE];
            }

            $annotationObjects[$annotationKey] = $annotationValues;
        }

        return $annotationObjects;
    }
}
