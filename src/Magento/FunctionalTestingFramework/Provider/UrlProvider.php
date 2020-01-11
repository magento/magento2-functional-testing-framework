<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Provider;

use Magento\FunctionalTestingFramework\Page\Objects\PageObject;

/**
 * Provider responsible for returning right URLs for provided scope.
 */
class UrlProvider
{
    /**
     * Returns proper Base URL for specified Area.
     *
     * @param string|null $customArea
     * @return string
     */
    public static function getBaseUrl($customArea = null): string
    {
        $baseUrl = getenv('MAGENTO_BASE_URL');

        switch ($customArea) {
            case PageObject::ADMIN_AREA:
                $backendName = getenv('MAGENTO_BACKEND_NAME');
                $baseUrl = self::getBackendBaseUrl() ?: $baseUrl;

                return rtrim($baseUrl, '/') . '/' . rtrim($backendName, '/') . '/';
        }

        return $baseUrl;
    }

    /**
     * Returns MAGENTO_BACKEND_BASE_URL if set or null
     *
     * @return string|null
     */
    public static function getBackendBaseUrl()
    {
        $backendBaseUrl = getenv('MAGENTO_BACKEND_BASE_URL');

        return $backendBaseUrl ?: null;
    }
}
