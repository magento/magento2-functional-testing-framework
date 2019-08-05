<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Vault\Client;
use VaultTransports\Guzzle6Transport;

class VaultStorage extends BaseStorage
{
    const MFTF_PATH = '/mftf';
    /**
     * Adobe Vault
     */
    const BASE_PATH = '/dx_magento_qe';
    const KV_DATA = 'data';

    /**
     * Vault client
     *
     * @var Client
     */
    private $client = null;

    /**
     * Vault token
     *
     * @var string
     */
    private $token;

    /**
     * CredentialVault constructor
     *
     * @param string $baseUrl
     * @param string $token
     * @throws TestFrameworkException
     */
    public function __construct($baseUrl, $token)
    {
        parent::__construct();
        if (null === $this->client) {
            // Creating the client using Guzzle6 Transport and passing a custom url
            $this->client = new Client(new Guzzle6Transport(['base_uri' => $baseUrl]));
        }
        $this->token = $token;
        if (!$this->authenticated()) {
            throw new TestFrameworkException("Credential vault is not used: cannot authenticate");
        }
    }

    /**
     * Returns the value of a secret based on corresponding key
     *
     * @param string $key
     * @return string|null
     */
    public function getEncryptedValue($key)
    {
        // Check if secret is in cached array
        if (null !== ($value = parent::getEncryptedValue($key))) {
            return $value;
        }

        if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
            LoggingUtil::getInstance()->getLogger(VaultStorage::class)->debug(
                "Retrieving secret for key name {$key} from vault"
            );
        }

        $reValue = null;
        try {
            // Split vendor/key to construct secret path
            list($vendor, $key) = explode('/', trim($key, '/'), 2);
            $url = self::BASE_PATH
                . (empty(self::KV_DATA) ? '' : '/' . self::KV_DATA)
                . self::MFTF_PATH
                . '/'
                . $vendor
                . '/'
                . $key;
            // Read value by key from vault
            $value = $this->client->read($url)->getData()[self::KV_DATA][$key];
            // Encrypt value for return
            $reValue = openssl_encrypt($value, parent::ENCRYPTION_ALGO, parent::$encodedKey, 0, parent::$iv);
            parent::$cachedSecretData[$key] = $reValue;
        } catch (\Exception $e) {
            if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
                LoggingUtil::getInstance()->getLogger(VaultStorage::class)->debug(
                    "Unable to read secret for key name {$key} from vault"
                );
            }
        }
        return $reValue;
    }

    /**
     * Check if vault token is valid
     *
     * @return boolean
     */
    private function authenticated()
    {
        try {
            // Authenticating using token auth backend
            $authenticated = $this->client
                ->setAuthenticationStrategy(new VaultTokenAuthStrategy($this->token))
                ->authenticate();

            if ($authenticated) {
                return true;
            }
        } catch (\Exception $e) {
        }
        return false;
    }
}
