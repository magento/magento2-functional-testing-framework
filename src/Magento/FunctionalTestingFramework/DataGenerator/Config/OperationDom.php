<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\DataGenerator\Config;

use Magento\FunctionalTestingFramework\Config\Dom\NodeMergingConfig;
use Magento\FunctionalTestingFramework\Config\Dom\NodePathMatcher;
use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Util\Validation\DuplicateNodeValidationUtil;

/**
 * MFTF metadata.xml configuration XML DOM utility
 * @package Magento\FunctionalTestingFramework\DataGenerator\Config
 */
class OperationDom extends \Magento\FunctionalTestingFramework\Config\MftfDom
{
    const METADATA_FILE_NAME_ENDING = "meta";
    const METADATA_META_FILENAME_ATTRIBUTE = "filename";

    /**
     * Takes a dom element from xml and appends the filename based on location
     *
     * @param string $xml
     * @param string|null $filename
     * @param ExceptionCollector $exceptionCollector
     * @return \DOMDocument
     */
    public function initDom($xml, $filename = null, $exceptionCollector = null)
    {
        $dom = parent::initDom($xml);

        if (strpos($filename, self::METADATA_FILE_NAME_ENDING)) {
            $operationNodes = $dom->getElementsByTagName('operation');
            foreach ($operationNodes as $operationNode) {
                /** @var \DOMElement $operationNode */
                $operationNode->setAttribute(self::METADATA_META_FILENAME_ATTRIBUTE, $filename);
                $this->validateOperationElements(
                    $operationNode,
                    $filename,
                    'key',
                    $exceptionCollector
                );
            }
        }

        return $dom;
    }

    /**
     * Recurse through child elements and validate uniqueKeys
     * @param \DOMElement $parentNode
     * @param string $filename
     * @param string $uniqueKey
     * @param ExceptionCollector $exceptionCollector
     * @return void
     */
    public function validateOperationElements(\DOMElement $parentNode, $filename, $uniqueKey, $exceptionCollector)
    {
        DuplicateNodeValidationUtil::validateChildUniqueness(
            $parentNode,
            $filename,
            $uniqueKey,
            $exceptionCollector
        );
        $childNodes = $parentNode->childNodes;

        for ($i = 0; $i < $childNodes->length; $i++) {
            $currentNode = $childNodes->item($i);
            if (!is_a($currentNode, \DOMElement::class)) {
                continue;
            }
            $this->validateOperationElements(
                $currentNode,
                $filename,
                $uniqueKey,
                $exceptionCollector
            );
        }
    }
}
