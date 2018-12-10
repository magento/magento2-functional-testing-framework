<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Validation;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;

/**
 * Class DuplicateNodeValidationUtil
 * @package Magento\FunctionalTestingFramework\Util\Validation
 */
class DuplicateNodeValidationUtil
{
    /**
     * Key to use as unique identifier in validation
     * @var string
     */
    private $uniqueKey;

    /**
     * ExceptionColletor used to catch errors.
     * @var ExceptionCollector
     */
    private $exceptionCollector;

    /**
     * DuplicateNodeValidationUtil constructor.
     * @param string             $uniqueKey
     * @param ExceptionCollector $exceptionCollector
     */
    public function __construct($uniqueKey, $exceptionCollector)
    {
        $this->uniqueKey = $uniqueKey;
        $this->exceptionCollector = $exceptionCollector;
    }

    /**
     * Parses through parent's children to find and flag duplicate values in given uniqueKey.
     *
     * @param \DOMElement $parentNode
     * @param string      $filename
     * @return void
     */
    public function validateChildUniqueness(\DOMElement $parentNode, $filename, $parentKey)
    {
        $childNodes = $parentNode->childNodes;
        $type = ucfirst($parentNode->tagName);

        $keyValues = [];
        for ($i = 0; $i < $childNodes->length; $i++) {
            $currentNode = $childNodes->item($i);

            if (!is_a($currentNode, \DOMElement::class)) {
                continue;
            }

            if ($currentNode->hasAttribute($this->uniqueKey)) {
                $keyValues[] = $currentNode->getAttribute($this->uniqueKey);
            }
        }

        $withoutDuplicates = array_unique($keyValues);

        if (count($withoutDuplicates) != count($keyValues)) {
            $duplicates = array_diff_assoc($keyValues, $withoutDuplicates);
            $keyError = "";
            foreach ($duplicates as $duplicateKey => $duplicateValue) {
                $keyError .= "\t{$this->uniqueKey}: {$duplicateValue} is used more than once.";
                if ($parentKey !== null) {
                    $keyError .=" (Parent: {$parentKey})";
                }
                $keyError .= "\n";
            }

            $errorMsg = "{$type} cannot use {$this->uniqueKey}s more than once.\t\n{$keyError}\tin file: {$filename}";
            $this->exceptionCollector->addError($filename, $errorMsg);
        }
    }
}
