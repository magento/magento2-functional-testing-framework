<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

/**
 * Class AnnotationExtractor
 */
class ActionGroupAnnotationExtractor extends AnnotationExtractor
{
    const ACTION_GROUP_REQUIRED_ANNOTATIONS = [
        "description"
    ];
    const GENERATE_DOCS_COMMAND = 'generate:docs';

    /**
     * This method trims away irrelevant tags and returns annotations used in the array passed. The annotations
     * can be found in both Tests and their child element tests.
     *
     * @param array  $testAnnotations
     * @param string $filename
     * @return array
     * @throws \Exception
     */
    public function extractAnnotations($testAnnotations, $filename)
    {
        $annotationObjects = [];
        $annotations = $this->stripDescriptorTags($testAnnotations, parent::NODE_NAME);

        foreach ($annotations as $annotationKey => $annotationData) {
            $annotationObjects[$annotationKey] = $annotationData[parent::ANNOTATION_VALUE];
        }
        // TODO: Remove this when all action groups have annotations
        if ($this->isCommandDefined()) {
            $this->validateMissingAnnotations($annotationObjects, $filename);
        }

        return $annotationObjects;
    }

    /**
     * Validates given annotations against list of required annotations.
     *
     * @param array $annotationObjects
     * @return void
     * @throws \Exception
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

    /**
     * Checks if command is defined as generate:docs
     *
     * @return boolean
     */
    private function isCommandDefined()
    {
        if (defined('COMMAND') and COMMAND == self::GENERATE_DOCS_COMMAND) {
            return true;
        } else {
            return false;
        }
    }
}
