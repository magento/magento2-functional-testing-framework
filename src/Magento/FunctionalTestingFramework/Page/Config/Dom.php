<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Page\Config;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Config\Dom\NodeMergingConfig;
use Magento\FunctionalTestingFramework\Config\Dom\NodePathMatcher;
use Magento\FunctionalTestingFramework\Util\ModulePathExtractor;

class Dom extends \Magento\FunctionalTestingFramework\Config\Dom
{
    /**
     * Module Path extractor
     *
     * @var ModulePathExtractor
     */
    private $modulePathExtractor;

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
        $this->modulePathExtractor = new ModulePathExtractor();
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

        $pageNodes = $dom->getElementsByTagName('page');
        $currentModule =
            $this->modulePathExtractor->extractModuleName($filename) .
            '_' .
            $this->modulePathExtractor->getExtensionPath($filename);
        foreach ($pageNodes as $pageNode) {
            $pageModule = $pageNode->getAttribute("module");
            $pageName = $pageNode->getAttribute("name");
            if ($pageModule !== $currentModule) {
                if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
                    print(
                        "Page Module does not match path Module. " .
                        "(Page, Module): ($pageName, $pageModule) - Path Module: $currentModule" .
                        PHP_EOL
                    );
                }
            }
        }
        return $dom;
    }
}
