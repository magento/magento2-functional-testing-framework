<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Provider;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Page\Objects\PageObject;
use Magento\FunctionalTestingFramework\Util\Path\UrlFormatter;

/**
 * Provider responsible for returning right URLs for provided scope.
 */
class UrlProvider
{
    /**
     * Magento Web API Base URL
     *
     * @var string null
     */
    private static $webApiBaseUrl = null;

    /**
     * Returns proper Base URL for specified Area.
     *
     * @param string|null $customArea
     * @param boolean     $withTrailingSeparator
     * @return string
     * @throws TestFrameworkException
     */
    public static function getBaseUrl($customArea = null, $withTrailingSeparator = true)
    {

        try {
            $baseUrl = getenv('MAGENTO_BASE_URL');

            switch ($customArea) {
                case PageObject::ADMIN_AREA:
                    $backendName = getenv('MAGENTO_BACKEND_NAME');
                    $baseUrl = self::getBackendBaseUrl() ?: $baseUrl;

                    return UrlFormatter::format(
                        UrlFormatter::format($baseUrl) . $backendName,
                        $withTrailingSeparator
                    );
            }
            return UrlFormatter::format($baseUrl, $withTrailingSeparator);
        } catch (TestFrameworkException $e) {
        }

        throw new TestFrameworkException(
            'Unable to retrieve Magento Base URL. Please check .env and set either:'
            . PHP_EOL
            . '"MAGENTO_BASE_URL" and "MAGENTO_BACKEND_NAME"'
            . PHP_EOL
            . 'or'
            . PHP_EOL
            . '"MAGENTO_BACKEND_BASE_URL"'
        );
    }

    /**
     * Returns MAGENTO_BACKEND_BASE_URL if set or null
     *
     * @param boolean $withTrailingSeparator
     * @return string|null
     * @throws TestFrameworkException
     */
    public static function getBackendBaseUrl($withTrailingSeparator = true)
    {
        $bUrl = getenv('MAGENTO_BACKEND_BASE_URL');

        $backendBaseUrl = $bUrl ?: null;

        if ($backendBaseUrl) {
            $backendBaseUrl = UrlFormatter::format($backendBaseUrl, $withTrailingSeparator);
        }
        return $backendBaseUrl;
    }

    /**
     * Return Web API Base URL
     *
     * @param boolean $withTrailingSeparator
     * @return string
     * @throws TestFrameworkException
     */
    public static function getWebApiBaseUrl($withTrailingSeparator = true)
    {
        if (!self::$webApiBaseUrl) {
            try {
                $webapiHost = getenv('MAGENTO_RESTAPI_SERVER_HOST');
                $webapiPort = getenv("MAGENTO_RESTAPI_SERVER_PORT");
                $webapiProtocol = getenv("MAGENTO_RESTAPI_SERVER_PROTOCOL");

                if ($webapiHost && $webapiProtocol) {
                    $baseUrl = UrlFormatter::format(
                        sprintf('%s://%s', $webapiProtocol, $webapiHost),
                        false
                    );
                } elseif ($webapiHost) {
                    $baseUrl = UrlFormatter::format($webapiHost, false);
                }

                if (!isset($baseUrl)) {
                    $baseUrl = self::getBaseUrl();
                }

                if ($webapiPort) {
                    $baseUrl .= ':' . $webapiPort;
                }

                self::$webApiBaseUrl = $baseUrl . '/rest';
            } catch (TestFrameworkException $e) {
            }
        }
        if (self::$webApiBaseUrl) {
            return UrlFormatter::format(self::$webApiBaseUrl, $withTrailingSeparator);
        }
        throw new TestFrameworkException(
            'Unable to retrieve Magento Web API Base URL. Please check .env and set either:'
            . PHP_EOL
            . '"MAGENTO_BASE_URL"'
            . PHP_EOL
            . 'or'
            . PHP_EOL
            . '"MAGENTO_RESTAPI_SERVER_HOST"'
        );
    }
}
