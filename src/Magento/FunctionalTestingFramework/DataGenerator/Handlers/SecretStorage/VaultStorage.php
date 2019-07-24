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
     * Default vault token file
     */
    const TOKEN_FILE = '.vault-token';
    /**
     * Default vault config file
     */
    const CONFIG_FILE = '.vault';
    /**
     * Environment variable name for vault config path
     */
    const CONFIG_PATH_ENV_VAR = 'VAULT_CONFIG_PATH';

    const TOKEN_HELPER_REGEX = "~\s*token_helper\s*=(.+)$~";

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
    private $token = null;

    /**
     * CredentialVault constructor
     *
     * @param string $baseUrl
     * @throws TestFrameworkException
     */
    public function __construct($baseUrl)
    {
        parent::__construct();
        if (null === $this->client) {
            // Creating the client using Guzzle6 Transport and passing a custom url
            $this->client = new Client(new Guzzle6Transport(['base_uri' => $baseUrl]));
        }
        $this->readVaultTokenFromFileSystem();
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

    /**
     * Read vault token from file system
     *
     * @return void
     * @throws TestFrameworkException
     */
    private function readVaultTokenFromFileSystem()
    {
        // Find user home directory
        $homeDir = getenv('HOME');
        if ($homeDir === false) {
            // If HOME is not set, don't fail right away
            $homeDir = '~/';
        } else {
            $homeDir = rtrim($homeDir, '/') . '/';
        }

        $vaultTokenFile = $homeDir . self::TOKEN_FILE;
        if (file_exists($vaultTokenFile)) {
            // Found .vault-token file in default location, construct command
            $cmd = 'cat ' .  $vaultTokenFile;
        } else {
            // Otherwise search vault config file for custom token helper script
            $vaultConfigPath = getenv(self::CONFIG_PATH_ENV_VAR);
            if ($vaultConfigPath === false) {
                $vaultConfigFile = $homeDir . self::CONFIG_FILE;
            } else {
                $vaultConfigFile = rtrim($vaultConfigPath, '/') . '/' . self::CONFIG_FILE;
            }
            // Found .vault config file, read custom token helper script and construct command
            if (file_exists($vaultConfigFile)
                && !empty($cmd = $this->getTokenHelperScript(file($vaultConfigFile, FILE_IGNORE_NEW_LINES)))) {
                $cmd = $cmd . ' get';
            } else {
                throw new TestFrameworkException(
                    'Unable to read .vault-token file. Please authenticate to vault through vault CLI first.'
                );
            }
        }
        $this->token = $this->execVaultTokenHelper($cmd);
    }

    /**
     * Get vault token helper script by parsing lines in vault config file
     *
     * @param array $lines
     * @return array
     */
    private function getTokenHelperScript($lines)
    {
        $tokenHelper = '';
        foreach ($lines as $line) {
            preg_match(self::TOKEN_HELPER_REGEX, $line, $matches);
            if (isset($matches[1])) {
                $tokenHelper = trim(trim(trim($matches[1]), '"'));
            }
        }
        return $tokenHelper;
    }

    /**
     * Execute vault token helper script and return the token it contains
     *
     * @param string $cmd
     * @return string
     */
    private function execVaultTokenHelper($cmd)
    {
        $output = '';
        exec($cmd, $out, $status);
        if ($status === 0 && isset($out[0])) {
            $output = $out[0];
        }
        return $output;
    }
}
