<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Test\Config;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;

class ActionGroupDom extends Dom
{
    const ACTION_GROUP_FILE_NAME_ENDING = "ActionGroup.xml";

    /**
     * Takes a dom element from xml and appends the filename based on location while also validating the action group
     * step key.
     *
     * @param string $xml
     * @param string|null $filename
     * @param ExceptionCollector $exceptionCollector
     * @return \DOMDocument
     */
    public function initDom($xml, $filename = null, $exceptionCollector = null)
    {
        $dom = parent::initDom($xml);

        if (strpos($filename, self::ACTION_GROUP_FILE_NAME_ENDING)) {
            $actionGroupNodes = $dom->getElementsByTagName('actionGroup');
            foreach ($actionGroupNodes as $actionGroupNode) {
                /** @var \DOMElement $actionGroupNode */
                $actionGroupNode->setAttribute(self::TEST_META_FILENAME_ATTRIBUTE, $filename);
                $this->validateDomStepKeys($actionGroupNode, $filename, 'Action Group', $exceptionCollector);
                if ($actionGroupNode->getAttribute(self::TEST_MERGE_POINTER_AFTER) !== "") {
                    $this->appendMergePointerToActions(
                        $actionGroupNode,
                        self::TEST_MERGE_POINTER_AFTER,
                        $actionGroupNode->getAttribute(self::TEST_MERGE_POINTER_AFTER),
                        $filename,
                        $exceptionCollector
                    );
                } elseif ($actionGroupNode->getAttribute(self::TEST_MERGE_POINTER_BEFORE) !== "") {
                    $this->appendMergePointerToActions(
                        $actionGroupNode,
                        self::TEST_MERGE_POINTER_BEFORE,
                        $actionGroupNode->getAttribute(self::TEST_MERGE_POINTER_BEFORE),
                        $filename,
                        $exceptionCollector
                    );
                }
            }
        }
        return $dom;
    }
}
