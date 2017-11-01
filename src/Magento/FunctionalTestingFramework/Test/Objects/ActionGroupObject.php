<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Objects;

use Magento\FunctionalTestingFramework\Test\Util\ActionMergeUtil;

/**
 * Class ActionGroupObject
 */
class ActionGroupObject
{
    const VAR_ATTRIBUTES = ['userInput', 'selector', 'page', 'url'];

    /**
     * The name of the action group
     *
     * @var string
     */
    private $name;

    /**
     * An array of action objects
     *
     * @var array
     */
    private $parsedActions = [];

    /**
     * An array used to store the default entities if the user does not specify any
     *
     * @var array
     */
    private $arguments;

    /**
     * ActionGroupObject constructor.
     *
     * @param string $name
     * @param string $arguments
     * @param array $actions
     */
    public function __construct($name, $arguments, $actions)
    {
        $this->name = $name;
        $this->arguments = $arguments;
        $this->parsedActions = $actions;
    }

    /**
     * Gets the ordered steps including merged waits
     *
     * @param array $arguments
     * @param string $actionReferenceKey
     * @return array
     */
    public function getSteps($arguments, $actionReferenceKey)
    {
        $mergeUtil = new ActionMergeUtil();
        $args = $this->arguments;

        if ($arguments) {
            $args = array_merge($args, $arguments);
        }

        return $mergeUtil->resolveActionSteps($this->getResolvedActionsWithArgs($args, $actionReferenceKey), true);
    }

    /**
     * Function which takes a set of arguments to be appended to an action objects fields returns resulting
     * action objects with proper argument.field references.
     *
     * @param array $arguments
     * @param string $actionReferenceKey
     * @return array
     */
    private function getResolvedActionsWithArgs($arguments, $actionReferenceKey)
    {
        $resolvedActions = [];

        // $regexPattern match on:   $matches[0] {{section.element(arg.field)}}
        // $matches[1] = section.element
        // $matches[2] = arg.field
        $regexPattern = '/{{([\w.\[\]]+)\(*([\w.$\',\s]+)*\)*}}/';

        foreach ($this->parsedActions as $action) {
            $varAttributes = array_intersect(self::VAR_ATTRIBUTES, array_keys($action->getCustomActionAttributes()));
            $newActionAttributes = [];

            if (!empty($varAttributes)) {
                // 1 check to see if we have pertinent var
                foreach ($varAttributes as $varAttribute) {
                    $attributeValue = $action->getCustomActionAttributes()[$varAttribute];
                    preg_match_all($regexPattern, $attributeValue, $matches);
                    if (empty($matches[0])) {
                        continue;
                    }

                    //get rid of full match {{arg.field(arg.field)}}
                    array_shift($matches);

                    $newActionAttributes[$varAttribute] = $this->replaceAttributeArguments(
                        $arguments,
                        $attributeValue,
                        $matches
                    );
                }
            }
            $resolvedActions[$action->getMergeKey() . $actionReferenceKey] = new ActionObject(
                $action->getMergeKey() . $actionReferenceKey,
                $action->getType(),
                array_merge($action->getCustomActionAttributes(), $newActionAttributes),
                $action->getLinkedAction(),
                $action->getOrderOffset()
            );
        }

        return $resolvedActions;
    }

    /**
     * Function that takes an array of replacement arguments, and matches them with args in an actionGroup's attribute.
     * Determines if the replacement arguments are persisted data, and replaces them accordingly.
     *
     * @param array $arguments
     * @param string $attributeValue
     * @param array $matches
     * @return string
     */
    private function replaceAttributeArguments($arguments, $attributeValue, $matches)
    {
        list($mainValueList, $possibleArgumentsList) = $matches;

        foreach ($mainValueList as $index => $mainValue) {
            $possibleArguments = $possibleArgumentsList[$index];

            $attributeValue = $this->replaceAttributeArgumentInVariable($mainValue, $arguments, $attributeValue);

            $argumentList = array_filter(array_map('trim', explode(',', $possibleArguments)));

            foreach ($argumentList as $argumentValue) {
                $attributeValue = $this->replaceAttributeArgumentInVariable(
                    $argumentValue,
                    $arguments,
                    $attributeValue,
                    true
                );
            }
        }

        return $attributeValue;
    }

    /**
     * Replace attribute arguments in variable.
     *
     * @param string $variable
     * @param array $arguments
     * @param string $attributeValue
     * @param bool $isInnerArgument
     * @return string
     */
    private function replaceAttributeArgumentInVariable(
        $variable,
        $arguments,
        $attributeValue,
        $isInnerArgument = false
    ) {
        // Truncate arg.field into arg
        $variableName = strstr($variable, '.', true);
        // Check if arguments has a mapping for the given variableName

        if ($variableName === false) {
            $variableName = $variable;
        }

        if (!array_key_exists($variableName, $arguments)) {
            return $attributeValue;
        }

        $isPersisted = strstr($arguments[$variableName], '$');
        if ($isPersisted) {
            return $this->replacePersistedArgument(
                $arguments[$variableName],
                $attributeValue,
                $variable,
                $variableName,
                $isInnerArgument
            );
        }

        return  str_replace($variableName, $arguments[$variableName], $attributeValue);
    }

    /**
     * Replaces args with replacements given, behavior is specific to persisted arguments.
     * @param string $replacement
     * @param string $attributeValue
     * @param string $fullVariable
     * @param string $variable
     * @param boolean $isParameter
     * @return string
     */
    private function replacePersistedArgument($replacement, $attributeValue, $fullVariable, $variable, $isParameter)
    {
        //hookPersisted will be true if replacement passed in is $$arg.field$$, otherwise assume it's $arg.field$
        $hookPersistedArgumentRegex = '/\$\$[\w.\[\]\',]+\$\$/';
        $hookPersisted = (preg_match($hookPersistedArgumentRegex, $replacement));

        $newAttributeValue = $attributeValue;

        $scope = '$';
        if ($hookPersisted) {
            $scope = '$$';
        }

        // parameter replacements require changing of (arg.field) to ($arg.field$)
        if ($isParameter) {
            $fullReplacement = str_replace($variable, trim($replacement, '$'), $fullVariable);
            $newAttributeValue = str_replace($fullVariable, $scope . $fullReplacement . $scope, $newAttributeValue);
        } else {
            $newAttributeValue = str_replace('{{', $scope, str_replace('}}', $scope, $newAttributeValue));
            $newAttributeValue = str_replace($variable, trim($replacement, '$'), $newAttributeValue);
        }

        return $newAttributeValue;
    }
}
