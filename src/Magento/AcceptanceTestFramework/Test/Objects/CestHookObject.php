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
     * Type.
     *
     * @var string
     */
    private $type;

    /**
     * Actions.
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
     * Returns type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns actions.
     *
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }
}
