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
        if (in_array("browser", $module->webDriver->manage()->getAvailableLogTypes())) {
            $browserLogEntries = $module->webDriver->manage()->getLog("browser");
            foreach ($browserLogEntries as $entry) {
                if (array_key_exists("source", $entry) && $entry["source"] === "javascript") {
                    $this->logError("javascript", $stepEvent, $entry);
                    //Set javascript error in MagentoWebDriver internal array
                    $module->setJsError("ERROR({$entry["level"]}) - " . $entry["message"]);
                }
            }
        }
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
