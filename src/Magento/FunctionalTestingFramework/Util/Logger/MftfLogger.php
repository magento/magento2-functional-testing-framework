<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class MftfLogger extends Logger
{
    /**
     * Prints a deprecation warning, as well as adding a log at the WARNING level.
     *
     * @param  string $message The log message.
     * @param  array  $context The log context.
     * @return void
     */
    public function deprecation($message, array $context = [])
    {
        $message = "DEPRECATION: " . $message;
        print ($message . json_encode($context) . "\n");
        parent::warning($message, $context);
    }
}
