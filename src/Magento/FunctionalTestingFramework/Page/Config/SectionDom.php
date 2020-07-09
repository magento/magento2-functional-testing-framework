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
use Magento\FunctionalTestingFramework\Util\Validation\SingleNodePerFileValidationUtil;

/**
 * MFTF section.xml configuration XML DOM utility
 * @package Magento\FunctionalTestingFramework\Page\Config
 */
class SectionDom extends \Magento\FunctionalTestingFramework\Config\MftfDom
{
    const SECTION_META_FILENAME_ATTRIBUTE = "filename";
    const SECTION_META_NAME_ATTRIBUTE = "name";

    /**
     * NodeValidationUtil
     * @var DuplicateNodeValidationUtil
     */
    private $validationUtil;

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
        $this->validationUtil = new DuplicateNodeValidationUtil('name', $exceptionCollector);
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

        if ($dom->getElementsByTagName('sections')->length > 0) {
            // Validate single section node per file
            $this->singleNodePerFileValidationUtil->validateSingleNodeForTag(
                $dom,
                'section',
                $filename
            );
            if ($dom->getElementsByTagName('section')->length > 0) {
                /** @var \DOMElement $sectionNode */
                $sectionNode = $dom->getElementsByTagName('section')[0];
                $sectionNode->setAttribute(self::SECTION_META_FILENAME_ATTRIBUTE, $filename);
                $this->validationUtil->validateChildUniqueness(
                    $sectionNode,
                    $filename,
                    $sectionNode->getAttribute(self::SECTION_META_NAME_ATTRIBUTE)
                );
            }
        }

        return $dom;
    }
}
