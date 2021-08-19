<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Path\UrlFormatter;

/**
 * Class ConfigSanitizerUtil
 */
class ConfigSanitizerUtil
{
    /**
     * Sanitizes the given Webdriver Config's url and selenium env params, can be selective based on second argument.
     * @param array    $config
     * @param String[] $params
     * @return array
     */
    public static function sanitizeWebDriverConfig($config, $params = ['url', 'selenium'])
    {
        self::validateConfigBasedVars($config);

        if (in_array('url', $params)) {
            $config['url'] = UrlFormatter::format($config['url']);
        }

        if (in_array('selenium', $params)) {
            $config = self::sanitizeSeleniumEnvs($config);
        }

        return $config;
    }

    /**
     * Sets Selenium params if they are left as defaults.
     * @param array $config
     * @return array
     */
    private static function sanitizeSeleniumEnvs($config)
    {
        if ($config['protocol'] === '%SELENIUM_PROTOCOL%') {
            $config['protocol'] = "http";
        }
        if ($config['host'] === '%SELENIUM_HOST%') {
            $config['host'] = "127.0.0.1";
        }
        if ($config['port'] === '%SELENIUM_PORT%') {
            $config['port'] = "4444";
        }
        if ($config['path'] === '%SELENIUM_PATH%') {
            $config['path'] = "/wd/hub";
        }
        return $config;
    }

    /**
     * Method which validates env vars have been properly read into the config. Method implemented as part of
     * bug MQE-567
     *
     * @param array $config
     * @return void
     */
    private static function validateConfigBasedVars($config)
    {
        $configStrings = array_filter($config, function ($value) {
            return is_string($value);
        });

        foreach ($configStrings as $configKey => $configValue) {
            $var = trim((String)$configValue, '%');
            if (array_key_exists($var, $_ENV)) {
                trigger_error(
                    "Issue with setting configuration for test runs. Please make sure '{$var}' is "
                    . "not duplicated as a system level variable",
                    E_USER_ERROR
                );
            }
        }
    }
}
