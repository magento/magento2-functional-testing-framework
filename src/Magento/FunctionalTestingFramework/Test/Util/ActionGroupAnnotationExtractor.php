<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

/**
 * Class AnnotationExtractor
 */
class ActionGroupAnnotationExtractor extends AnnotationExtractor
{
    const ACTION_GROUP_REQUIRED_ANNOTATIONS = [
        "description",
        "page",
    ];
    const GENERATE_DOCS_COMMAND = 'generate:docs';

    /**
     * This method trims away irrelevant tags and returns annotations used in the array passed. The annotations
     * can be found in both Tests and their child element tests.
     *
     * @param array  $testAnnotations
     * @param string $filename
     * @return array
     * @throws XmlException
     */
    public function extractAnnotations($testAnnotations, $filename)
    {
        $annotationObjects = [];
        $annotations = $this->stripDescriptorTags($testAnnotations, parent::NODE_NAME);

        // parse the Test annotations

        foreach ($annotations as $annotationKey => $annotationData) {
            $annotationObjects[$annotationKey] = $annotationData[parent::ANNOTATION_VALUE];
        }
        if(defined('COMMAND') and COMMAND == self::GENERATE_DOCS_COMMAND) {
            $this->validateMissingAnnotations($annotationObjects, $filename);
        }

        return $annotationObjects;
    }

    /**
     * Validates given annotations against list of required annotations.
     * @param array $annotationObjects
     * @return void
     */
    private function validateMissingAnnotations($annotationObjects, $filename)
    {
        $missingAnnotations = [];

        foreach (self::ACTION_GROUP_REQUIRED_ANNOTATIONS as $REQUIRED_ANNOTATION) {
            if (!array_key_exists($REQUIRED_ANNOTATION, $annotationObjects)) {
                $missingAnnotations[] = $REQUIRED_ANNOTATION;
            }
        }

        if (!empty($missingAnnotations)) {
            $message = "Action Group File {$filename} is missing required annotations.";
            LoggingUtil::getInstance()->getLogger(ActionObject::class)->deprecation(
                $message,
                ["actionGroup" => $filename, "missingAnnotations" => implode(", ", $missingAnnotations)]
            );
        }
    }
}
