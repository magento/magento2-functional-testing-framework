<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\Config;

/**
 * Class ValidationState
 * Used for Object Manager.
 *
 * @internal
 */
class ValidationState implements ValidationStateInterface
{
    /**
     * @var string
     */
    protected $_appMode;

    /**
     * @constructor
     * @param string $appMode
     */
    public function __construct($appMode)
    {
        $this->_appMode = $appMode;
    }

    /**
     * Retrieve current validation state
     *
     * @return boolean
     */
    public function isValidationRequired()
    {
        return $this->_appMode == 'developer'; // @todo
    }
}
