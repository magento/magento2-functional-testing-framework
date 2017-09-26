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
     * @param array $actions
     * @param array $customData
     */
    public function __construct($type, $actions, $customData = null)
    {
        $this->type = $type;
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
        $mergeUtil = new ActionMergeUtil();
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
