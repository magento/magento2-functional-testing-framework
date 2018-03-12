<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\DataGenerator\Config;

use Magento\FunctionalTestingFramework\Config\Dom\NodeMergingConfig;
use Magento\FunctionalTestingFramework\Config\Dom\NodePathMatcher;

/**
 * Magento configuration XML DOM utility
 */
class Dom extends \Magento\FunctionalTestingFramework\Config\Dom
{

    /**
     * Array of non keyed mergeable paths
     *
     * @var array
     */
    private $mergeablePaths;

    /**
     * Build DOM with initial XML contents and specifying identifier attributes for merging. Overridden to include new
     * mergeablePaths argument which can be matched for non keyed mergeable xml elements.
     *
     * Format of $idAttributes: array('/xpath/to/some/node' => 'id_attribute_name')
     * The path to ID attribute name should not include any attribute notations or modifiers -- only node names
     *
     * @param string $xml
     * @param array $idAttributes
     * @param array $mergeablePaths
     * @param string $typeAttributeName
     * @param string $schemaFile
     * @param string $errorFormat
     */
    public function __construct(
        $xml,
        array $idAttributes = [],
        array $mergeablePaths = [],
        $typeAttributeName = null,
        $schemaFile = null,
        $errorFormat = self::ERROR_FORMAT_DEFAULT
    ) {
        $this->schemaFile = $schemaFile;
        $this->nodeMergingConfig = new NodeMergingConfig(new NodePathMatcher(), $idAttributes);
        $this->mergeablePaths = $mergeablePaths;
        $this->typeAttributeName = $typeAttributeName;
        $this->errorFormat = $errorFormat;
        $this->dom = $this->initDom($xml);
        $this->rootNamespace = $this->dom->lookupNamespaceUri($this->dom->namespaceURI);
    }

    /**
     * Recursive merging of the \DOMElement into the original document. Overridden to include a call to
     *
     * Algorithm:
     * 1. Find the same node in original document
     * 2. Extend and override original document node attributes and scalar value if found
     * 3. Append new node if original document doesn't have the same node
     *
     * @param \DOMElement $node
     * @param string $parentPath path to parent node
     * @return void
     */
    public function mergeNode(\DOMElement $node, $parentPath)
    {
        $path = $this->getNodePathByParent($node, $parentPath);
        $isMergeablePath = $this->validateIsPathMergeable($path);

        $matchedNode = $this->getMatchedNode($path, $isMergeablePath);

        /* Update matched node attributes and value */
        if ($matchedNode && !$isMergeablePath) {
            //different node type
            $this->mergeMatchingNode($node, $parentPath, $matchedNode, $path);
        } else {
            /* Add node as is to the document under the same parent element */
            $parentMatchedNode = $this->getMatchedNode($parentPath);
            $newNode = $this->dom->importNode($node, true);
            $parentMatchedNode->appendChild($newNode);
        }
    }

    /**
     * Getter for node by path, overridden to include validation flag for mergeable entries
     * An exception is possible if original document contains multiple nodes for identifier
     *
     * @param string $nodePath
     * @oaram boolean $isMergeablePath
     * @throws \Exception
     * @return \DOMElement|null
     */
    public function getMatchedNode($nodePath, $isMergeablePath = false)
    {
        $xPath = new \DOMXPath($this->dom);
        if ($this->rootNamespace) {
            $xPath->registerNamespace(self::ROOT_NAMESPACE_PREFIX, $this->rootNamespace);
        }
        $matchedNodes = $xPath->query($nodePath);
        $node = null;

        if ($matchedNodes->length > 1 && !$isMergeablePath) {
            throw new \Exception("More than one node matching the query: {$nodePath}");
        } elseif ($matchedNodes->length == 1) {
            $node = $matchedNodes->item(0);
        }
        return $node;
    }

    /**
     * Function which simplifies and xpath match in dom and compares with listed known mergeable paths
     *
     * @param string $path
     * @return boolean
     */
    private function validateIsPathMergeable($path)
    {
        $simplifiedPath = $this->nodeMergingConfig->getNodePathMatcher()->simplifyXpath($path);
        return array_key_exists($simplifiedPath, $this->mergeablePaths);
    }
}
