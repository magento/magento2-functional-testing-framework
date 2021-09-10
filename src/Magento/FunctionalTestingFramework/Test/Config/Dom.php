<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Config;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Config\Dom\NodeMergingConfig;
use Magento\FunctionalTestingFramework\Config\Dom\NodePathMatcher;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Util\Validation\DuplicateNodeValidationUtil;
use Magento\FunctionalTestingFramework\Util\Validation\SingleNodePerFileValidationUtil;

/**
 * MFTF test.xml configuration XML DOM utility
 * @package Magento\FunctionalTestingFramework\Test\Config
 */
class Dom extends \Magento\FunctionalTestingFramework\Config\MftfDom
{
    const TEST_FILE_NAME_ENDING = 'Test.xml';
    const TEST_META_FILENAME_ATTRIBUTE = 'filename';
    const TEST_META_NAME_ATTRIBUTE = 'name';
    const TEST_HOOK_NAMES = ["after", "before"];
    const TEST_MERGE_POINTER_BEFORE = "insertBefore";
    const TEST_MERGE_POINTER_AFTER = "insertAfter";

    /**
     * NodeValidationUtil for test actions
     * @var DuplicateNodeValidationUtil
     */
    protected $actionsValidationUtil;

    /**
     * NodeValidationUtil for test names
     * @var DuplicateNodeValidationUtil
     */
    protected $testsValidationUtil;

    /**
     * SingleNodePerFileValidationUtil
     * @var SingleNodePerFileValidationUtil
     */
    protected $singleNodePerFileValidationUtil;

    /**
     * ExceptionCollector
     * @var ExceptionCollector
     */
    private $exceptionCollector;

    /**
     * Metadata Dom constructor.
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
        $this->actionsValidationUtil = new DuplicateNodeValidationUtil('stepKey', $exceptionCollector);
        $this->testsValidationUtil = new DuplicateNodeValidationUtil('name', $exceptionCollector);
        $this->singleNodePerFileValidationUtil = new SingleNodePerFileValidationUtil($exceptionCollector);
        $this->exceptionCollector = $exceptionCollector;
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

        // Cannot rely on filename to ensure this file is a Test.xml
        if ($dom->getElementsByTagName('tests')->length > 0) {
            $testsNode = $dom->getElementsByTagName('tests')[0];
            $this->testsValidationUtil->validateChildUniqueness(
                $testsNode,
                $filename,
                null
            );
            // Validate single test node per file
            $this->singleNodePerFileValidationUtil->validateSingleNodeForTag(
                $dom,
                'test',
                $filename
            );

            if ($dom->getElementsByTagName('test')->length > 0) {
                /** @var \DOMElement $testNode */
                $testNode = $dom->getElementsByTagName('test')[0];
                $testNode->setAttribute(self::TEST_META_FILENAME_ATTRIBUTE, $filename);
                if ($testNode->getAttribute(self::TEST_MERGE_POINTER_AFTER) !== "") {
                    $this->appendMergePointerToActions(
                        $testNode,
                        self::TEST_MERGE_POINTER_AFTER,
                        $testNode->getAttribute(self::TEST_MERGE_POINTER_AFTER),
                        $filename
                    );
                } elseif ($testNode->getAttribute(self::TEST_MERGE_POINTER_BEFORE) !== "") {
                    $this->appendMergePointerToActions(
                        $testNode,
                        self::TEST_MERGE_POINTER_BEFORE,
                        $testNode->getAttribute(self::TEST_MERGE_POINTER_BEFORE),
                        $filename
                    );
                }

                $this->actionsValidationUtil->validateChildUniqueness(
                    $testNode,
                    $filename,
                    $testNode->getAttribute(self::TEST_META_NAME_ATTRIBUTE)
                );
                $beforeNode = $testNode->getElementsByTagName('before')->item(0);
                $afterNode = $testNode->getElementsByTagName('after')->item(0);

                if (isset($beforeNode)) {
                    $this->actionsValidationUtil->validateChildUniqueness(
                        $beforeNode,
                        $filename,
                        $testNode->getAttribute(self::TEST_META_NAME_ATTRIBUTE) . "/before"
                    );
                }
                if (isset($afterNode)) {
                    $this->actionsValidationUtil->validateChildUniqueness(
                        $afterNode,
                        $filename,
                        $testNode->getAttribute(self::TEST_META_NAME_ATTRIBUTE) . "/after"
                    );
                }
            }
        }

        return $dom;
    }

    /**
     * Parses DOM Structure's actions and appends a before/after attribute along with the parent's stepkey reference.
     *
     * @param \DOMElement $testNode
     * @param string      $insertType
     * @param string      $insertKey
     * @param string      $filename
     * @return void
     */
    protected function appendMergePointerToActions($testNode, $insertType, $insertKey, $filename)
    {
        $childNodes = $testNode->childNodes;
        $previousStepKey = $insertKey;
        $actionInsertType = ActionObject::MERGE_ACTION_ORDER_AFTER;
        if ($insertType === self::TEST_MERGE_POINTER_BEFORE) {
            $actionInsertType = ActionObject::MERGE_ACTION_ORDER_BEFORE;
        }
        for ($i = 0; $i < $childNodes->length; $i++) {
            $currentNode = $childNodes->item($i);
            if (!is_a($currentNode, \DOMElement::class) || !$currentNode->hasAttribute('stepKey')) {
                continue;
            }
            if ($currentNode->hasAttribute($insertType) && $testNode->hasAttribute($insertType)) {
                $errorMsg = "Actions cannot have merge pointers if contained in tests that has a merge pointer.";
                $errorMsg .= "\n\tstepKey: {$currentNode->getAttribute('stepKey')}\tin file: {$filename}";
                $this->exceptionCollector->addError($filename, $errorMsg);
            }
            $currentNode->setAttribute($actionInsertType, $previousStepKey);
            $previousStepKey = $currentNode->getAttribute('stepKey');
            // All actions after the first need to insert AFTER.
            $actionInsertType = ActionObject::MERGE_ACTION_ORDER_AFTER;
        }
    }
}
