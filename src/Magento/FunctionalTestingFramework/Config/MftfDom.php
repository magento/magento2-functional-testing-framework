<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Config;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Config\Dom\NodeMergingConfig;
use Magento\FunctionalTestingFramework\Config\Dom\NodePathMatcher;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Util\Validation\DuplicateNodeValidationUtil;

/**
 * Class MftfDom
 * @package Magento\FunctionalTestingFramework\Config
 */
class MftfDom extends \Magento\FunctionalTestingFramework\Config\Dom
{
    /**
     * MftfDom constructor.
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
        $this->schemaFile = $schemaFile;
        $this->nodeMergingConfig = new NodeMergingConfig(new NodePathMatcher(), $idAttributes);
        $this->typeAttributeName = $typeAttributeName;
        $this->errorFormat = $errorFormat;
        $this->dom = $this->initDom($xml, $filename, $exceptionCollector);
        $this->rootNamespace = $this->dom->lookupNamespaceUri($this->dom->namespaceURI);
    }

    /**
     * Redirects any merges into the init method for appending xml filename
     *
     * @param string             $xml
     * @param string|null        $filename
     * @param ExceptionCollector $exceptionCollector
     * @return void
     */
    public function merge($xml, $filename = null, $exceptionCollector = null)
    {
        $dom = $this->initDom($xml, $filename, $exceptionCollector);
        $this->mergeNode($dom->documentElement, '');
    }

    /**
     * Checks if the filename given ends with the correct suffix.
     * @param string $filename
     * @param string $suffix
     * @return boolean
     */
    public function checkFilenameSuffix($filename, $suffix)
    {
        if (substr_compare($filename, $suffix, -strlen($suffix)) === 0) {
            return true;
        }
        return false;
    }
}
