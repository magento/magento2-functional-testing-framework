<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension;

/**
 * Class ErrorLogger
 * @package Magento\FunctionalTestingFramework\Extension
 */
class ErrorLogger
{
    const LOG_TYPE_BROWSER = "browser";
    const ERROR_TYPE_JAVASCRIPT = "javascript";

    /**
     * Error Logger Instance
     * @var ErrorLogger
     */
    private static $errorLogger;

    /**
     * Singleton method to return ErrorLogger.
     * @return ErrorLogger
     */
    public static function getInstance()
    {
        if (!self::$errorLogger) {
            self::$errorLogger = new ErrorLogger();
        }

        return self::$errorLogger;
    }

    /**
     * ErrorLogger constructor.
     */
    private function __construct()
    {
        // private constructor
    }

    /**
     * Loops through stepEvent for browser log entries
     *
     * @param \Magento\FunctionalTestingFramework\Module\MagentoWebDriver $module
     * @param \Codeception\Event\StepEvent                                $stepEvent
     * @return void
     */
    public function logErrors($module, $stepEvent)
    {
        //Types available should be "server", "browser", "driver". Only care about browser at the moment.
        if (in_array(self::LOG_TYPE_BROWSER, $module->webDriver->manage()->getAvailableLogTypes())) {
            $browserLogEntries = $module->webDriver->manage()->getLog(self::LOG_TYPE_BROWSER);
            $jsErrors = $this->getLogsOfType($browserLogEntries, self::ERROR_TYPE_JAVASCRIPT);
            foreach ($jsErrors as $entry) {
                $this->logError(self::ERROR_TYPE_JAVASCRIPT, $stepEvent, $entry);
                //Set javascript error in MagentoWebDriver internal array
                $module->setJsError("ERROR({$entry["level"]}) - " . $entry["message"]);
            }
        }
    }

    /**
     * Loops through given log and returns entries of the given type.
     *
     * @param array $log
     * @param string $type
     * @return array
     */
    public function getLogsOfType($log, $type)
    {
        $errors = [];
        foreach ($log as $entry) {
            if (array_key_exists("source", $entry) && $entry["source"] === $type) {
                $errors[] = $entry;
            }
        }
        return $errors;
    }

    /**
     * Loops through given log and filters entries of the given type.
     *
     * @param array $log
     * @param string $type
     * @return array
     */
    public function filterLogsOfType($log, $type)
    {
        $errors = [];
        foreach ($log as $entry) {
            if (array_key_exists("source", $entry) && $entry["source"] !== $type) {
                $errors[] = $entry;
            }
        }
        return $errors;
    }

    /**
     * Logs errors to console/report.
     * @param string                       $type
     * @param \Codeception\Event\StepEvent $stepEvent
     * @param array                        $entry
     * @return void
     */
    private function logError($type, $stepEvent, $entry)
    {
        //TODO Add to overall log
        $stepEvent->getTest()->getScenario()->comment("{$type} ERROR({$entry["level"]}) - " . $entry["message"]);
    }
}
