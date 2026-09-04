<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
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
     * @var TOTP[]
     */
    private static $totps = [];

    /**
     * Return OTP for custom secret stored in `magento/tfa/OTP_SHARED_SECRET`
     *
     * @param string|null $path
     * @return string
     * @throws TestFrameworkException
     */
    public static function getOTP(?string $path = null)
    {
        if ($path === null) {
            $path = self::OTP_SHARED_SECRET_PATH;
        }
        return self::create($path)->now();
    }

    /**
     * Create TOTP object
     *
     * @param string $path
     * @return TOTP
     * @throws TestFrameworkException
     */
    private static function create($path)
    {
        if (!isset(self::$totps[$path])) {
            try {
                // Get shared secret from Credential storage
                $encryptedSecret = CredentialStore::getInstance()->getSecret($path);
                $secret = CredentialStore::getInstance()->decryptSecretValue($encryptedSecret);
            } catch (TestFrameworkException $e) {
                throw new TestFrameworkException('Unable to get OTP' . PHP_EOL . $e->getMessage());
            }

            self::$totps[$path] = TOTP::create(
                $secret,
                TOTP::DEFAULT_PERIOD,
                TOTP::DEFAULT_DIGEST,
                TOTP::DEFAULT_DIGITS,
                TOTP::DEFAULT_EPOCH,
                new Clock()
            );
            self::$totps[$path]->setIssuer('MFTF');
            self::$totps[$path]->setLabel('MFTF Testing');
        }
        return self::$totps[$path];
    }
}
