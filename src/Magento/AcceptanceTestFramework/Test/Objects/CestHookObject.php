<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\Test\Objects;

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
     * CestHookObject constructor.
     * @param string $type
     * @param array $actions
     */
    public function __construct($type, $actions)
    {
        $this->type = $type;
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
     * Returns an array of action objects to be executed within the hook.
     *
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }
}
