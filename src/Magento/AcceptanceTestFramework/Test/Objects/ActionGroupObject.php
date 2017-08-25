<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\Test\Objects;

use Magento\AcceptanceTestFramework\Test\Util\ActionMergeUtil;

/**
 * Class ActionGroupObject
 */
class ActionGroupObject
{
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
     * A string used as the default entity if the user does not specify one
     *
     * @var string
     */
    private $defaultEntity;

    /**
     * ActionGroupObject constructor.
     *
     * @param string $name
     * @param string $defaultEntity
     * @param array $actions
     */
    public function __construct($name, $defaultEntity, $actions)
    {
        $this->name = $name;
        $this->defaultEntity = $defaultEntity;
        $this->parsedActions = $actions;
    }

    /**
     * Gets the ordered steps including merged waits
     *
     * @param string $entity
     * @return array
     */
    public function getSteps($entity)
    {
        $mergeUtil = new ActionMergeUtil();
        if (!$entity) {
            $entity = $this->defaultEntity;
        }
        return $mergeUtil->mergeStepsAndInsertWaits($this->getResolvedActions($entity));
    }

    /**
     * Function which takes the name of the entity object to be appended to an action objects fields returns resulting
     * action objects with proper entity.field references.
     *
     * @param string $entity
     * @return array
     */
    private function getResolvedActions($entity)
    {
        $resolvedActions = [];

        foreach ($this->parsedActions as $action) {
            /**@var \Magento\AcceptanceTestFramework\Test\Objects\ActionObject $action **/
            if (array_key_exists('userInput', $action->getCustomActionAttributes())) {
                $regexPattern = '/{{.[\w]+}}/';
                preg_match_all($regexPattern, $action->getCustomActionAttributes()['userInput'], $matches);

                $userInputString = $action->getCustomActionAttributes()['userInput'];
                foreach ($matches[0] as $match) {
                    $search = str_replace('}}', '', str_replace('{{', '', $match));
                    $userInputString = str_replace($search, $entity . $search, $userInputString);
                }

                $attribute['userInput'] = $userInputString;

                $resolvedActions[$action->getMergeKey()] = new ActionObject(
                    $action->getMergeKey(),
                    $action->getType(),
                    array_merge($action->getCustomActionAttributes(), $attribute),
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
}
