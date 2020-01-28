<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage;

abstract class BaseStorage
{
    const ENCRYPTION_ALGO = "AES-256-CBC";

    /**
     * Initial vector for open_ssl encryption
     *
     * @var string
     */
    protected static $iv = null;

    /**
     * Key for open_ssl encryption/decryption
     *
     * @var string
     */
    protected static $encodedKey = null;

    /**
     * Accessed key/value secret data pairs
     *
     * @var array
     */
    protected static $cachedSecretData = [];

    /**
     * BaseStorage constructor
     */
    public function __construct()
    {
        if (null === self::$encodedKey) {
            self::$encodedKey = base64_encode(openssl_random_pseudo_bytes(16));
            self::$iv = substr(hash('sha256', self::$encodedKey), 0, 16);
        }
    }

    /**
     * Returns the encrypted value based on corresponding key
     *
     * @param string $key
     * @return string|null
     */
    public function getEncryptedValue($key)
    {
        if (!array_key_exists($key, self::$cachedSecretData)) {
            return null;
        }
        return self::$cachedSecretData[$key] ?? null;
    }

    /**
     * Takes a value encrypted at runtime and decrypts it using the object's initial vector
     * return the decrypted string on success or false on failure
     *
     * @param string $value
     * @return string|false The decrypted string on success or false on failure
     */
    public static function getDecryptedValue($value)
    {
        return openssl_decrypt($value, self::ENCRYPTION_ALGO, self::$encodedKey, 0, self::$iv);
    }

    /**
     * Takes a string that contains encrypted data at runtime and decrypts each value
     * return false if no decryption happens or a failure occurs
     *
     * @param string $string
     * @return string|false The decrypted string on success or false on failure
     */
    public static function getAllDecryptedValuesInString($string)
    {
        $decrypted = false;
        foreach (self::$cachedSecretData as $key => $secretValue) {
            if (strpos($string, $secretValue) !== false) {
                $decryptedValue = self::getDecryptedValue($secretValue);
                if ($decryptedValue === false) {
                    return false;
                }
                if (!$decrypted) {
                    $decrypted = true;
                }
                $string = str_replace($secretValue, $decryptedValue, $string);
            }
        }
        return $decrypted ? $string : false;
    }
}
