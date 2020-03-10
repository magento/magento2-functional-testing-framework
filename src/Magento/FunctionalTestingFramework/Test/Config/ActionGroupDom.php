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
    const ACTION_GROUP_META_NAME_ATTRIBUTE = "name";

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
        $dom = parent::initDom($xml, $filename);

        if ($this->checkFilenameSuffix($filename, self::ACTION_GROUP_FILE_NAME_ENDING)) {
            $actionGroupsNode = $dom->getElementsByTagName('actionGroups')[0];

            $this->testsValidationUtil->validateChildUniqueness(
                $actionGroupsNode,
                $filename,
                null
            );

            // Validate single action group node per file
            $this->singleNodePerFileValidationUtil->validateSingleNodeForTag(
                $dom,
                'actionGroup',
                $filename
            );

            if ($dom->getElementsByTagName('actionGroup')->length > 0) {
                /** @var \DOMElement $actionGroupNode */
                $actionGroupNode = $dom->getElementsByTagName('actionGroup')[0];
                $actionGroupNode->setAttribute(self::TEST_META_FILENAME_ATTRIBUTE, $filename);
                $this->actionsValidationUtil->validateChildUniqueness(
                    $actionGroupNode,
                    $filename,
                    $actionGroupNode->getAttribute(self::ACTION_GROUP_META_NAME_ATTRIBUTE)
                );
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
