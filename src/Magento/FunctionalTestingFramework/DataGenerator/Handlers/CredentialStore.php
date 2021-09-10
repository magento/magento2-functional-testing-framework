<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers;

use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage\BaseStorage;
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
     * @var BaseStorage[]
     */
    private $credStorage = [];

    /**
     * Boolean to indicate if credential storage have been initialized
     *
     * @var boolean
     */
    private $initialized;

    /**
     * Singleton instance
     *
     * @var CredentialStore
     */
    private static $INSTANCE = null;

    /**
     * Exception contexts
     *
     * @var ExceptionCollector
     */
    private $exceptionContexts;

    /**
     * Static singleton getter for CredentialStore Instance
     *
     * @return CredentialStore
     */
    public static function getInstance()
    {
        if (self::$INSTANCE === null) {
            self::$INSTANCE = new CredentialStore();
        }

        return self::$INSTANCE;
    }

    /**
     * CredentialStore constructor
     */
    private function __construct()
    {
        $this->initialized = false;
        $this->exceptionContexts = new ExceptionCollector();
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
        // Initialize credential storage if it's not been done
        $this->initializeCredentialStorage();

        // Get secret data from storage according to the order they are stored which follows this precedence:
        // FileStorage > VaultStorage > AwsSecretsManagerStorage
        foreach ($this->credStorage as $storage) {
            $value = $storage->getEncryptedValue($key);
            if (null !== $value) {
                return $value;
            }
        }

        $exceptionContexts = $this->getExceptionContexts();
        $this->resetExceptionContext();
        throw new TestFrameworkException(
            "{$key} not found. " . self::CREDENTIAL_STORAGE_INFO
            . " and ensure key, value exists to use _CREDS in tests."
            . $exceptionContexts
        );
    }

    /**
     * Return decrypted input value
     *
     * @param string $value
     * @return string|false The decrypted string on success or false on failure
     * @throws TestFrameworkException
     */
    public function decryptSecretValue($value)
    {
        // Initialize credential storage if it's not been done
        $this->initializeCredentialStorage();

        // Decrypt secret value
        return BaseStorage::getDecryptedValue($value);
    }

    /**
     * Return decrypted values for all occurrences from input string
     *
     * @param string $string
     * @return string|false The decrypted string on success or false on failure
     * @throws TestFrameworkException
     */
    public function decryptAllSecretsInString($string)
    {
        // Initialize credential storage if it's not been done
        $this->initializeCredentialStorage();

        // Decrypt all secret values in string
        return BaseStorage::getAllDecryptedValuesInString($string);
    }

    /**
     * Setter for exception contexts
     *
     * @param string $type
     * @param string $context
     * @return void
     */
    public function setExceptionContexts($type, $context)
    {
        $typeArray = [self::ARRAY_KEY_FOR_FILE, self::ARRAY_KEY_FOR_VAULT, self::ARRAY_KEY_FOR_AWS_SECRETS_MANAGER];
        if (in_array($type, $typeArray) && !empty($context)) {
            $this->exceptionContexts->addError($type, $context);
        }
    }

    /**
     * Return collected exception contexts
     *
     * @return string
     */
    private function getExceptionContexts()
    {
        // Gather all exceptions collected
        $exceptionMessage = "\n";
        foreach ($this->exceptionContexts->getErrors() as $type => $exceptions) {
            $exceptionMessage .= "\nException from ";
            if ($type === self::ARRAY_KEY_FOR_FILE) {
                $exceptionMessage .= "File Storage: \n";
            }
            if ($type === self::ARRAY_KEY_FOR_VAULT) {
                $exceptionMessage .= "Vault Storage: \n";
            }
            if ($type === self::ARRAY_KEY_FOR_AWS_SECRETS_MANAGER) {
                $exceptionMessage .= "AWS Secrets Manager Storage: \n";
            }

            if (is_array($exceptions)) {
                $exceptionMessage .= implode("\n", $exceptions) . "\n";
            } else {
                $exceptionMessage .= $exceptions . "\n";
            }
        }
        return $exceptionMessage;
    }

    /**
     * Reset exception contexts to empty array
     *
     * @return void
     */
    private function resetExceptionContext()
    {
        $this->exceptionContexts->reset();
    }

    /**
     * Initialize all available credential storage
     *
     * @return void
     * @throws TestFrameworkException
     */
    private function initializeCredentialStorage()
    {
        if (!$this->initialized) {
            // Initialize credential storage by defined order of precedence as the following
            $this->initializeFileStorage();
            $this->initializeVaultStorage();
            $this->initializeAwsSecretsManagerStorage();
            $this->initialized = true;
        }

        if (empty($this->credStorage)) {
            throw new TestFrameworkException(
                'Invalid Credential Storage. ' . self::CREDENTIAL_STORAGE_INFO
                . '.' . $this->getExceptionContexts()
            );
        }
        $this->resetExceptionContext();
    }

    /**
     * Initialize file storage
     *
     * @return void
     */
    private function initializeFileStorage(): void
    {
        // Initialize file storage
        try {
            $fileStorage = new FileStorage();
            $fileStorage->initialize();
            $this->credStorage[self::ARRAY_KEY_FOR_FILE]  = $fileStorage;
        } catch (TestFrameworkException $e) {
            // Print error message in console
            print_r($e->getMessage());
            // Save to exception context for Allure report
            $this->setExceptionContexts(self::ARRAY_KEY_FOR_FILE, $e->getMessage());
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
                // Print error message in console
                print_r($e->getMessage());
                // Save to exception context for Allure report
                $this->setExceptionContexts(self::ARRAY_KEY_FOR_VAULT, $e->getMessage());
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
                // Print error message in console
                print_r($e->getMessage());
                // Save to exception context for Allure report
                $this->setExceptionContexts(self::ARRAY_KEY_FOR_AWS_SECRETS_MANAGER, $e->getMessage());
            }
        }
    }
}
