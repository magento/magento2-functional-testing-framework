<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Console\BuildProjectCommand;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

class CredentialStore
{
    /**
     * Singletone instnace
     *
     * @var CredentialStore
     */
    private static $INSTANCE = null;

    /**
     * Key/Value paris of credential names and their corresponding values
     *
     * @var array
     */
    private $credentials = [];

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
        $this->readInCredentialsFile();
    }

    /**
     * Returns the value of a secret based on corresponding key
     *
     * @param string $key
     * @return string|null
     * @throws TestFrameworkException
     */
    public function getSecret($key)
    {
        if (!array_key_exists($key, $this->credentials)) {
            throw new TestFrameworkException(
                "{$key} not defined in .credentials, please provide a value in order to use this secret in a test."
            );
        }

        // log here for verbose config
        if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
            LoggingUtil::getInstance()->getLogger(CredentialStore::class)->debug(
                "retrieving secret for key name {$key}"
            );
        }

        return $this->credentials[$key] ?? null;
    }

    /**
     * Private function which reads in secret key/values from .credentials file and stores in memory as key/value pair.
     *
     * @return void
     * @throws TestFrameworkException
     */
    private function readInCredentialsFile()
    {
        $credsFilePath = str_replace(
            '.credentials.example',
            '.credentials',
            BuildProjectCommand::CREDENTIALS_FILE_PATH
        );

        if (!file_exists($credsFilePath)) {
            throw new TestFrameworkException(
                "Cannot find .credentials file, please create in "
                . TESTS_BP . " in order to reference sensitive information"
            );
        }

        $credContents = file($credsFilePath, FILE_IGNORE_NEW_LINES);
        foreach ($credContents as $credValue) {
            if (substr($credValue, 0, 1) === '#' || empty($credValue)) {
                continue;
            }

            list($key, $value) = explode("=", $credValue);
            if (!empty($value)) {
                $this->credentials[$key] = $value;
            }
        }
    }
}
