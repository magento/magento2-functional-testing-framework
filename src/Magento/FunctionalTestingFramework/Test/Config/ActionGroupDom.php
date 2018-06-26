<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Test\Config;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Util\Validation\DuplicateNodeValidationUtil;

/**
 * MFTF actionGroup.xml configuration XML DOM utility
 * @package Magento\FunctionalTestingFramework\Test\Config
 */
class ActionGroupDom extends Dom
{
    const ACTION_GROUP_FILE_NAME_ENDING = "ActionGroup.xml";

    /**
     * Takes a dom element from xml and appends the filename based on location while also validating the action group
     * step key.
     *
     * @param string      $xml
     * @param string|null $filename
     * @return \DOMDocument
     */
    public function initDom($xml, $filename = null)
    {
        $dom = parent::initDom($xml);

        if (strpos($filename, self::ACTION_GROUP_FILE_NAME_ENDING)) {
            $actionGroupNodes = $dom->getElementsByTagName('actionGroup');
            foreach ($actionGroupNodes as $actionGroupNode) {
                /** @var \DOMElement $actionGroupNode */
                $actionGroupNode->setAttribute(self::TEST_META_FILENAME_ATTRIBUTE, $filename);
                $this->validationUtil->validateChildUniqueness(
                    $actionGroupNode,
                    $filename
                );
                $beforeNode = $actionGroupNode->getElementsByTagName('before')->item(0);
                $afterNode = $actionGroupNode->getElementsByTagName('after')->item(0);
                if (isset($beforeNode)) {
                    $this->validationUtil->validateChildUniqueness(
                        $beforeNode,
                        $filename
                    );
                }
                if (isset($afterNode)) {
                    $this->validationUtil->validateChildUniqueness(
                        $afterNode,
                        $filename
                    );
                }
                if ($actionGroupNode->getAttribute(self::TEST_MERGE_POINTER_AFTER) !== "") {
                    $this->appendMergePointerToActions(
                        $actionGroupNode,
                        self::TEST_MERGE_POINTER_AFTER,
                        $actionGroupNode->getAttribute(self::TEST_MERGE_POINTER_AFTER),
                        $filename
                    );
                } elseif ($actionGroupNode->getAttribute(self::TEST_MERGE_POINTER_BEFORE) !== "") {
                    $this->appendMergePointerToActions(
                        $actionGroupNode,
                        self::TEST_MERGE_POINTER_BEFORE,
                        $actionGroupNode->getAttribute(self::TEST_MERGE_POINTER_BEFORE),
                        $filename
                    );
                }
            }
        }
        return $dom;
    }
}
