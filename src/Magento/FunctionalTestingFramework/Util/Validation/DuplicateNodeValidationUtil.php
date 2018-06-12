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
     * Parses through parent's children to find and flag duplicate values in given uniqueKey.
     *
     * @param \DOMElement $parentNode
     * @param string $filename
     * @param string $uniqueKey
     * @param ExceptionCollector $exceptionCollector
     * @return void
     */
    public static function validateChildUniqueness(\DOMElement $parentNode, $filename, $uniqueKey, $exceptionCollector)
    {
        $childNodes = $parentNode->childNodes;
        $type = ucfirst($parentNode->tagName);

        $keyValues = [];
        for ($i = 0; $i < $childNodes->length; $i++) {
            $currentNode = $childNodes->item($i);

            if (!is_a($currentNode, \DOMElement::class)) {
                continue;
            }

            if ($currentNode->hasAttribute($uniqueKey)) {
                $keyValues[] = $currentNode->getAttribute($uniqueKey);
            }
        }

        $withoutDuplicates = array_unique($keyValues);
        $duplicates = array_diff_assoc($keyValues, $withoutDuplicates);

        if (count($duplicates) > 0) {
            $keyError = "";
            foreach ($duplicates as $duplicateKey => $duplicateValue) {
                $keyError .= "\t{$uniqueKey}: {$duplicateValue} is used more than once.\n";
            }

            $errorMsg = "{$type} cannot use {$uniqueKey}s more than once.\t\n{$keyError}\tin file: {$filename}";
            $exceptionCollector->addError($filename, $errorMsg);
        }
    }
}
