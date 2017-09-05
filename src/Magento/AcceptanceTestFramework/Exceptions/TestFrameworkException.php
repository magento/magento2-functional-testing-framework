<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\Exceptions;

/**
 * Class TestFrameworkException
 */
class TestFrameworkException extends \Exception
{
    /**
     * TestFrameworkException constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
