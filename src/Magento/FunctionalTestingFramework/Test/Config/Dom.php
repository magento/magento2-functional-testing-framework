<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Config;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Config\Dom\NodeMergingConfig;
use Magento\FunctionalTestingFramework\Config\Dom\NodePathMatcher;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;

class Dom extends \Magento\FunctionalTestingFramework\Config\Dom
{
    const TEST_FILE_NAME_ENDING = 'Test';
    const TEST_META_FILENAME_ATTRIBUTE = 'filename';
    const TEST_META_NAME_ATTRIBUTE = 'name';
    const TEST_HOOK_NAMES = ["after", "before"];
    const TEST_MERGE_POINTER_BEFORE = "insertBefore";
    const TEST_MERGE_POINTER_AFTER = "insertAfter";

    /**
     * TestDom constructor.
     * @param string $xml
     * @param string $filename
     * @param ExceptionCollector $exceptionCollector
     * @param array $idAttributes
     * @param string $typeAttributeName
     * @param string $schemaFile
     * @param string $errorFormat
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
        $this->schemaFile = $schemaFile;
        $this->nodeMergingConfig = new NodeMergingConfig(new NodePathMatcher(), $idAttributes);
        $this->typeAttributeName = $typeAttributeName;
        $this->errorFormat = $errorFormat;
        $this->dom = $this->initDom($xml, $filename, $exceptionCollector);
        $this->rootNamespace = $this->dom->lookupNamespaceUri($this->dom->namespaceURI);
    }

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

        if (strpos($filename, self::TEST_FILE_NAME_ENDING)) {
            $testNodes = $dom->getElementsByTagName('test');
            foreach ($testNodes as $testNode) {
                /** @var \DOMElement $testNode */
                $testNode->setAttribute(self::TEST_META_FILENAME_ATTRIBUTE, $filename);
                $this->validateDomStepKeys($testNode, $filename, 'Test', $exceptionCollector);
                if ($testNode->getAttribute(self::TEST_MERGE_POINTER_AFTER) !== "") {
                    $this->appendMergePointerToActions(
                        $testNode,
                        self::TEST_MERGE_POINTER_AFTER,
                        $testNode->getAttribute(self::TEST_MERGE_POINTER_AFTER),
                        $filename,
                        $exceptionCollector
                    );
                } elseif ($testNode->getAttribute(self::TEST_MERGE_POINTER_BEFORE) !== "") {
                    $this->appendMergePointerToActions(
                        $testNode,
                        self::TEST_MERGE_POINTER_BEFORE,
                        $testNode->getAttribute(self::TEST_MERGE_POINTER_BEFORE),
                        $filename,
                        $exceptionCollector
                    );
                }
            }
        }

        return $dom;
    }

    /**
     * Redirects any merges into the init method for appending xml filename
     *
     * @param string $xml
     * @param string|null $filename
     * @param ExceptionCollector $exceptionCollector
     * @return void
     */
    public function merge($xml, $filename = null, $exceptionCollector = null)
    {
        $dom = $this->initDom($xml, $filename, $exceptionCollector);
        $this->mergeNode($dom->documentElement, '');
    }

    /**
     * Parses DOM Structure's actions and appends a before/after attribute along with the parent's stepkey reference.
     *
     * @param \DOMElement $testNode
     * @param string $insertType
     * @param string $insertKey
     * @param string $filename
     * @param ExceptionCollector $exceptionCollector
     * @return void
     */
    protected function appendMergePointerToActions($testNode, $insertType, $insertKey, $filename, $exceptionCollector)
    {
        $childNodes = $testNode->childNodes;
        $previousStepKey = $insertKey;
        $actionInsertType = ActionObject::MERGE_ACTION_ORDER_AFTER;
        if ($insertType == self::TEST_MERGE_POINTER_BEFORE) {
            $actionInsertType = ActionObject::MERGE_ACTION_ORDER_BEFORE;
        }
        for ($i = 0; $i < $childNodes->length; $i++) {
            $currentNode = $childNodes->item($i);
            if (!is_a($currentNode, \DOMElement::class)) {
                continue;
            }
            if ($currentNode->hasAttribute('stepKey')) {
                if ($currentNode->hasAttribute($insertType) && $testNode->hasAttribute($insertType)) {
                    $errorMsg = "Actions cannot have merge pointers if contained in tests that has a merge pointer.";
                    $errorMsg .= "\n\tstepKey: {$currentNode->getAttribute('stepKey')}\tin file: {$filename}";
                    $exceptionCollector->addError($filename, $errorMsg);
                }
                $currentNode->setAttribute($actionInsertType, $previousStepKey);
                $previousStepKey = $currentNode->getAttribute('stepKey');
                // All actions after the first need to insert AFTER.
                $actionInsertType = ActionObject::MERGE_ACTION_ORDER_AFTER;
            }
        }
    }

    /**
     * Parses an individual DOM structure for repeated stepKey attributes
     *
     * @param \DOMElement $testNode
     * @param string $filename
     * @param string $type
     * @param ExceptionCollector $exceptionCollector
     * @return void
     * @throws XmlException
     */
    protected function validateDomStepKeys($testNode, $filename, $type, $exceptionCollector)
    {
        $childNodes = $testNode->childNodes;

        $keyValues = [];
        for ($i = 0; $i < $childNodes->length; $i++) {
            $currentNode = $childNodes->item($i);

            if (!is_a($currentNode, \DOMElement::class)) {
                continue;
            }

            if (in_array($currentNode->nodeName, self::TEST_HOOK_NAMES)) {
                $this->validateDomStepKeys($currentNode, $filename, $type, $exceptionCollector);
            }

            if ($currentNode->hasAttribute('stepKey')) {
                $keyValues[] = $currentNode->getAttribute('stepKey');
            }
        }

        $withoutDuplicates = array_unique($keyValues);
        $duplicates = array_diff_assoc($keyValues, $withoutDuplicates);

        if (count($duplicates) > 0) {
            $stepKeyError = "";
            foreach ($duplicates as $duplicateKey => $duplicateValue) {
                $stepKeyError .= "\tstepKey: {$duplicateValue} is used more than once.\n";
            }

            $errorMsg = "{$type}s cannot use stepKey more than once.\t\n{$stepKeyError}\tin file: {$filename}";
            $exceptionCollector->addError($filename, $errorMsg);
        }
    }
}
