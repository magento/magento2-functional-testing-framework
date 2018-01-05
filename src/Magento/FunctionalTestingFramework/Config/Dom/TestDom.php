<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Config\Dom;

use Magento\FunctionalTestingFramework\Config\Dom;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;

class TestDom extends Dom
{
    const TEST_FILE_NAME_ENDING = 'Test';
    const TEST_META_FILENAME_ATTRIBUTE = 'filename';
    const TEST_META_NAME_ATTRIBUTE = 'name';

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
        $this->nodeMergingConfig = new Dom\NodeMergingConfig(new Dom\NodePathMatcher(), $idAttributes);
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
        $dom = parent::initDom($xml, $filename);

        if (strpos($filename, self::TEST_FILE_NAME_ENDING)) {
            $testNodes = $dom->getElementsByTagName('test');
            foreach ($testNodes as $testNode) {
                $testNode->setAttribute(self::TEST_META_FILENAME_ATTRIBUTE, $filename);
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
}
