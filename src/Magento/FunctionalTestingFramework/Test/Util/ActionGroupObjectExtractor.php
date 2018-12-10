<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Data\Argument\Interpreter\Argument;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Objects\ArgumentObject;

/**
 * Class ActionGroupObjectExtractor
 */
class ActionGroupObjectExtractor extends BaseObjectExtractor
{
    const DEFAULT_VALUE = 'defaultValue';
    const ACTION_GROUP_ARGUMENTS = 'arguments';
    const FILENAME = 'filename';
    const ACTION_GROUP_INSERT_BEFORE = "insertBefore";
    const ACTION_GROUP_INSERT_AFTER = "insertAfter";
    const EXTENDS_ACTION_GROUP = 'extends';

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
     * @throws XmlException
     */
    public function extractActionGroup($actionGroupData)
    {
        $arguments = [];

        $actionGroupReference = $actionGroupData[self::EXTENDS_ACTION_GROUP] ?? null;
        $actionData = $this->stripDescriptorTags(
            $actionGroupData,
            self::NODE_NAME,
            self::ACTION_GROUP_ARGUMENTS,
            self::NAME,
            self::FILENAME,
            self::ACTION_GROUP_INSERT_BEFORE,
            self::ACTION_GROUP_INSERT_AFTER,
            self::EXTENDS_ACTION_GROUP
        );

        // TODO filename is now available to the ActionGroupObject, integrate this into debug and error statements
        try {
            $actions = $this->actionObjectExtractor->extractActions($actionData);
        } catch (\Exception $error) {
            throw new XmlException($error->getMessage() . " in Action Group " . $actionGroupData[self::FILENAME]);
        }

        if (array_key_exists(self::ACTION_GROUP_ARGUMENTS, $actionGroupData)) {
            $arguments = $this->extractArguments($actionGroupData[self::ACTION_GROUP_ARGUMENTS]);
        }

        return new ActionGroupObject(
            $actionGroupData[self::NAME],
            $arguments,
            $actions,
            $actionGroupReference
        );
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
            $parsedArguments[] = new ArgumentObject(
                $argValue[ArgumentObject::ARGUMENT_NAME],
                $argValue[ArgumentObject::ARGUMENT_DEFAULT_VALUE] ?? null,
                $argValue[ArgumentObject::ARGUMENT_DATA_TYPE] ?? ArgumentObject::ARGUMENT_DATA_ENTITY
            );
        }
        return $parsedArguments;
    }
}
