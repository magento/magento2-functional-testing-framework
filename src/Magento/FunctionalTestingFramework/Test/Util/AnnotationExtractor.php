<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

/**
 * Class AnnotationExtractor
 */
class AnnotationExtractor extends BaseObjectExtractor
{
    const ANNOTATION_VALUE = 'value';
    const MAGENTO_TO_ALLURE_SEVERITY_MAP = [
        "BLOCKER" => "BLOCKER",
        "CRITICAL" => "MAJOR",
        "MAJOR" => "NORMAL",
        "AVERAGE" => "MINOR",
        "MINOR" => "TRIVIAL"
    ];

    /**
     * AnnotationExtractor constructor.
     */
    public function __construct()
    {
        // empty constructor
    }

    /**
     * This method trims away irrelevant tags and returns annotations used in the array passed. The annotations
     * can be found in both Tests and their child element tests.
     *
     * @param array $testAnnotations
     * @return array
     */
    public function extractAnnotations($testAnnotations)
    {

        $annotationObjects = [];
        $annotations = $this->stripDescriptorTags($testAnnotations, self::NODE_NAME);

        // parse the Test annotations

        foreach ($annotations as $annotationKey => $annotationData) {
            $annotationValues = [];
            // Only transform severity annotation
            if ($annotationKey == "Severity") {
                $annotationObjects[$annotationKey] = $this->transformAllureSeverityToMagento($annotationData);
                continue;
            }

            foreach ($annotationData as $annotationValue) {
                $annotationValues[] = $annotationValue[self::ANNOTATION_VALUE];
            }
            $annotationObjects[$annotationKey] = $annotationValues;
        }

        return $annotationObjects;
    }

    /**
     * This method transforms Magento severity values from Severity annotation
     * Returns Allure annotation value
     *
     * @param array $annotationData
     * @return array
     */
    public function transformAllureSeverityToMagento($annotationData)
    {
        $annotationValue = strtoupper($annotationData[0]);
        //Mapping Magento severity to Allure Severity
        //Attempts to resolve annotationValue reference against MAGENTO_TO_ALLURE_SEVERITY_MAP -
        // if not found returns without modification
        $allureAnnotation[] = self::MAGENTO_TO_ALLURE_SEVERITY_MAP[$annotationValue] ?? $annotationValue;

        return $allureAnnotation;
    }
}
