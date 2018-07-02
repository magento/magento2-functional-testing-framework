<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;

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
            $config['url'] = self::sanitizeUrl($config['url']);
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
        if ($config['protocol'] == '%SELENIUM_PROTOCOL%') {
            $config['protocol'] = "http";
        }
        if ($config['host'] == '%SELENIUM_HOST%') {
            $config['host'] = "127.0.0.1";
        }
        if ($config['port'] == '%SELENIUM_PORT%') {
            $config['port'] = "4444";
        }
        if ($config['path'] == '%SELENIUM_PATH%') {
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

    /**
     * Sanitizes and returns given URL.
     * @param string $url
     * @return string
     */
    public static function sanitizeUrl($url)
    {
        if (strlen($url) == 0 && !MftfApplicationConfig::getConfig()->forceGenerateEnabled()) {
            trigger_error("MAGENTO_BASE_URL must be defined in .env", E_USER_ERROR);
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === true) {
            return rtrim($url, "/") . "/";
        }

        $urlParts = parse_url($url);

        if (!isset($urlParts['scheme'])) {
            $urlParts['scheme'] = "http";
        }
        if (!isset($urlParts['host'])) {
            $urlParts['host'] = rtrim($urlParts['path'], "/");
            $urlParts['host'] = str_replace("//", "/", $urlParts['host']);
            unset($urlParts['path']);
        }

        if (!isset($urlParts['path'])) {
            $urlParts['path'] = "/";
        } else {
            $urlParts['path'] = rtrim($urlParts['path'], "/") . "/";
        }

        return str_replace("///", "//", self::buildUrl($urlParts));
    }

    /**
     * Returns url from $parts given, used with parse_url output for convenience.
     * This only exists because of deprecation of http_build_url, which does the exact same thing as the code below.
     * @param array $parts
     * @return string
     */
    private static function buildUrl(array $parts)
    {
        $get = function ($key) use ($parts) {
            return isset($parts[$key]) ? $parts[$key] : null;
        };

        $pass      = $get('pass');
        $user      = $get('user');
        $userinfo  = $pass !== null ? "$user:$pass" : $user;
        $port      = $get('port');
        $scheme    = $get('scheme');
        $query     = $get('query');
        $fragment  = $get('fragment');
        $authority =
            ($userinfo !== null ? "$userinfo@" : '') .
            $get('host') .
            ($port ? ":$port" : '');

        return
            (strlen($scheme) ? "$scheme:" : '') .
            (strlen($authority) ? "//$authority" : '') .
            $get('path') .
            (strlen($query) ? "?$query" : '') .
            (strlen($fragment) ? "#$fragment" : '');
    }
}
