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
     * Singleton LogginUtil Instance
     *
     * @var LoggingUtil
     */
    private static $INSTANCE;

    /**
     * Singleton accessor for instance variable
     *
     * @return LoggingUtil
     */
    public static function getInstance()
    {
        if (self::$INSTANCE == null) {
            self::$INSTANCE = new LoggingUtil();
        }

        return self::$INSTANCE;
    }

    /**
     * Constructor for Logging Util
     */
    private function __construct()
    {
        // private constructor
    }

    /**
     * Creates a new logger instances based on class name if it does not exist. If logger instance already exists, the
     * existing instance is simply returned.
     *
     * @param string $clazz
     * @return MftfLogger
     * @throws \Exception
     */
    public function getLogger($clazz)
    {
        if ($clazz == null) {
            throw new \Exception("You must pass a class to receive a logger");
        }

        if (!array_key_exists($clazz, $this->loggers)) {
            $logger = new MftfLogger($clazz);
            $logger->pushHandler(new StreamHandler($this->getLoggingPath()));
            $this->loggers[$clazz] = $logger;
        }

        return $this->loggers[$clazz];
    }

    /**
     * Function which returns a static path to the the log file.
     *
     * @return string
     */
    public function getLoggingPath()
    {
        return TESTS_BP . DIRECTORY_SEPARATOR . "mftf.log";
    }
}
