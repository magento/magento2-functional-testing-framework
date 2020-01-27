<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Data\Argument\Interpreter\Argument;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\ArgumentObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

/**
 * Class ActionGroupObjectExtractor
 */
class ActionGroupObjectExtractor extends BaseObjectExtractor
{
    const DEFAULT_VALUE = 'defaultValue';
    const ACTION_GROUP_ARGUMENTS = 'arguments';
    const ACTION_GROUP_ANNOTATIONS = 'annotations';
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
     * Annotation Extractor object
     *
     * @var AnnotationExtractor
     */
    private $annotationExtractor;

    /**
     * ActionGroupObjectExtractor constructor.
     */
    public function __construct()
    {
        $this->actionObjectExtractor = new ActionObjectExtractor();
        $this->annotationExtractor = new ActionGroupAnnotationExtractor();
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
        $deprecated = null;

        if (array_key_exists(self::OBJ_DEPRECATED, $actionGroupData)) {
            $deprecated = $actionGroupData[self::OBJ_DEPRECATED];
            LoggingUtil::getInstance()->getLogger(ActionGroupObject::class)->deprecation(
                $deprecated,
                ["actionGroupName" => $actionGroupData[self::FILENAME], "deprecatedActionGroup" => $deprecated]
            );
        }
        $actionGroupReference = $actionGroupData[self::EXTENDS_ACTION_GROUP] ?? null;
        $actionData = $this->stripDescriptorTags(
            $actionGroupData,
            self::NODE_NAME,
            self::ACTION_GROUP_ARGUMENTS,
            self::NAME,
            self::ACTION_GROUP_ANNOTATIONS,
            self::FILENAME,
            self::ACTION_GROUP_INSERT_BEFORE,
            self::ACTION_GROUP_INSERT_AFTER,
            self::EXTENDS_ACTION_GROUP,
            'deprecated'
        );

        // TODO filename is now available to the ActionGroupObject, integrate this into debug and error statements

        try {
            $annotations = $this->annotationExtractor->extractAnnotations(
                $actionGroupData[self::ACTION_GROUP_ANNOTATIONS] ?? [],
                $actionGroupData[self::FILENAME]
            );
        } catch (\Exception $error) {
            throw new XmlException($error->getMessage() . " in Action Group " . $actionGroupData[self::FILENAME]);
        }

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
            $annotations,
            $arguments,
            $actions,
            $actionGroupReference,
            $actionGroupData[self::FILENAME],
            $deprecated
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

        // Filtering XML comments from action group arguments.
        $argData = array_filter($argData, function ($key) {
            return strpos($key, ActionObject::COMMENT_ACTION) === false;
        }, ARRAY_FILTER_USE_KEY);

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
