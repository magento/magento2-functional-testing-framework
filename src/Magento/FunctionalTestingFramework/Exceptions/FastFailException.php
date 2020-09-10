<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Exceptions;

use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

/**
 * Class FastFailException
 *
 * This exception type should not be caught and should allow fast fail of current execution
 */
class FastFailException extends \Exception
{
    /**
     * Exception context
     *
     * @var array
     */
    protected $context;

    /**
     * FastFailException constructor
     *
     * @param string $message
     * @param array  $context
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function __construct($message, $context = [])
    {
        list($childClass, $callingClass) = debug_backtrace(false, 2);
        LoggingUtil::getInstance()->getLogger($callingClass['class'])->error(
            $message,
            $context
        );

        $this->context = $context;
        parent::__construct($message);
    }

    /**
     * Return exception context
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
}
