<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Logger;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

class MftfLogger extends Logger
{
    /**
     * MFTF execution phase
     *
     * @var string
     */
    private $phase;

    /**
     * MftfLogger constructor.
     *
     * @param string             $name
     * @param HandlerInterface[] $handlers
     * @param callable[]         $processors
     * @throws TestFrameworkException
     */
    public function __construct($name, array $handlers = [], array $processors = [])
    {
        parent::__construct($name, $handlers, $processors);
        $this->phase = MftfApplicationConfig::getConfig()->getPhase();
    }

    /**
     * Prints a deprecation warning, as well as adds a log at the WARNING level.
     *
     * @param string  $message The log message.
     * @param array   $context The log context.
     * @param boolean $verbose
     * @return void
     */
    public function deprecation($message, array $context = [], $verbose = false)
    {
        $message = "DEPRECATION: " . $message;
        // Suppress print during unit testing
        if ($this->phase !== MftfApplicationConfig::UNIT_TEST_PHASE && $verbose) {
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
     */
    public function criticalFailure($message, array $context = [], $verbose = false)
    {
        $message = "FAILURE: " . $message;
        // Suppress print during unit testing
        if ($this->phase !== MftfApplicationConfig::UNIT_TEST_PHASE && $verbose) {
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
     */
    public function notification($message, array $context = [], $verbose = false)
    {
        $message = "NOTICE: " . $message;
        // Suppress print during unit testing
        if ($this->phase !== MftfApplicationConfig::UNIT_TEST_PHASE && $verbose) {
            print ($message . json_encode($context) . "\n");
        }
        parent::notice($message, $context);
    }
}
