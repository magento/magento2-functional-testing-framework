<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\DataGenerator\Config;

use Magento\FunctionalTestingFramework\Config\Dom\NodeMergingConfig;
use Magento\FunctionalTestingFramework\Config\Dom\NodePathMatcher;
use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Util\Validation\DuplicateNodeValidationUtil;

/**
 * MFTF actionGroup.xml configuration XML DOM utility
 * @package Magento\FunctionalTestingFramework\DataGenerator\Config
 */
class Dom extends \Magento\FunctionalTestingFramework\Config\MftfDom
{
    const DATA_FILE_NAME_ENDING = "Data";
    const DATA_META_FILENAME_ATTRIBUTE = "filename";

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

        if (strpos($filename, self::DATA_FILE_NAME_ENDING)) {
            $entityNodes = $dom->getElementsByTagName('entity');
            foreach ($entityNodes as $entityNode) {
                /** @var \DOMElement $entityNode */
                $entityNode->setAttribute(self::DATA_META_FILENAME_ATTRIBUTE, $filename);
                DuplicateNodeValidationUtil::validateChildUniqueness(
                    $entityNode,
                    $filename,
                    'key',
                    $exceptionCollector
                );
            }
        }

        return $dom;
    }
}
