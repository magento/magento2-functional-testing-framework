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
    const VAR_ATTRIBUTES = ['userInput', 'selector', 'page'];

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
     * @return array
     */
    public function getSteps($arguments)
    {
        $mergeUtil = new ActionMergeUtil();
        $args = $this->arguments;

        if ($arguments) {
            $args = array_merge($args, $arguments);
        }

        return $mergeUtil->resolveActionSteps($this->getResolvedActionsWithArgs($args), true);
    }

    /**
     * Function which takes a set of arguments to be appended to an action objects fields returns resulting
     * action objects with proper argument.field references.
     *
     * @param array $arguments
     * @return array
     */
    private function getResolvedActionsWithArgs($arguments)
    {
        $resolvedActions = [];
        $regexPattern = '/{{([\w]+)/';

        foreach ($this->parsedActions as $action) {
            $varAttributes = array_intersect(self::VAR_ATTRIBUTES, array_keys($action->getCustomActionAttributes()));
            if (!empty($varAttributes)) {
                $newActionAttributes = [];
                // 1 check to see if we have pertinent var
                foreach ($varAttributes as $varAttribute) {
                    $attributeValue = $action->getCustomActionAttributes()[$varAttribute];
                    preg_match_all($regexPattern, $attributeValue, $matches);
                    if (empty($matches[0]) & empty($matches[1])) {
                        continue;
                    }

                    $newActionAttributes[$varAttribute] = $this->resolveNewAttribute(
                        $arguments,
                        $attributeValue,
                        $matches
                    );
                }

                $resolvedActions[$action->getMergeKey()] = new ActionObject(
                    $action->getMergeKey(),
                    $action->getType(),
                    array_merge($action->getCustomActionAttributes(), $newActionAttributes),
                    $action->getLinkedAction(),
                    $action->getOrderOffset()
                );
            } else {
                // add action here if we do not see any userInput in this particular action
                $resolvedActions[$action->getMergeKey()] = $action;
            }
        }

        return $resolvedActions;
    }

    /**
     * Function which takes an array of arguments to use for replacement of var name, the string which contains
     * the variable for replacement, an array of matching vars.
     *
     * @param array $arguments
     * @param string $attributeValue
     * @param array $matches
     * @return string
     */
    private function resolveNewAttribute($arguments, $attributeValue, $matches)
    {
        $newAttributeVal = $attributeValue;
        foreach ($matches[1] as $var) {
            if (array_key_exists($var, $arguments)) {
                $newAttributeVal = str_replace($var, $arguments[$var], $newAttributeVal);
            }
        }

        return $newAttributeVal;
    }
}
