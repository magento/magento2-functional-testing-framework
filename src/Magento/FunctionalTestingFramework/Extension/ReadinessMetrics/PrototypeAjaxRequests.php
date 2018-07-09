<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension\ReadinessMetrics;

use Facebook\WebDriver\Exception\UnexpectedAlertOpenException;

/**
 * Class PrototypeAjaxRequests
 *
 * Looks for all active prototype ajax requests to finish before passing the readiness check
 */
class PrototypeAjaxRequests extends AbstractMetricCheck
{
    /**
     * Metric passes once there are no remaining active requests
     *
     * @param integer $value
     * @return boolean
     */
    protected function doesMetricPass($value)
    {
        return $value == 0;
    }

    /**
     * Grabs the number of active prototype ajax requests if available
     *
     * @return integer
     * @throws UnexpectedAlertOpenException
     */
    protected function fetchValueFromPage()
    {
        return intval(
            $this->executeJS(
                'if (!!window.Prototype) {
                    return window.Ajax.activeRequestCount;
                }
                return 0;'
            )
        );
    }

    /**
     * Active request count can get stuck above zero if an exception is thrown during a callback, causing the
     * ajax handler method to fail before decrementing the request count
     *
     * @return void
     * @throws UnexpectedAlertOpenException
     */
    protected function clearFailureOnPage()
    {
        $this->executeJS('if (!!window.Prototype) { window.Ajax.activeRequestCount = 0; };');
    }
}
