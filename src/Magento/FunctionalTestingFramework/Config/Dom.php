<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Config;

use Magento\FunctionalTestingFramework\Config\Dom\ValidationException;

/**
 * Magento configuration XML DOM utility
 */
class Dom
{
    /**
     * Prefix which will be used for root namespace
     */
    const ROOT_NAMESPACE_PREFIX = 'x';

    /**
     * Format of items in errors array to be used by default. Available placeholders - fields of \LibXMLError.
     */
    const ERROR_FORMAT_DEFAULT = "%message%\nLine: %line%\n";

    /**
     * Dom document
     *
     * @var \DOMDocument
     */
    protected $dom;

    /**
     * Configuration of identifier attributes to be taken into account during merging.
     *
     * @var Dom\NodeMergingConfig
     */
    protected $nodeMergingConfig;

    /**
     * Name of attribute that specifies type of argument node
     *
     * @var string|null
     */
    protected $typeAttributeName;

    /**
     * Schema validation file
     *
     * @var string
     */
    protected $schemaFile;

    /**
     * Format of error messages
     *
     * @var string
     */
    protected $errorFormat;

    /**
     * Default namespace for xml elements
     *
     * @var string
     */
    protected $rootNamespace;

    /**
     * Build DOM with initial XML contents and specifying identifier attributes for merging
     *
     * Format of $idAttributes: array('/xpath/to/some/node' => 'id_attribute_name')
     * The path to ID attribute name should not include any attribute notations or modifiers -- only node names
     *
     * @param string $xml
     * @param array  $idAttributes
     * @param string $typeAttributeName
     * @param string $schemaFile
     * @param string $errorFormat
     */
    public function __construct(
        $xml,
        array $idAttributes = [],
        $typeAttributeName = null,
        $schemaFile = null,
        $errorFormat = self::ERROR_FORMAT_DEFAULT
    ) {
        $this->schemaFile = $schemaFile;
        $this->nodeMergingConfig = new Dom\NodeMergingConfig(new Dom\NodePathMatcher(), $idAttributes);
        $this->typeAttributeName = $typeAttributeName;
        $this->errorFormat = $errorFormat;
        $this->dom = $this->initDom($xml);
        $this->rootNamespace = $this->dom->lookupNamespaceUri($this->dom->namespaceURI);
    }

    /**
     * Merge $xml into DOM document
     *
     * @param string             $xml
     * @param string             $filename
     * @param ExceptionCollector $exceptionCollector
     * @return void
     */
    public function merge($xml, $filename = null, $exceptionCollector = null)
    {
        $dom = $this->initDom($xml, $filename, $exceptionCollector);
        $this->mergeNode($dom->documentElement, '');
    }

    /**
     * Recursive merging of the \DOMElement into the original document
     *
     * Algorithm:
     * 1. Find the same node in original document
     * 2. Extend and override original document node attributes and scalar value if found
     * 3. Append new node if original document doesn't have the same node
     *
     * @param \DOMElement $node
     * @param string      $parentPath Path to parent node.
     * @return void
     */
    protected function mergeNode(\DOMElement $node, $parentPath)
    {
        $path = $this->getNodePathByParent($node, $parentPath);

        $matchedNode = $this->getMatchedNode($path);

        /* Update matched node attributes and value */
        if ($matchedNode) {
            $this->mergeMatchingNode($node, $parentPath, $matchedNode, $path);
        } else {
            /* Add node as is to the document under the same parent element */
            $parentMatchedNode = $this->getMatchedNode($parentPath);
            $newNode = $this->dom->importNode($node, true);
            $parentMatchedNode->appendChild($newNode);
        }
    }

    /**
     * Function to process matching node merges. Broken into shared logic for extending classes.
     *
     * @param \DomElement $node
     * @param string      $parentPath
     * @param |DomElement $matchedNode
     * @param string      $path
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @TODO Ported magento code - to be refactored later
     */
    protected function mergeMatchingNode(\DomElement $node, $parentPath, $matchedNode, $path)
    {
        //different node type
        if ($this->typeAttributeName
            && $node->hasAttribute($this->typeAttributeName)
            && $matchedNode->hasAttribute($this->typeAttributeName)
            && $node->getAttribute($this->typeAttributeName)
            !== $matchedNode->getAttribute($this->typeAttributeName)
        ) {
            $this->replaceNodeValue($parentPath, $node, $matchedNode);
            return;
        }

        $this->mergeAttributes($matchedNode, $node);
        if ($node->nodeValue === '' && $matchedNode->nodeValue !== '' && $matchedNode->childNodes->length === 1) {
            $this->replaceNodeValue($parentPath, $node, $matchedNode);
        }
        if (!$node->hasChildNodes()) {
            return;
        }
        /* override node value */
        if ($this->isTextNode($node)) {
            /* skip the case when the matched node has children, otherwise they get overridden */
            if (!$matchedNode->hasChildNodes() || $this->isTextNode($matchedNode)) {
                $matchedNode->nodeValue = $node->childNodes->item(0)->nodeValue;
            }
        } else {
            /* recursive merge for all child nodes */
            foreach ($node->childNodes as $childNode) {
                if ($childNode instanceof \DOMElement) {
                    $this->mergeNode($childNode, $path);
                }
            }
        }
    }

    /**
     * Replace node value.
     *
     * @param string      $parentPath
     * @param \DOMElement $node
     * @param \DOMElement $matchedNode
     *
     * @return void
     */
    protected function replaceNodeValue($parentPath, \DOMElement $node, \DOMElement $matchedNode)
    {
        $parentMatchedNode = $this->getMatchedNode($parentPath);
        $newNode = $this->dom->importNode($node, true);
        $parentMatchedNode->replaceChild($newNode, $matchedNode);
    }

    /**
     * Check if the node content is text
     *
     * @param \DOMElement $node
     * @return boolean
     */
    protected function isTextNode($node)
    {
        return $node->childNodes->length == 1 && $node->childNodes->item(0) instanceof \DOMText;
    }

    /**
     * Merges attributes of the merge node to the base node
     *
     * @param \DOMElement $baseNode
     * @param \DOMNode    $mergeNode
     * @return void
     */
    protected function mergeAttributes($baseNode, $mergeNode)
    {
        foreach ($mergeNode->attributes as $attribute) {
            // Do not overwrite filename of base node
            if ($attribute->name === "filename") {
                $baseNode->setAttribute(
                    $this->getAttributeName($attribute),
                    $baseNode->getAttribute("filename") . "," . $attribute->value
                );
                continue;
            }
            $baseNode->setAttribute($this->getAttributeName($attribute), $attribute->value);
        }
    }

    /**
     * Identify node path based on parent path and node attributes
     *
     * @param \DOMElement $node
     * @param string      $parentPath
     * @return string
     */
    protected function getNodePathByParent(\DOMElement $node, $parentPath)
    {
        $prefix = $this->rootNamespace === null ? '' : self::ROOT_NAMESPACE_PREFIX . ':';
        $path = $parentPath . '/' . $prefix . $node->tagName;
        $idAttribute = $this->nodeMergingConfig->getIdAttribute($path);
        if ($idAttribute) {
            foreach (explode('|', $idAttribute) as $idAttributeValue) {
                if ($node->hasAttribute($idAttributeValue)) {
                    $path .= "[@{$idAttributeValue}='" . $node->getAttribute($idAttributeValue) . "']";
                    break;
                }
            }
        }
        return $path;
    }

    /**
     * Getter for node by path
     * An exception is possible if original document contains multiple nodes for identifier
     *
     * @param string $nodePath
     * @throws \Exception
     * @return \DOMElement|null
     */
    protected function getMatchedNode($nodePath)
    {
        $xPath = new \DOMXPath($this->dom);
        if ($this->rootNamespace) {
            $xPath->registerNamespace(self::ROOT_NAMESPACE_PREFIX, $this->rootNamespace);
        }
        $matchedNodes = $xPath->query($nodePath);
        $node = null;
        if ($matchedNodes->length > 1) {
            throw new \Exception("More than one node matching the query: {$nodePath}");
        } elseif ($matchedNodes->length == 1) {
            $node = $matchedNodes->item(0);
        }
        return $node;
    }

    /**
     * Validate dom document
     *
     * @param \DOMDocument $dom
     * @param string       $schemaFileName
     * @param string       $errorFormat
     * @return array of errors
     * @throws \Exception
     */
    public static function validateDomDocument(
        \DOMDocument $dom,
        $schemaFileName,
        $errorFormat = self::ERROR_FORMAT_DEFAULT
    ) {
        libxml_use_internal_errors(true);
        try {
            $result = $dom->schemaValidate($schemaFileName);
            $errors = [];
            if (!$result) {
                $validationErrors = libxml_get_errors();
                if (count($validationErrors)) {
                    foreach ($validationErrors as $error) {
                        $errors[] = self::renderErrorMessage($error, $errorFormat);
                    }
                } else {
                    $errors[] = 'Unknown validation error';
                }
            }
        } catch (\Exception $exception) {
            libxml_use_internal_errors(false);
            throw new \Exception(
                sprintf(
                    'Failed to validate xml using schema: %s. Exception: %s',
                    $schemaFileName,
                    $exception->getMessage()
                )
            );
        }
        libxml_use_internal_errors(false);
        return $errors;
    }

    /**
     * Render error message string by replacing placeholders '%field%' with properties of \LibXMLError
     *
     * @param \LibXMLError $errorInfo
     * @param string       $format
     * @return string
     * @throws \InvalidArgumentException
     */
    private static function renderErrorMessage(\LibXMLError $errorInfo, $format)
    {
        $result = $format;
        foreach ($errorInfo as $field => $value) {
            $placeholder = '%' . $field . '%';
            $value = trim((string)$value);
            $result = str_replace($placeholder, $value, $result);
        }
        if (strpos($result, '%') !== false) {
            throw new \InvalidArgumentException("Error format '{$format}' contains unsupported placeholders.");
        }
        return $result;
    }

    /**
     * DOM document getter
     *
     * @return \DOMDocument
     */
    public function getDom()
    {
        return $this->dom;
    }

    /**
     * Create DOM document based on $xml parameter
     *
     * @param string $xml
     * @param string $filename
     * @return \DOMDocument
     * @throws \Magento\FunctionalTestingFramework\Config\Dom\ValidationException
     */
    protected function initDom($xml, $filename = null)
    {
        $dom = new \DOMDocument();
        try {
            $domSuccess = $dom->loadXML($xml);
            if (!$domSuccess) {
                throw new \Exception();
            }
        } catch (\Exception $exception) {
            throw new ValidationException("XML Parse Error: $filename\n");
        }
        if ($this->schemaFile) {
            $errors = self::validateDomDocument($dom, $this->schemaFile, $this->errorFormat);
            if (count($errors)) {
                throw new \Magento\FunctionalTestingFramework\Config\Dom\ValidationException(implode("\n", $errors));
            }
        }
        return $dom;
    }

    /**
     * Validate self contents towards to specified schema
     *
     * @param string $schemaFileName Absolute path to schema file.
     * @param array  $errors
     * @return boolean
     */
    public function validate($schemaFileName, &$errors = [])
    {
        $errors = self::validateDomDocument($this->dom, $schemaFileName, $this->errorFormat);
        return !count($errors);
    }

    /**
     * Set schema file
     *
     * @param string $schemaFile
     * @return $this
     */
    public function setSchemaFile($schemaFile)
    {
        $this->schemaFile = $schemaFile;
        return $this;
    }

    /**
     * Returns the attribute name with prefix, if there is one
     *
     * @param \DOMAttr $attribute
     * @return string
     */
    private function getAttributeName($attribute)
    {
        if ($attribute->prefix !== null && !empty($attribute->prefix)) {
            $attributeName = $attribute->prefix . ':' . $attribute->name;
        } else {
            $attributeName = $attribute->name;
        }
        return $attributeName;
    }
}
