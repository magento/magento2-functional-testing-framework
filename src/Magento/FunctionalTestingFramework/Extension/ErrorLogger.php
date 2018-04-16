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
     * Gets webdriver log, prints out javascript errors when encountered.
     * @param \Facebook\WebDriver\Remote\RemoteWebDriver $webDriver
     * @param \Codeception\Event\StepEvent $stepEvent
     * @return void
     */
    public static function logJsError($webDriver, $stepEvent)
    {
        $logEntries = $webDriver->manage()->getLog("browser");
        foreach ($logEntries as $entry) {
            if ($entry["source"] === "javascript") {
                //TODO Add to overall log
                $stepEvent->getTest()->getScenario()->comment("JS ERROR({$entry["level"]}) - " . $entry["message"]);
            }
        }
    }
}
