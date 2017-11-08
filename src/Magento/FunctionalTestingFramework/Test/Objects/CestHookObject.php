<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Objects;

use Magento\FunctionalTestingFramework\Test\Util\ActionMergeUtil;

/**
 * Class CestHookObject
 */
class CestHookObject
{
    /**
     * Type of Hook (i.e. before or after).
     *
     * @var string
     */
    private $type;

    /**
     * Name of parent object
     *
     * @var string
     */
    private $parentName;

    /**
     * Array which contains the action objects to be executed in a hook.
     *
     * @var array
     */
    private $actions = [];

    /**
     * Array of Hook-defined data.
     * @var array|null
     */
    private $customData = [];

    /**
     * CestHookObject constructor.
     * @param string $type
     * @param string $parentName
     * @param array $actions
     * @param array $customData
     */
    public function __construct($type, $parentName, $actions, $customData = null)
    {
        $this->type = $type;
        $this->cestNameparent = $parentName;
        $this->actions = $actions;
        $this->customData = $customData;
    }

    /**
     * Getter for hook type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns an array of action objects to be executed within the hook.
     *
     * @return array
     */
    public function getActions()
    {
        $mergeUtil = new ActionMergeUtil($this->parentName, $this->getType());
        return $mergeUtil->resolveActionSteps($this->actions);
    }

    /**
     * Returns an array of customData to be interperpreted by the generator.
     * @return array|null
     */
    public function getCustomData()
    {
        return $this->customData;
    }
}
