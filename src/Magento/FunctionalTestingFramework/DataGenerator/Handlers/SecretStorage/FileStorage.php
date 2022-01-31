<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;

class FileStorage extends BaseStorage
{
    /**
     * Key/value secret data pairs parsed from file
     *
     * @var array
     */
    private $secretData = [];

    /**
     * Initialize secret data value which represents encrypted credentials
     *
     * @return void
     * @throws TestFrameworkException
     */
    public function initialize(): void
    {
        if (!$this->secretData) {
            $creds = $this->readInCredentialsFile();
            $this->secretData = $this->encryptCredFileContents($creds);
        }
    }

    /**
     * Returns the value of a secret based on corresponding key
     *
     * @param string $key
     * @return string|null
     * @throws TestFrameworkException
     */
    public function getEncryptedValue($key): ?string
    {
        $this->initialize();

        // Check if secret is in cached array
        if (null !== ($value = parent::getEncryptedValue($key))) {
            return $value;
        }

        // log here for verbose config
        if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
            LoggingUtil::getInstance()->getLogger(FileStorage::class)->debug(
                "retrieving secret for key name {$key} from file"
            );
        }

        // Retrieve from file storage
        if (array_key_exists($key, $this->secretData) && (null !== ($value = $this->secretData[$key]))) {
            parent::$cachedSecretData[$key] = $value;
        }

        return $value;
    }

    /**
     * Private function which reads in secret key/values from .credentials file and stores in memory as key/value pair
     *
     * @return array
     * @throws TestFrameworkException
     */
    private function readInCredentialsFile()
    {
        $credsFilePath = str_replace(
            '.credentials.example',
            '.credentials',
            FilePathFormatter::format(TESTS_BP) . '.credentials.example'
        );

        if (!file_exists($credsFilePath)) {
            throw new TestFrameworkException(
                "Credential file is not used: .credentials file not found in " . TESTS_BP
            );
        }

        return file($credsFilePath, FILE_IGNORE_NEW_LINES);
    }

    /**
     * Function which takes the contents of the credentials file and encrypts the entries
     *
     * @param array $credContents
     * @return array
     * @throws TestFrameworkException
     */
    private function encryptCredFileContents($credContents)
    {
        $encryptedCreds = [];
        foreach ($credContents as $credValue) {
            if (substr($credValue, 0, 1) === '#' || empty($credValue)) {
                continue;
            } elseif (strpos($credValue, "=") === false) {
                throw new TestFrameworkException(
                    $credValue . " not configured correctly in .credentials file"
                );
            }

            list($key, $value) = explode("=", $credValue, 2);
            if (!empty($value)) {
                $encryptedCreds[$key] = openssl_encrypt(
                    $value,
                    parent::ENCRYPTION_ALGO,
                    parent::$encodedKey,
                    0,
                    parent::$iv
                );
            }
        }
        return $encryptedCreds;
    }
}
