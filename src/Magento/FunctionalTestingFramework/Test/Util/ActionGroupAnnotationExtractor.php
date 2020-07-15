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
    /**
     * This method trims away irrelevant tags and returns annotations used in the array passed. The annotations
     * can be found in both Tests and their child element tests.
     *
     * @param array   $testAnnotations
     * @param string  $filename
     * @param boolean $validateAnnotations
     * @return array
     * @throws \Exception
     */
    public function extractAnnotations($testAnnotations, $filename, $validateAnnotations = true)
    {
        $annotationObjects = [];
        $annotations = $this->stripDescriptorTags($testAnnotations, parent::NODE_NAME);

        foreach ($annotations as $annotationKey => $annotationData) {
            $annotationObjects[$annotationKey] = $annotationData[parent::ANNOTATION_VALUE];
        }

        return $annotationObjects;
    }
}
