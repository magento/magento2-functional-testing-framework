<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Logger;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class MftfLogger extends Logger
{
    /**
     * Prints a deprecation warning, as well as adds a log at the WARNING level.
     *
     * @param string  $message The log message.
     * @param array   $context The log context.
     * @param boolean $verbose
     * @return void
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException
     */
    public function deprecation($message, array $context = [], $verbose = false)
    {
        $message = "DEPRECATION: " . $message;
        // Suppress print during unit testing
        if (MftfApplicationConfig::getConfig()->getPhase() !== MftfApplicationConfig::UNIT_TEST_PHASE && $verbose) {
            print ($message . json_encode($context) . "\n");
        }
        parent::warning($message, $context);
    }

    /**
     * Prints a critical failure, as well as adds a log at the CRITICAL level.
     *
     * @param string  $message The log message.
     * @param array   $context The log context.
     * @param boolean $verbose
     * @return void
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException
     */
    public function criticalFailure($message, array $context = [], $verbose = false)
    {
        $message = "FAILURE: " . $message;
        // Suppress print during unit testing
        if (MftfApplicationConfig::getConfig()->getPhase() !== MftfApplicationConfig::UNIT_TEST_PHASE && $verbose) {
            print ($message . implode("\n", $context) . "\n");
        }
        parent::critical($message, $context);
    }

    /**
     * Adds a log record at the NOTICE level.
     *
     * @param string  $message
     * @param array   $context
     * @param boolean $verbose
     * @return void
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException
     */
    public function notification($message, array $context = [], $verbose = false)
    {
        $message = "NOTICE: " . $message;
        // Suppress print during unit testing
        if (MftfApplicationConfig::getConfig()->getPhase() !== MftfApplicationConfig::UNIT_TEST_PHASE && $verbose) {
            print ($message . implode("\n", $context) . "\n");
        }
        parent::notice($message, $context);
    }
}
