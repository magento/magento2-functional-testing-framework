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
 * MFTF section.xml configuration XML DOM utility
 * @package Magento\FunctionalTestingFramework\Page\Config
 */
class SectionDom extends \Magento\FunctionalTestingFramework\Config\MftfDom
{
    const SECTION_META_FILENAME_ATTRIBUTE = "filename";

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
        $sectionNodes = $dom->getElementsByTagName('section');
        foreach ($sectionNodes as $sectionNode) {
            $sectionNode->setAttribute(self::SECTION_META_FILENAME_ATTRIBUTE, $filename);
            DuplicateNodeValidationUtil::validateChildUniqueness($sectionNode, $filename, 'name', $exceptionCollector);
        }
        return $dom;
    }
}
