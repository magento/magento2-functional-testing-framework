<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension\ReadinessMetrics;

use Facebook\WebDriver\Exception\UnexpectedAlertOpenException;

/**
 * Class JQueryAjaxRequests
 *
 * Looks for all active jQuery ajax requests to finish before passing the readiness check
 */
class JQueryAjaxRequests extends AbstractMetricCheck
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
     * Grabs the number of active jQuery ajax requests if available
     *
     * @return integer
     * @throws UnexpectedAlertOpenException
     */
    protected function fetchValueFromPage()
    {
        return intval(
            $this->executeJS(
                'if (!!window.jQuery) {
                    return window.jQuery.active;
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
        $this->executeJS('if (!!window.jQuery) { window.jQuery.active = 0; };');
    }
}
