<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage\FileStorage;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage\VaultStorage;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage\AwsSecretsManagerStorage;
use Magento\FunctionalTestingFramework\Util\Path\UrlFormatter;

class CredentialStore
{
    const ARRAY_KEY_FOR_VAULT = 'vault';
    const ARRAY_KEY_FOR_FILE = 'file';
    const ARRAY_KEY_FOR_AWS_SECRETS_MANAGER = 'aws';

    const CREDENTIAL_STORAGE_INFO = 'You need to configure at least one of these options: '
        . '.credentials file, HashiCorp Vault or AWS Secrets Manager correctly';

    /**
     * Credential storage array
     *
     * @var array
     */
    private $credStorage = [];

    /**
     * Singleton instance
     *
     * @var CredentialStore
     */
    private static $INSTANCE = null;

    /**
     * Static singleton getter for CredentialStore Instance
     *
     * @return CredentialStore
     * @throws TestFrameworkException
     */
    public static function getInstance()
    {
        if (self::$INSTANCE == null) {
            self::$INSTANCE = new CredentialStore();
        }

        return self::$INSTANCE;
    }

    /**
     * CredentialStore constructor
     *
     * @throws TestFrameworkException
     */
    private function __construct()
    {
        // Initialize credential storage by defined order of precedence as the following
        $this->initializeFileStorage();
        $this->initializeVaultStorage();
        $this->initializeAwsSecretsManagerStorage();

        if (empty($this->credStorage)) {
            throw new TestFrameworkException(
                'Invalid Credential Storage. ' . self::CREDENTIAL_STORAGE_INFO . '.'
            );
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
        // Get secret data from storage according to the order they are stored which follows this precedence:
        // FileStorage > VaultStorage > AwsSecretsManagerStorage
        foreach ($this->credStorage as $storage) {
            $value = $storage->getEncryptedValue($key);
            if (null !== $value) {
                return $value;
            }
        }

        throw new TestFrameworkException(
            "{$key} not found. " . self::CREDENTIAL_STORAGE_INFO
            . ' and ensure key, value exists to use _CREDS in tests.'
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
        // Loop through storage to decrypt value
        foreach ($this->credStorage as $storage) {
            return $storage->getDecryptedValue($value);
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
        // Loop through storage to decrypt all occurrences from input string
        foreach ($this->credStorage as $storage) {
            return $storage->getAllDecryptedValuesInString($string);
        }
    }

    /**
     * Initialize file storage
     *
     * @return void
     */
    private function initializeFileStorage()
    {
        // Initialize file storage
        try {
            $this->credStorage[self::ARRAY_KEY_FOR_FILE]  = new FileStorage();
        } catch (TestFrameworkException $e) {
        }
    }

    /**
     * Initialize Vault storage
     *
     * @return void
     */
    private function initializeVaultStorage()
    {
        // Initialize vault storage
        $cvAddress = getenv('CREDENTIAL_VAULT_ADDRESS');
        $cvSecretPath = getenv('CREDENTIAL_VAULT_SECRET_BASE_PATH');
        if ($cvAddress !== false && $cvSecretPath !== false) {
            try {
                $this->credStorage[self::ARRAY_KEY_FOR_VAULT] = new VaultStorage(
                    UrlFormatter::format($cvAddress, false),
                    '/' . trim($cvSecretPath, '/')
                );
            } catch (TestFrameworkException $e) {
            }
        }
    }

    /**
     * Initialize AWS Secrets Manager storage
     *
     * @return void
     */
    private function initializeAwsSecretsManagerStorage()
    {
        // Initialize AWS Secrets Manager storage
        $awsRegion = getenv('CREDENTIAL_AWS_SECRETS_MANAGER_REGION');
        $awsProfile = getenv('CREDENTIAL_AWS_SECRETS_MANAGER_PROFILE');
        $awsId = getenv('CREDENTIAL_AWS_ACCOUNT_ID');
        if (!empty($awsRegion)) {
            if (empty($awsProfile)) {
                $awsProfile = null;
            }
            if (empty($awsId)) {
                $awsId = null;
            }
            try {
                $this->credStorage[self::ARRAY_KEY_FOR_AWS_SECRETS_MANAGER] = new AwsSecretsManagerStorage(
                    $awsRegion,
                    $awsProfile,
                    $awsId
                );
            } catch (TestFrameworkException $e) {
            }
        }
    }
}
