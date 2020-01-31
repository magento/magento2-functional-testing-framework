<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\CredentialStore;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;
use Aws\Result;
use InvalidArgumentException;
use Exception;

class AwsSecretsManagerStorage extends BaseStorage
{
    /**
     * Mftf project path
     */
    const MFTF_PATH = 'mftf';

    /**
     * AWS Secrets Manager partial ARN
     */
    const AWS_SM_PARTIAL_ARN = 'arn:aws:secretsmanager:';

    /**
     * AWS Secrets Manager version
     *
     * Last tested version '2017-10-17'
     */
    const LATEST_VERSION = 'latest';

    /**
     * SecretsManagerClient client
     *
     * @var SecretsManagerClient
     */
    private $client = null;

    /**
     * AWS account id
     *
     * @var string
     */
    private $awsAccountId;

    /**
     * AWS account region
     *
     * @var string
     */
    private $region;

    /**
     * AwsSecretsManagerStorage constructor
     *
     * @param string $region
     * @param string $profile
     * @param string $accountId
     * @throws TestFrameworkException
     * @throws InvalidArgumentException
     */
    public function __construct($region, $profile = null, $accountId = null)
    {
        parent::__construct();
        $this->createAwsSecretsManagerClient($region, $profile);
        $this->region = $region;
        $this->awsAccountId = $accountId;
    }

    /**
     * Returns the value of a secret based on corresponding key
     *
     * @param string $key
     * @return string|null
     * @throws Exception
     */
    public function getEncryptedValue($key)
    {
        // Check if secret is in cached array
        if (null !== ($value = parent::getEncryptedValue($key))) {
            return $value;
        }

        if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
            LoggingUtil::getInstance()->getLogger(AwsSecretsManagerStorage::class)->debug(
                "Retrieving value for key name {$key} from AWS Secrets Manager"
            );
        }

        $reValue = null;
        try {
            // Split vendor/key to construct secret id
            list($vendor, $key) = explode('/', trim($key, '/'), 2);
            // If AWS account id is specified, create and use full ARN, otherwise use partial ARN as secret id
            $secretId = '';
            if (!empty($this->awsAccountId)) {
                $secretId = self::AWS_SM_PARTIAL_ARN . $this->region . ':' . $this->awsAccountId . ':secret:';
            }
            $secretId .= self::MFTF_PATH
                . '/'
                . $vendor
                . '/'
                . $key;
            // Read value by id from AWS Secrets Manager, and parse the result
            $value = $this->parseAwsSecretResult(
                $this->client->getSecretValue(['SecretId' => $secretId]),
                $key
            );
            // Encrypt value for return
            $reValue = openssl_encrypt($value, parent::ENCRYPTION_ALGO, parent::$encodedKey, 0, parent::$iv);
            parent::$cachedSecretData[$key] = $reValue;
        } catch (AwsException $e) {
            $errMessage = "\nAWS Exception:\n" . $e->getAwsErrorMessage()
                . "\nUnable to read value for key {$key} from AWS Secrets Manager\n";
            // Print error message in console
            print_r($errMessage);
            // Add error message in mftf log if verbose is enable
            if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
                LoggingUtil::getInstance()->getLogger(AwsSecretsManagerStorage::class)->debug($errMessage);
            }
            // Save to exception context for Allure report
            CredentialStore::getInstance()->setExceptionContexts(
                CredentialStore::ARRAY_KEY_FOR_AWS_SECRETS_MANAGER,
                $errMessage
            );
        } catch (\Exception $e) {
            $errMessage = "\nException:\n" . $e->getMessage()
                . "\nUnable to read value for key {$key} from AWS Secrets Manager\n";
            // Print error message in console
            print_r($errMessage);
            // Add error message in mftf log if verbose is enable
            if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
                LoggingUtil::getInstance()->getLogger(AwsSecretsManagerStorage::class)->debug($errMessage);
            }
            // Save to exception context for Allure report
            CredentialStore::getInstance()->setExceptionContexts(
                CredentialStore::ARRAY_KEY_FOR_AWS_SECRETS_MANAGER,
                $errMessage
            );
        }
        return $reValue;
    }

    /**
     * Parse AWS result object and return secret for key
     *
     * @param Result $awsResult
     * @param string $key
     * @return string
     * @throws TestFrameworkException
     */
    private function parseAwsSecretResult($awsResult, $key)
    {
        // Return secret from the associated KMS CMK
        if (isset($awsResult['SecretString'])) {
            $rawSecret = $awsResult['SecretString'];
        } else {
            throw new TestFrameworkException(
                "'SecretString' field is not set in AWS Result. Error parsing result from AWS Secrets Manager"
            );
        }

        // Secrets are saved as JSON structures of key/value pairs if using AWS Secrets Manager console, and
        // Secrets are saved as plain text if using AWS CLI. We need to handle both cases.
        $secret = json_decode($rawSecret, true);
        if (isset($secret[$key])) {
            return $secret[$key];
        } elseif (is_string($rawSecret)) {
            return $rawSecret;
        }
        throw new TestFrameworkException(
            "$key not found or value is not string . Error parsing result from AWS Secrets Manager"
        );
    }

    /**
     * Create Aws Secrets Manager client
     *
     * @param string $region
     * @param string $profile
     * @return void
     * @throws TestFrameworkException
     * @throws InvalidArgumentException
     */
    private function createAwsSecretsManagerClient($region, $profile)
    {
        if (null !== $this->client) {
            return;
        }

        $options = [
            'region' => $region,
            'version' => self::LATEST_VERSION,
        ];

        if (!empty($profile)) {
            $options['profile'] = $profile;
        }

        // Create AWS Secrets Manager client
        $this->client = new SecretsManagerClient($options);
        if ($this->client === null) {
            throw new TestFrameworkException("Unable to create AWS Secrets Manager client");
        }
    }
}
