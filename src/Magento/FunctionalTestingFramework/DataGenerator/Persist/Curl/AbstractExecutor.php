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
     * Returns Magento base URL. Used as a fallback for other services (eg. WebApi, Backend)
     *
     * @var string
     */
    protected static $baseUrl = null;

    /**
     * Returns base URL for Magento instance
     * @return string
     */
    public function getBaseUrl(): string
    {
        return getenv('MAGENTO_BASE_URL');
    }
}
