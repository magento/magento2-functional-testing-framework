<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggingUtil
{
    /**
     * Private Map of Logger instances, indexed by Class Name.
     *
     * @var array
     */
    private $loggers = [];

    /**
     * Singleton LoggingUtil Instance
     *
     * @var LoggingUtil
     */
    private static $instance;

    /**
     * Singleton accessor for instance variable
     *
     * @return LoggingUtil
     */
    public static function getInstance(): LoggingUtil
    {
        if (self::$instance === null) {
            self::$instance = new LoggingUtil();
        }

        return self::$instance;
    }

    /**
     * Avoids instantiation of LoggingUtil by new.
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Avoids instantiation of LoggingUtil by clone.
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Creates a new logger instances based on class name if it does not exist. If logger instance already exists, the
     * existing instance is simply returned.
     *
     * @param string $className
     * @return MftfLogger
     * @throws \Exception
     */
    public function getLogger($className): MftfLogger
    {
        if ($className == null) {
            throw new \Exception("You must pass a class name to receive a logger");
        }

        if (!array_key_exists($className, $this->loggers)) {
            $logger = new MftfLogger($className);
            $logger->pushHandler(new StreamHandler($this->getLoggingPath()));
            $this->loggers[$className] = $logger;
        }

        return $this->loggers[$className];
    }

    /**
     * Function which returns a static path to the the log file.
     *
     * @return string
     */
    public function getLoggingPath(): string
    {
        return TESTS_BP . DIRECTORY_SEPARATOR . "mftf.log";
    }
}
