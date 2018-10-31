<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Objects;

use Magento\FunctionalTestingFramework\Test\Util\ActionMergeUtil;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;

/**
 * Class TestHookObject
 */
class TestHookObject
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
     * Array of Hook-defined data. Deprecated because no usage of property exist. Will be removed next major release.
     * @var array|null
     */
    private $customData = [];

    /**
     * TestHookObject constructor.
     * @param string $type
     * @param string $parentName
     * @param array  $actions
     */
    public function __construct($type, $parentName, $actions)
    {
        $this->type = $type;
        $this->parentName = $parentName;
        $this->actions = $actions;
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
     * Getter for hook parent name
     *
     * @return string
     */
    public function getParentName()
    {
        return $this->parentName;
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
     * Returns an array of unresolved actions
     *
     * @return array
     */
    public function getUnresolvedActions()
    {
        return $this->actions;
    }

    /**
     * Returns an array of customData to be interperpreted by the generator.
     * @return array|null
     * @deprecated because no usages where found. Will be removed next major release.
     */
    public function getCustomData()
    {
        return $this->customData;
    }
}
