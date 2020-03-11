<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Suite\Config;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Util\Validation\SingleNodePerFileValidationUtil;

/**
 * MFTF suite.xml configuration XML DOM utility
 * @package Magento\FunctionalTestingFramework\Suite\Config
 */
class SuiteDom extends \Magento\FunctionalTestingFramework\Config\MftfDom
{
    const SUITE_META_FILENAME_ATTRIBUTE = "filename";

    /** SingleNodePerFileValidationUtil
     *
     * @var SingleNodePerFileValidationUtil
     */
    private $singleNodePerFileValidationUtil;

    /**
     * Entity Dom constructor.
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
        $this->singleNodePerFileValidationUtil = new SingleNodePerFileValidationUtil($exceptionCollector);
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

        if ($dom->getElementsByTagName('suites')->length > 0) {
            // Validate single suite node per file
            $this->singleNodePerFileValidationUtil->validateSingleNodeForTag(
                $dom,
                'suite',
                $filename
            );
            if ($dom->getElementsByTagName('suite')->length > 0) {
                /** @var \DOMElement $suiteNode */
                $suiteNode = $dom->getElementsByTagName('suite')[0];
                $suiteNode->setAttribute(self::SUITE_META_FILENAME_ATTRIBUTE, $filename);
            }
        }

        return $dom;
    }
}
