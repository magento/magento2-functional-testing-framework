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
use Vault\Client;
use VaultTransports\Guzzle6Transport;

class VaultStorage extends BaseStorage
{
    /**
     * Mftf project path
     */
    const MFTF_PATH = '/mftf';

    /**
     * Vault kv version 2 data
     */
    const KV2_DATA = 'data';

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

    /**
     * Regex to grab token helper script
     */
    const TOKEN_HELPER_REGEX_GROUP_NAME = 'GROUP_NAME';
    const TOKEN_HELPER_REGEX = "~\s*token_helper\s*=(?<" . self::TOKEN_HELPER_REGEX_GROUP_NAME . ">.+)$~";

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
     * Vault secret base path
     *
     * @var string
     */
    private $secretBasePath;

    /**
     * VaultStorage constructor
     *
     * @param string $baseUrl
     * @param string $secretBasePath
     * @throws TestFrameworkException
     */
    public function __construct($baseUrl, $secretBasePath)
    {
        parent::__construct();
        if (null === $this->client) {
            // Creating the client using Guzzle6 Transport and passing a custom url
            $this->client = new Client(new Guzzle6Transport(['base_uri' => $baseUrl]));
            $this->secretBasePath = $secretBasePath;
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
            $url = $this->secretBasePath
                . (empty(self::KV2_DATA) ? '' : '/' . self::KV2_DATA)
                . self::MFTF_PATH
                . '/'
                . $vendor
                . '/'
                . $key;
            // Read value by key from vault
            $value = $this->client->read($url)->getData()[self::KV2_DATA][$key];
            // Encrypt value for return
            $reValue = openssl_encrypt($value, parent::ENCRYPTION_ALGO, parent::$encodedKey, 0, parent::$iv);
            parent::$cachedSecretData[$key] = $reValue;
        } catch (\Exception $e) {
            $errMessage = "\nUnable to read secret for key name {$key} from vault." . $e->getMessage();
            // Print error message in console
            print_r($errMessage);
            // Save to exception context for Allure report
            CredentialStore::getInstance()->setExceptionContexts('vault', $errMessage);
            // Add error message in mftf log if verbose is enable
            if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
                LoggingUtil::getInstance()->getLogger(VaultStorage::class)->debug($errMessage);
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
            // Print error message in console
            print_r($e->getMessage());
            // Save to exception context for Allure report
            CredentialStore::getInstance()->setExceptionContexts(
                CredentialStore::ARRAY_KEY_FOR_VAULT,
                $e->getMessage()
            );
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
            throw new TestFrameworkException(
                "HOME environment variable is not set. It's required when using vault."
            );
        }
        $homeDir = realpath($homeDir) . DIRECTORY_SEPARATOR;

        // Read .vault-token file if it is found in default location
        $vaultTokenFile = $homeDir . self::TOKEN_FILE;
        if (file_exists($vaultTokenFile)) {
            $token = file_get_contents($vaultTokenFile);
            if ($token !== false) {
                $this->token = $token;
                return;
            }
        }

        // Otherwise search vault config file for custom token helper script
        $vaultConfigPath = getenv(self::CONFIG_PATH_ENV_VAR);
        if ($vaultConfigPath === false) {
            $vaultConfigFile = $homeDir . self::CONFIG_FILE;
        } else {
            $vaultConfigFile = realpath($vaultConfigPath) . DIRECTORY_SEPARATOR . self::CONFIG_FILE;
        }
        // Get custom token helper script file from .vault config file
        if (file_exists($vaultConfigFile)) {
            $cmd = $this->getTokenHelperScript(file($vaultConfigFile, FILE_IGNORE_NEW_LINES));
            if (!empty($cmd)) {
                $this->token = $this->execVaultTokenHelper($cmd . ' get');
                return;
            }
        }
        throw new TestFrameworkException(
            'Unable to read .vault-token file. Please authenticate to vault through vault CLI first.'
        );
    }

    /**
     * Get vault token helper script by parsing lines in vault config file
     *
     * @param array $lines
     * @return string
     */
    private function getTokenHelperScript($lines)
    {
        $tokenHelper = '';
        foreach ($lines as $line) {
            preg_match(self::TOKEN_HELPER_REGEX, $line, $matches);
            if (isset($matches[self::TOKEN_HELPER_REGEX_GROUP_NAME])) {
                $tokenHelper = trim(trim(trim($matches[self::TOKEN_HELPER_REGEX_GROUP_NAME]), '"'));
            }
        }
        return $tokenHelper;
    }

    /**
     * Execute vault token helper script and return the token it contains
     *
     * @param string $cmd
     * @return string
     * @throws TestFrameworkException
     */
    private function execVaultTokenHelper($cmd)
    {
        exec($cmd, $out, $status);
        if ($status === 0 && isset($out[0]) && !empty($out[0])) {
            return $out[0];
        }
        throw new TestFrameworkException(
            'Error running custom vault token helper script. Please make sure vault CLI works in your environment.'
        );
    }
}
