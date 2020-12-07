<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Path\UrlFormatter;

/**
 * MFTF Globals
 */
class MftfGlobals
{
    /**
     * Magento Base URL
     *
     * @var string null
     */
    private static $baseUrl = null;

    /**
     * Magento Backend Base URL
     *
     * @var string null
     */
    private static $backendBaseUrl = null;

    /**
     * Magento Web API Base URL
     *
     * @var string null
     */
    private static $webApiBaseUrl = null;

    /**
     * Returns Magento Base URL
     *
     * @param boolean $withTrailingSeparator
     * @return string
     * @throws TestFrameworkException
     */
    public static function getBaseUrl($withTrailingSeparator = true)
    {
        if (!self::$baseUrl) {
            try {
                $url = getenv('MAGENTO_BASE_URL');
                if ($url) {
                    self::$baseUrl = UrlFormatter::format($url, false);
                }
            } catch (TestFrameworkException $e) {
            }
        }

        if (self::$baseUrl) {
            return UrlFormatter::format(self::$baseUrl, $withTrailingSeparator);
        }

        throw new TestFrameworkException(
            'Unable to retrieve Magento Base URL. Please check .env and set:'
            . PHP_EOL
            . '"MAGENTO_BASE_URL"'
        );
    }

    /**
     * Return Magento Backend Base URL
     *
     * @param boolean $withTrailingSeparator
     * @return string
     * @throws TestFrameworkException
     */
    public static function getBackendBaseUrl($withTrailingSeparator = true)
    {
        if (!self::$backendBaseUrl) {
            try {
                $backendName = getenv('MAGENTO_BACKEND_NAME');
                $bUrl = getenv('MAGENTO_BACKEND_BASE_URL');
                if ($bUrl && $backendName) {
                    self::$backendBaseUrl = UrlFormatter::format(
                        UrlFormatter::format($bUrl) . $backendName,
                        false
                    );
                } else {
                    $baseUrl = getenv('MAGENTO_BASE_URL');
                    if ($baseUrl && $backendName) {
                        self::$backendBaseUrl = UrlFormatter::format(
                            UrlFormatter::format($baseUrl) . $backendName,
                            false
                        );
                    }
                }
            } catch (TestFrameworkException $e) {
            }
        }

        if (self::$backendBaseUrl) {
            return UrlFormatter::format(self::$backendBaseUrl, $withTrailingSeparator);
        }

        throw new TestFrameworkException(
            'Unable to retrieve Magento Backend Base URL. Please check .env and set either:'
            . PHP_EOL
            . '"MAGENTO_BASE_URL" and "MAGENTO_BACKEND_NAME"'
            . PHP_EOL
            . 'or'
            . PHP_EOL
            . '"MAGENTO_BACKEND_BASE_URL"'
        );
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
                    $baseUrl = MftfGlobals::getBaseUrl(false);
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
