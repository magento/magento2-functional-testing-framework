<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage\FileStorage;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage\VaultStorage;

class CredentialStore
{
    /**
     * Singleton instance
     *
     * @var CredentialStore
     */
    private static $INSTANCE = null;

    /**
     * File storage for credentials
     *
     * @var FileStorage
     */
    private $credFile = null;

    /**
     * Vault storage for credentials
     *
     * @var VaultStorage
     */
    private $credVault = null;

    /**
     * Static singleton getter for CredentialStore Instance
     *
     * @return CredentialStore
     */
    public static function getInstance()
    {
        if (self::$INSTANCE == null) {
            self::$INSTANCE = new CredentialStore();
        }

        return self::$INSTANCE;
    }

    /**
     * CredentialStore constructor.
     */
    private function __construct()
    {
        // Initialize vault storage
        $csBaseUrl = getenv('CREDENTIAL_VAULT_BASE_URL');
        $csToken = getenv('CREDENTIAL_VAULT_TOKEN');
        if ($csBaseUrl !== false && $csToken !== false) {
            try {
                $this->credVault = new VaultStorage(rtrim($csBaseUrl, '/'), $csToken);
            } catch (TestFrameworkException $e) {
            }
        }

        // Initialize file storage
        try {
            $this->credFile = new FileStorage();
        } catch (TestFrameworkException $e) {
        }
    }

    /**
     * Get encrypted value by key
     *
     * @param string $key
     * @return string|null
     * @throws TestFrameworkException
     */
    public function getSecret($key)
    {
        // Get secret data from vault storage first
        if (null !== $this->credVault) {
            $value = $this->credVault->getEncryptedValue($key);
            if (!empty($value)) {
                return $value;
            }
        }

        // Get secret data from file when not found in vault
        if (null !== $this->credFile) {
            $value = $this->credFile->getEncryptedValue($key);
            if (!empty($value)) {
                return $value;
            }
        }

        throw new TestFrameworkException(
            "value for key \"$key\" not found in credential storage."
        );
    }

    /**
     * Return decrypted input value
     *
     * @param string $value
     * @return string
     */
    public function decryptSecretValue($value)
    {
        if (null !== $this->credVault) {
            return $this->credVault->getDecryptedValue($value);
        }

        if (null !== $this->credFile) {
            return $this->credFile->getDecryptedValue($value);
        }
    }

    /**
     * Return decrypted values for all occurrences from input string
     *
     * @param string $string
     * @return mixed
     */
    public function decryptAllSecretsInString($string)
    {
        if (null !== $this->credVault) {
            return $this->credVault->getAllDecryptedValues($string);
        }

        if (null !== $this->credFile) {
            return $this->credFile->getAllDecryptedValues($string);
        }
    }
}
