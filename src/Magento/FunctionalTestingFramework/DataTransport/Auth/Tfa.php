<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataTransport\Auth;

use Magento\FunctionalTestingFramework\Util\MftfGlobals;
use Magento\FunctionalTestingFramework\DataTransport\Protocol\CurlInterface;
use Magento\FunctionalTestingFramework\DataTransport\Protocol\CurlTransport;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

/**
 * Class Tfa (i.e. 2FA)
 */
class Tfa
{
    const WEB_API_AUTH_GOOGLE = 'V1/tfa/provider/google/authenticate';
    const ADMIN_FORM_AUTH_GOOGLE = 'tfa/google/authpost/?isAjax=true';
    const TFA_SCHEMA = 'schema?services=twoFactorAuthAdminTokenServiceV1';

    /**
     * If 2FA is enabled
     *
     * @var boolean|null
     */
    private static $tfaEnabled = null;

    /** Rest request headers
     *
     * @var string[]
     */
    private static $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    /**
     * 2FA provider web API authentication endpoints
     *
     * @var string[]
     */
    private static $providerWebApiAuthEndpoints = [
        'google' => self::WEB_API_AUTH_GOOGLE,
    ];

    /**
     * 2FA provider admin form authentication endpoints
     *
     * @var string[]
     */
    private static $providerAdminFormAuthEndpoints = [
        'google' => self::ADMIN_FORM_AUTH_GOOGLE,
    ];

    /**
     * Check if 2FA is enabled for Magento instance under test
     *
     * @return boolean
     * @throws TestFrameworkException
     */
    public static function isEnabled()
    {
        if (self::$tfaEnabled !== null) {
            return self::$tfaEnabled;
        }

        $schemaUrl = MftfGlobals::getWebApiBaseUrl() . self::TFA_SCHEMA;
        $transport = new CurlTransport();
        try {
            $transport->write($schemaUrl, [], CurlInterface::GET, self::$headers);
            $response = $transport->read();
            $transport->close();
            $schema = json_decode($response, true);
            if (isset($schema['definitions'], $schema['paths'])) {
                return true;
            }
        } catch (TestFrameworkException $e) {
            $transport->close();
        }
        return false;
    }

    /**
     * Return provider's 2FA web API authentication endpoint
     *
     * @param string $name
     * @return string|null
     */
    public static function getProviderWebApiAuthEndpoint($name)
    {
        // Currently only support Google Authenticator
        return self::$providerWebApiAuthEndpoints[$name] ?? null;
    }

    /**
     * Return 2FA provider's admin form authentication endpoint
     *
     * @param string $name
     * @return string|null
     */
    public static function getProviderAdminFormEndpoint($name)
    {
        // Currently only support Google Authenticator
        return self::$providerAdminFormAuthEndpoints[$name] ?? null;
    }
}
