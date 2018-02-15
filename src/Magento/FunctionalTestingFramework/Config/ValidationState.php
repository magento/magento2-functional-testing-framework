<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Config;

/**
 * Class ValidationState
 * Used for Object Manager.
 *
 * @internal
 */
class ValidationState implements ValidationStateInterface
{
    /**
     * Application mode value.
     *
     * @var string
     */
    protected $appMode;

    /**
     * ValidationState constructor.
     * @param string $appMode
     */
    public function __construct($appMode)
    {
        $this->appMode = $appMode;
    }

    /**
     * Retrieve current validation state
     *
     * @return boolean
     */
    public function isValidationRequired()
    {
        return $this->appMode == 'developer'; // @todo
    }
}
