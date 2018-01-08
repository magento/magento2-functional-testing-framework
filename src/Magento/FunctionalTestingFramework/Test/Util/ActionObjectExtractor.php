<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

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
    const BEFORE_AFTER_ERROR_MSG = "Merge Error - Steps cannot have both before and after attributes.\tTestStep='%s'";

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
     * @return array
     * @throws XmlException
     */
    public function extractActions($testActions)
    {
        $actions = [];

        foreach ($testActions as $actionName => $actionData) {
            $stepKey = $actionData[self::TEST_STEP_MERGE_KEY];
            if ($actionData[self::NODE_NAME] === TestEntityExtractor::TEST_STEP_ENTITY_CREATION) {
                $actionData = $this->stripDataFields($actionData);
            }

            $actionAttributes = $this->stripDescriptorTags(
                $actionData,
                self::TEST_STEP_MERGE_KEY,
                self::NODE_NAME
            );
            $linkedAction = null;
            $order = null;

            if ($actionData[self::NODE_NAME] === self::ACTION_GROUP_TAG) {
                $actionAttributes = $this->processActionGroupArgs($actionAttributes);
            }

            if (array_key_exists(self::TEST_ACTION_BEFORE, $actionData)
                and array_key_exists(self::TEST_ACTION_AFTER, $actionData)) {
                throw new XmlException(sprintf(self::BEFORE_AFTER_ERROR_MSG, $actionName));
            }

            if (array_key_exists(self::TEST_ACTION_BEFORE, $actionData)) {
                $linkedAction = $actionData[self::TEST_ACTION_BEFORE];
                $order = self::TEST_ACTION_BEFORE;
            } elseif (array_key_exists(self::TEST_ACTION_AFTER, $actionData)) {
                $linkedAction = $actionData[self::TEST_ACTION_AFTER];
                $order = self::TEST_ACTION_AFTER;
            }
            // TODO this is to be implemented later. Currently the schema does not use or need return var.
            /*if (array_key_exists(ActionGroupObjectHandler::TEST_ACTION_RETURN_VARIABLE, $actionData)) {
                $returnVariable = $actionData[ActionGroupObjectHandler::TEST_ACTION_RETURN_VARIABLE];
            }*/

            $actions[] = new ActionObject(
                $stepKey,
                $actionData[self::NODE_NAME],
                $actionAttributes,
                $linkedAction,
                $order
            );
        }

        return $actions;
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
     * Function which checks an entity definition for type array and strips this key out (as data is not stores in this
     * type of object).
     *
     * @param array $entityDataArray
     * @return array
     */
    private function stripDataFields($entityDataArray)
    {
        $results = $entityDataArray;
        foreach ($entityDataArray as $key => $attribute) {
            if (is_array($attribute)) {
                unset($results[$key]);
            }
        }

        return $results;
    }
}
