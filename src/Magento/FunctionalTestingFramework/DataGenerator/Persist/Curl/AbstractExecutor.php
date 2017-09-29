<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Persist\Curl;

use Magento\FunctionalTestingFramework\Util\Protocol\CurlInterface;

/**
 * Abstract Curl executor.
 */
abstract class AbstractExecutor implements CurlInterface
{
    /**
     * Base url.
     *
     * @var string
     */
    protected static $baseUrl = null;

    /**
     * Resolve base url.
     *
     * @return void
     */
    protected static function resolveBaseUrl()
    {

        if ((getenv('MAGENTO_RESTAPI_SERVER_HOST') !== false)
            && (getenv('MAGENTO_RESTAPI_SERVER_HOST') !== '') ) {
            self::$baseUrl = getenv('MAGENTO_RESTAPI_SERVER_HOST');
        } else {
            self::$baseUrl = getenv('MAGENTO_BASE_URL');
        }

        if ((getenv('MAGENTO_RESTAPI_SERVER_PORT') !== false)
            && (getenv('MAGENTO_RESTAPI_SERVER_PORT') !== '')) {
            self::$baseUrl .= ':' . getenv('MAGENTO_RESTAPI_SERVER_PORT');
        }

        self::$baseUrl = rtrim(self::$baseUrl, '/') . '/';
    }
}
