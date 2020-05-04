<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataTransport\Auth\Tfa;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use OTPHP\TOTP;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\CredentialStore;

/**
 * Class OTP
 */
class OTP
{
    const OTP_SHARED_SECRET_PATH = 'magento/tfa/OTP_SHARED_SECRET';

    /**
     * TOTP object
     *
     * @var TOTP
     */
    private static $totp = null;

    /**
     * Return OTP for custom secret stored in `magento/tfa/OTP_SHARED_SECRET`
     *
     * @return string
     * @throws TestFrameworkException
     */
    public static function getOTP()
    {
        return self::create()->now();
    }

    /**
     * Create TOTP object
     *
     * @return TOTP
     * @throws TestFrameworkException
     */
    private static function create()
    {
        if (self::$totp === null) {
            try {
                // Get shared secret from Credential storage
                $encryptedSecret = CredentialStore::getInstance()->getSecret(self::OTP_SHARED_SECRET_PATH);
                $secret = CredentialStore::getInstance()->decryptSecretValue($encryptedSecret);
            } catch (TestFrameworkException $e) {
                throw new TestFrameworkException('Unable to get OTP' . PHP_EOL . $e->getMessage());
            }

            self::$totp = TOTP::create($secret);
            self::$totp->setIssuer('MFTF');
            self::$totp->setLabel('MFTF Testing');
        }
        return self::$totp;
    }
}
