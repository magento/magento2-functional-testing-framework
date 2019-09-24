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
use Magento\FunctionalTestingFramework\Util\Validation\DuplicateNodeValidationUtil;

/**
 * MFTF page.xml configuration XML DOM utility
 * @package Magento\FunctionalTestingFramework\Page\Config
 */
class Dom extends \Magento\FunctionalTestingFramework\Config\MftfDom
{
    const PAGE_META_FILENAME_ATTRIBUTE = "filename";
    const PAGE_META_NAME_ATTRIBUTE = "name";

    /**
     * Module Path extractor
     *
     * @var ModulePathExtractor
     */
    private $modulePathExtractor;

    /**
     * NodeValidationUtil
     *
     * @var DuplicateNodeValidationUtil
     */
    private $validationUtil;

    /**
     * Page Dom constructor.
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
        $this->modulePathExtractor = new ModulePathExtractor();
        $this->validationUtil = new DuplicateNodeValidationUtil('name', $exceptionCollector);
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

        $pagesNode = $dom->getElementsByTagName('pages')->item(0);
        $this->validationUtil->validateChildUniqueness(
            $pagesNode,
            $filename,
            $pagesNode->getAttribute(self::PAGE_META_NAME_ATTRIBUTE)
        );
        $pageNodes = $dom->getElementsByTagName('page');
        $currentModule =
            $this->modulePathExtractor->getExtensionPath($filename)
            . '_'
            . $this->modulePathExtractor->extractModuleName($filename);
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
            $pageNode->setAttribute(self::PAGE_META_FILENAME_ATTRIBUTE, $filename);
        }
        return $dom;
    }
}
