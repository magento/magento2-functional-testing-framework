<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Validation;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;

/**
 * Class SingleNodePerDocumentValidationUtil
 * @package Magento\FunctionalTestingFramework\Util\Validation
 */
class SingleNodePerFileValidationUtil
{
    /**
     * ExceptionColletor used to catch errors
     *
     * @var ExceptionCollector
     */
    private $exceptionCollector;

    /**
     * SingleNodePerDocumentValidationUtil constructor
     *
     * @param ExceptionCollector $exceptionCollector
     */
    public function __construct($exceptionCollector)
    {
        $this->exceptionCollector = $exceptionCollector;
    }

    /**
     * Validate single node per dom document for a given tag name
     *
     * @param \DOMDocument $dom
     * @param string       $tag
     * @param string       $filename
     * @return void
     */
    public function validateSingleNodeForTag($dom, $tag, $filename = '')
    {
        $tagNodes = $dom->getElementsByTagName($tag);
        $count = $tagNodes->length;
        if ($count === 1) {
            return;
        }

        $errorMsg = "Single <{$tag}> node per xml file. {$count} found in file: {$filename}\n";
        $this->exceptionCollector->addError($filename, $errorMsg);
    }
}
