<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\Exceptions;

use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

/**
 * Class TestReferenceException
 */
class TestReferenceException extends \Exception
{
    /**
     * TestReferenceException constructor.
     * @param string $message
     * @param array  $context
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function __construct($message, $context = [])
    {
        list($childClass, $callingClass) = debug_backtrace(false, 2);
        LoggingUtil::getInstance()->getLogger($callingClass['class'])->error(
            "Line {$callingClass['line']}: $message",
            $context
        );

        parent::__construct($message);
    }
}
