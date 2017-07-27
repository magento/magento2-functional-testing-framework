<?php

namespace Magento\AcceptanceTestFramework\Exceptions;

use Exception;

class XmlException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }

}