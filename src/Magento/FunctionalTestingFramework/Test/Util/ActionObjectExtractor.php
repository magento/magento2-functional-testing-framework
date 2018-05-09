<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;

/**
 * Class ActionObjectExtractor
 */
class ActionObjectExtractor extends BaseObjectExtractor
{
    const TEST_ACTION_BEFORE = 'before';
    const TEST_ACTION_AFTER = 'after';
    const TEST_STEP_MERGE_KEY = 'stepKey';
    const ACTION_GROUP_TAG = 'actionGroup';
    const ACTION_GROUP_REF = 'ref';
    const ACTION_GROUP_ARGUMENTS = 'arguments';
    const ACTION_GROUP_ARG_VALUE = 'value';
    const BEFORE_AFTER_ERROR_MSG = "Merge Error - Steps cannot have both before and after attributes.\tStepKey='%s'";
    const STEP_KEY_BLACKLIST_ERROR_MSG = "StepKeys cannot contain non alphanumeric characters.\tStepKey='%s'";
    const DATA_PERSISTENCE_CUSTOM_FIELD = 'field';
    const DATA_PERSISTENCE_CUSTOM_FIELD_KEY = 'key';
    const ACTION_OBJECT_PERSISTENCE_FIELDS = 'customFields';
    const ACTION_OBJECT_USER_INPUT = 'userInput';
    const DATA_PERSISTENCE_ACTIONS = ['createData', 'deleteData', 'updateData'];

    /**
     * ActionObjectExtractor constructor.
     */
    public function __construct()
    {
        //public constructor
    }

    /**
     * This method takes an array of test actions read in from a TestHook or Test. The actions are stripped of
     * irrelevant tags and returned as an array of ActionObjects.
     *
     * @param array $testActions
     * @param string $testName
     * @return array
     * @throws XmlException
     * @throws TestReferenceException
     */
    public function extractActions($testActions, $testName = null)
    {
        $actions = [];
        $stepKeyRefs = [];

        foreach ($testActions as $actionName => $actionData) {
            $stepKey = $actionData[self::TEST_STEP_MERGE_KEY];

            if (preg_match('/[^a-zA-Z0-9_]/', $stepKey)) {
                throw new XmlException(sprintf(self::STEP_KEY_BLACKLIST_ERROR_MSG, $actionName));
            }

            $actionAttributes = $this->stripDescriptorTags(
                $actionData,
                self::TEST_STEP_MERGE_KEY,
                self::NODE_NAME
            );

            // Flatten AssertSorted "array" element to parameterArray
            if (isset($actionData["array"])) {
                $actionAttributes['parameterArray'] = $actionData['array']['value'];
            }

            if ($actionData[self::NODE_NAME] === self::ACTION_GROUP_TAG) {
                $actionAttributes = $this->processActionGroupArgs($actionAttributes);
            }

            $linkedAction = $this->processLinkedActions($actionName, $actionData);
            $actions = $this->extractFieldActions($actionData, $actions);
            $actionAttributes = $this->extractFieldReferences($actionData, $actionAttributes);

            if ($linkedAction['stepKey'] != null) {
                $stepKeyRefs[$linkedAction['stepKey']][] = $stepKey;
            }

            // TODO this is to be implemented later. Currently the schema does not use or need return var.
            /*if (array_key_exists(ActionGroupObjectHandler::TEST_ACTION_RETURN_VARIABLE, $actionData)) {
                $returnVariable = $actionData[ActionGroupObjectHandler::TEST_ACTION_RETURN_VARIABLE];
            }*/

            $actions[$stepKey] = new ActionObject(
                $stepKey,
                $actionData[self::NODE_NAME],
                $actionAttributes,
                $linkedAction['stepKey'],
                $linkedAction['order']
            );
        }

        $this->auditMergeSteps($stepKeyRefs, $testName);

        return $actions;
    }

    /**
     * Function which processes any actions which have an explicit reference to an additional step for merging purposes.
     * Returns an array with keys corresponding to the linked action's stepKey and order.
     *
     * @param string $actionName
     * @param array $actionData
     * @return array
     * @throws XmlException
     */
    private function processLinkedActions($actionName, $actionData)
    {
        $linkedAction =['stepKey' => null, 'order' => null];
        if (array_key_exists(self::TEST_ACTION_BEFORE, $actionData)
            and array_key_exists(self::TEST_ACTION_AFTER, $actionData)) {
            throw new XmlException(sprintf(self::BEFORE_AFTER_ERROR_MSG, $actionName));
        }

        if (array_key_exists(self::TEST_ACTION_BEFORE, $actionData)) {
            $linkedAction['stepKey'] = $actionData[self::TEST_ACTION_BEFORE];
            $linkedAction['order'] = self::TEST_ACTION_BEFORE;
        } elseif (array_key_exists(self::TEST_ACTION_AFTER, $actionData)) {
            $linkedAction['stepKey'] = $actionData[self::TEST_ACTION_AFTER];
            $linkedAction['order'] = self::TEST_ACTION_AFTER;
        }

        return $linkedAction;
    }

    /**
     * Takes the action group reference and parses out arguments as an array that can be passed to override defaults
     * defined in the action group xml.
     *
     * @param array $actionAttributeData
     * @return array
     */
    private function processActionGroupArgs($actionAttributeData)
    {
        $actionAttributeArgData = [];
        foreach ($actionAttributeData as $attributeDataKey => $attributeDataValues) {
            if ($attributeDataKey == self::ACTION_GROUP_REF) {
                $actionAttributeArgData[self::ACTION_GROUP_REF] = $attributeDataValues;
                continue;
            }

            $actionAttributeArgData[self::ACTION_GROUP_ARGUMENTS][$attributeDataKey] =
                $attributeDataValues[self::ACTION_GROUP_ARG_VALUE] ?? null;
        }

        return $actionAttributeArgData;
    }

    /**
     * Takes the array representing an action and validates it is a persistence type. If of type persistence,
     * the function checks for any user specified fields to extract as separate actions to be resolved independently
     * from the the persistence method.
     *
     * @param array $actionData
     * @param array $actions
     * @return array
     * @throws XmlException
     * @throws TestReferenceException
     */
    private function extractFieldActions($actionData, $actions)
    {
        if (!in_array($actionData[self::NODE_NAME], self::DATA_PERSISTENCE_ACTIONS)) {
            return $actions;
        }

        $fieldActions = [];
        foreach ($actionData as $type => $data) {
            // determine if field type is entity passed in
            if (!is_array($data) || $data[self::NODE_NAME] != self::DATA_PERSISTENCE_CUSTOM_FIELD) {
                continue;
            }

            // must append stepKey and userInput to resolve fields passed in properly via extractActions method
            $fieldData = $data;
            $fieldData[self::TEST_STEP_MERGE_KEY] = $actionData[self::TEST_STEP_MERGE_KEY]
                . ucfirst($fieldData[self::DATA_PERSISTENCE_CUSTOM_FIELD_KEY]);
            $fieldData[self::ACTION_OBJECT_USER_INPUT] = $fieldData['value'];
            $fieldActions[] = $fieldData;
        }

        // merge resolved actions with those psased in and return
        return array_merge($actions, $this->extractActions($fieldActions));
    }

    /**
     * Takes the array representing an action and validates it is a persistence type. If of type persistence, the
     * function creates a new set of attributes for the persistence method which represent a named list of fields and
     * any other references (such as required entities etc.)
     *
     * @param array $actionData
     * @param array $actionAttributes
     * @return array
     */
    private function extractFieldReferences($actionData, $actionAttributes)
    {
        if (!in_array($actionData[self::NODE_NAME], self::DATA_PERSISTENCE_ACTIONS)) {
            return $actionAttributes;
        }

        $attributes = [];
        foreach ($actionAttributes as $attributeName => $attributeValue) {
            if (!is_array($attributeValue) || $attributeValue[self::NODE_NAME] != self::DATA_PERSISTENCE_CUSTOM_FIELD) {
                $attributes[$attributeName] = $attributeValue;
                continue;
            }

            $attributes[self::ACTION_OBJECT_PERSISTENCE_FIELDS][] = $attributeName;
        }

        if (array_key_exists(self::ACTION_OBJECT_PERSISTENCE_FIELDS, $attributes)) {
            $attributes[self::ACTION_OBJECT_PERSISTENCE_FIELDS][self::NODE_NAME] = 'fields';
        }

        return $attributes;
    }

    /**
     * Function which validates stepKey references within mergeable actions
     *
     * @param array $stepKeyRefs
     * @param string $testName
     * @return void
     * @throws TestReferenceException
     */
    private function auditMergeSteps($stepKeyRefs, $testName)
    {
        if (empty($stepKeyRefs)) {
            return;
        }

        // check for step keys which are referencing themselves as before/after
        $invalidStepRef = array_filter($stepKeyRefs, function ($value, $key) {
            return in_array($key, $value);
        }, ARRAY_FILTER_USE_BOTH);

        if (!empty($invalidStepRef)) {
            $errorMsg = "Invalid ordering configuration in test {$testName} with step key(s):\n";
            array_walk($invalidStepRef, function ($value, $key) use (&$errorMsg) {
                $errorMsg.="\t{$key}\n";
            });

            throw new TestReferenceException($errorMsg);
        }

        // check for ambiguous references to step keys (multiple refs across test merges).
        $atRiskStepRef = array_filter($stepKeyRefs, function ($value) {
            return count($value) > 1;
        });

        $multipleActionsError = "";
        foreach ($atRiskStepRef as $stepKey => $stepRefs) {
            $multipleActionsError.= "multiple actions referencing step key {$stepKey} in test {$testName}:\n";
            array_walk($stepRefs, function ($value) use (&$multipleActionsError) {
                $multipleActionsError.= "\t{$value}\n";
            });
        }

        if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
            print $multipleActionsError;
        }
    }
}
