<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers\SecretStorage;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Vault\AuthenticationStrategies\TokenAuthenticationStrategy;
use Vault\Client;
use VaultTransports\Guzzle6Transport;

class VaultStorage extends BaseStorage
{
    const MFTF_PATH = '/mftf';
    /**
     * Adobe Vault
     */
    const BASE_PATH = '/dx_magento_qe';
    const KV_DATA = '/data';
    /**
     * Local Vault
     */
    //const BASE_PATH = '/secret';
    //const KV_DATA = '/data';

    /**
     * Vault client
     *
     * @var Client
     */
    private static $client = null;

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
        if (null === self::$client) {
            // Creating the client using Guzzle6 Transport and passing a custom url
            self::$client = new Client(new Guzzle6Transport(['base_uri' => $baseUrl]));
        }
        $this->token = $token;
        if (!$this->authenticated()) {
            throw new TestFrameworkException("Credential Vault: Cannot Authenticate");
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

        try {
            // Log here for verbose config
            if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
                LoggingUtil::getInstance()->getLogger(VaultStorage::class)->debug(
                    "retrieving secret for key name {$key} from vault"
                );
            }
        } catch (\Exception $e) {
        }

        // Retrieve from vault storage
        if (!$this->authenticated()) {
            return null;
        }

        // Read value by key from vault
        list($vendor, $key) = explode('/', trim($key, '/'), 2);
        $url = self::BASE_PATH
            . (empty(self::KV_DATA) ? '' : self::KV_DATA)
            . self::MFTF_PATH
            . '/'
            . $vendor
            . '/'
            . $key;
        $value = self::$client->read($url)->getData()['data'][$key];

        if (empty($value)) {
            return null;
        }
        $eValue = openssl_encrypt($value, parent::ENCRYPTION_ALGO, parent::$encodedKey, 0, parent::$iv);
        parent::$cachedSecretData[$key] = $eValue;
        return $eValue;
    }

    /**
     * Check if vault token is still valid.
     *
     * @return boolean
     */
    private function authenticated()
    {
        try {
            // Authenticating using token auth backend.
            $authenticated = self::$client
                ->setAuthenticationStrategy(new TokenAuthenticationStrategy($this->token))
                ->authenticate();

            if ($authenticated) {
                return true;
            }
        } catch (\Exception $e) {
        }
        return false;
    }
}
