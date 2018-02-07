<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;

/**
 * Class ActionGroupObjectExtractor
 */
class ActionGroupObjectExtractor extends BaseObjectExtractor
{
    const DEFAULT_VALUE = 'defaultValue';
    const ACTION_GROUP_ARGUMENTS = 'arguments';
    const ACTION_GROUP_DATA = 'data';
    const ACTION_GROUP_DATA_ENTITY = 'entity';
    const ACTION_GROUP_SIMPLE_DATA_TYPES = ['string', 'int', 'float', 'boolean'];

    /**
     * Action Object Extractor for converting actions into objects
     *
     * @var ActionObjectExtractor
     */
    private $actionObjectExtractor;

    /**
     * ActionGroupObjectExtractor constructor.
     */
    public function __construct()
    {
        $this->actionObjectExtractor = new ActionObjectExtractor();
    }

    /**
     * Method to parse array of action group data into ActionGroupObject
     *
     * @param array $actionGroupData
     * @return ActionGroupObject
     */
    public function extractActionGroup($actionGroupData)
    {
        $arguments = [];
        $argumentMap = [];

        $actionData = $this->stripDescriptorTags(
            $actionGroupData,
            self::NODE_NAME,
            self::ACTION_GROUP_ARGUMENTS,
            self::NAME
        );

        $actions = $this->actionObjectExtractor->extractActions($actionData);

        if (array_key_exists(self::ACTION_GROUP_ARGUMENTS, $actionGroupData)) {
            $arguments = $this->extractArguments($actionGroupData[self::ACTION_GROUP_ARGUMENTS]);
            $argumentMap = $this->extractArgumentTypeMap($actionGroupData[self::ACTION_GROUP_ARGUMENTS]);
        }

        return new ActionGroupObject(
            $actionGroupData[self::NAME],
            $arguments,
            $argumentMap,
            $actions
        );
    }

    /**
     * Extracts argName => dataType mapping.
     * @param array $arguments
     * @return array
     */
    private function extractArgumentTypeMap($arguments)
    {
        $parsedTypeMap = [];
        $argData = $this->stripDescriptorTags(
            $arguments,
            self::NODE_NAME
        );
        foreach ($argData as $argName => $argValue) {
            $parsedTypeMap[$argName] = $argValue[self::ACTION_GROUP_DATA] ?? self::ACTION_GROUP_DATA_ENTITY;
        }
        return $parsedTypeMap;
    }

    /**
     * Method which extract argument declarations from an action group and returns an array of default values indexed
     * by argument name.
     *
     * @param array $arguments
     * @return array
     */
    private function extractArguments($arguments)
    {
        $parsedArguments = [];
        $argData = $this->stripDescriptorTags(
            $arguments,
            self::NODE_NAME
        );

        foreach ($argData as $argName => $argValue) {
            $parsedArguments[$argName] = $argValue[self::DEFAULT_VALUE] ?? null;
        }
        return $parsedArguments;
    }
}
