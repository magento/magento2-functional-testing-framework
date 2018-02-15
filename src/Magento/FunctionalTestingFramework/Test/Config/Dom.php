<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Config;

use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Config\Dom\NodeMergingConfig;
use Magento\FunctionalTestingFramework\Config\Dom\NodePathMatcher;

class Dom extends \Magento\FunctionalTestingFramework\Config\Dom
{
    const TEST_FILE_NAME_ENDING = 'Test';
    const TEST_META_FILENAME_ATTRIBUTE = 'filename';
    const TEST_META_NAME_ATTRIBUTE = 'name';
    const TEST_HOOK_NAMES = ["after", "before"];

    /**
     * TestDom constructor.
     * @param string $xml
     * @param string $filename
     * @param array $idAttributes
     * @param string $typeAttributeName
     * @param string $schemaFile
     * @param string $errorFormat
     */
    public function __construct(
        $xml,
        $filename,
        array $idAttributes = [],
        $typeAttributeName = null,
        $schemaFile = null,
        $errorFormat = self::ERROR_FORMAT_DEFAULT
    ) {
        $this->schemaFile = $schemaFile;
        $this->nodeMergingConfig = new NodeMergingConfig(new NodePathMatcher(), $idAttributes);
        $this->typeAttributeName = $typeAttributeName;
        $this->errorFormat = $errorFormat;
        $this->dom = $this->initDom($xml, $filename);
        $this->rootNamespace = $this->dom->lookupNamespaceUri($this->dom->namespaceURI);
    }

    /**
     * Takes a dom element from xml and appends the filename based on location
     *
     * @param string $xml
     * @param string|null $filename
     * @return \DOMDocument
     */
    public function initDom($xml, $filename = null)
    {
        $dom = parent::initDom($xml);

        if (strpos($filename, self::TEST_FILE_NAME_ENDING)) {
            $testNodes = $dom->getElementsByTagName('test');
            foreach ($testNodes as $testNode) {
                /** @var \DOMElement $testNode */
                $testNode->setAttribute(self::TEST_META_FILENAME_ATTRIBUTE, $filename);
                $this->validateTestDomStepKeys($testNode, $filename);
            }
        }

        return $dom;
    }

    /**
     * Redirects any merges into the init method for appending xml filename
     *
     * @param string $xml
     * @param string|null $filename
     * @return void
     */
    public function merge($xml, $filename = null)
    {
        $dom = $this->initDom($xml, $filename);
        $this->mergeNode($dom->documentElement, '');
    }

    /**
     * Parses an individual DOM structure for repeated stepKey attributes
     *
     * @param \DOMElement $testNode
     * @param string $filename
     * @return void
     * @throws XmlException
     */
    private function validateTestDomStepKeys($testNode, $filename)
    {
        $childNodes = $testNode->childNodes;

        $keyValues = [];
        for ($i = 0; $i < $childNodes->length; $i++) {
            $currentNode = $childNodes->item($i);

            if (!is_a($currentNode, \DOMElement::class)) {
                continue;
            }

            if (in_array($currentNode->nodeName, self::TEST_HOOK_NAMES)) {
                $this->validateTestDomStepKeys($currentNode, $filename);
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

            throw new XmlException("Tests cannot use stepKey more than once!\t\n{$stepKeyError}\tin file: {$filename}");
        }
    }
}
