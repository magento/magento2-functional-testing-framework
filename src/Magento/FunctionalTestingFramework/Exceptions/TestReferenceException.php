<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Exceptions;

/**
 * Class TestReferenceException
 */
class TestReferenceException extends \Exception
{
    /**
     * TestReferenceException constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
