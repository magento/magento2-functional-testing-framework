<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension\ReadinessMetrics;

use Facebook\WebDriver\Exception\UnexpectedAlertOpenException;

/**
 * Class RequireJsDefinitions
 *
 * Looks for all active require.js module definitions to complete before passing the readiness check
 */
class RequireJsDefinitions extends AbstractMetricCheck
{
    /**
     * Metric passes once there are no enabled modules waiting in the registry queue
     *
     * @param string|null $value
     * @return boolean
     */
    protected function doesMetricPass($value)
    {
        return $value === null;
    }

    /**
     * Retrieve the name of the first enabled module still waiting in the require.js registry queue
     *
     * @return string|null
     * @throws UnexpectedAlertOpenException
     */
    protected function fetchValueFromPage()
    {
        $script =
            'if (!window.requirejs) {
                return null;
            }
            var contexts = window.requirejs.s.contexts;
            for (var label in contexts) {
                if (contexts.hasOwnProperty(label)) {
                    var registry = contexts[label].registry;
                    for (var module in registry) {
                        if (registry.hasOwnProperty(module) && registry[module].enabled) {
                            return module;
                        }
                    }
                }
            }
            return null;';

        $moduleInProgress = $this->executeJS($script);
        if ($moduleInProgress === 'null') {
            $moduleInProgress = null;
        }
        return $moduleInProgress;
    }
}
