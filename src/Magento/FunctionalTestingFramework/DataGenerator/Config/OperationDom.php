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
    const METADATA_META_NAME_ATTRIBUTE = "name";

    /**
     * NodeValidationUtil
     * @var DuplicateNodeValidationUtil
     */
    private $validationUtil;

    /**
     * Metadata Dom constructor.
     * @param string             $xml
     * @param string             $filename
     * @param ExceptionCollector $exceptionCollector
     * @param array              $idAttributes
     * @param string             $typeAttributeName
     * @param string             $schemaFile
     * @param string             $errorFormat
     */
    public function __construct(
        $xml,
        $filename,
        $exceptionCollector,
        array $idAttributes = [],
        $typeAttributeName = null,
        $schemaFile = null,
        $errorFormat = self::ERROR_FORMAT_DEFAULT
    ) {
        $this->validationUtil = new DuplicateNodeValidationUtil('key', $exceptionCollector);
        parent::__construct(
            $xml,
            $filename,
            $exceptionCollector,
            $idAttributes,
            $typeAttributeName,
            $schemaFile,
            $errorFormat
        );
    }

    /**
     * Takes a dom element from xml and appends the filename based on location
     *
     * @param string      $xml
     * @param string|null $filename
     * @return \DOMDocument
     */
    public function initDom($xml, $filename = null)
    {
        $dom = parent::initDom($xml, $filename);

        if (strpos($filename, self::METADATA_FILE_NAME_ENDING)) {
            $operationNodes = $dom->getElementsByTagName('operation');
            foreach ($operationNodes as $operationNode) {
                /** @var \DOMElement $operationNode */
                $operationNode->setAttribute(self::METADATA_META_FILENAME_ATTRIBUTE, $filename);
                $this->validateOperationElements(
                    $operationNode,
                    $filename,
                    $operationNode->getAttribute(self::METADATA_META_NAME_ATTRIBUTE)
                );
            }
        }

        return $dom;
    }

    /**
     * Recurse through child elements and validate uniqueKeys
     * @param \DOMElement $parentNode
     * @param string      $filename
     * @param string      $topParent
     * @return void
     */
    public function validateOperationElements(\DOMElement $parentNode, $filename, $topParent)
    {
        $this->validationUtil->validateChildUniqueness(
            $parentNode,
            $filename,
            $topParent
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
                $topParent
            );
        }
    }
}
