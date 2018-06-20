<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Util\Env;

/**
 * Helper class EnvProcessor for reading and writing .env files.
 *
 * @package Magento\FunctionalTestingFramework\Util\Env
 */
class EnvProcessor
{
    /**
     * File .env location.
     *
     * @var string
     */
    private $envFile = '';

    /**
     * File .env.example location.
     *
     * @var string
     */
    private $envExampleFile = '';

    /**
     * Array of environment variables form file.
     *
     * @var array
     */
    private $env = [];

    /**
     * Boolean indicating existence of env file
     *
     * @var boolean
     */
    private $envExists;

    /**
     * EnvProcessor constructor.
     * @param string $envFile
     */
    public function __construct(
        string $envFile = ''
    ) {
        $this->envFile = $envFile;
        $this->envExists = file_exists($envFile);
        $this->envExampleFile = realpath(FW_BP . "/etc/config/.env.example");
    }

    /**
     * Serves for parsing '.env' file into associative array.
     *
     * @return array
     */
    private function parseEnvFile(): array
    {
        $envExampleFile = file(
            $this->envExampleFile,
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );

        $envContents = [];
        if ($this->envExists) {
            $envFile = file(
                $this->envFile,
                FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
            );

            $envContents = $this->parseEnvFileLines($envFile);
        }

        return array_merge($this->parseEnvFileLines($envExampleFile), $envContents);
    }

    /**
     * Iterates through env and returns array of file contents.
     * @param array $file
     * @return array
     */
    private function parseEnvFileLines(array $file): array
    {
        $fileArray = [];
        foreach ($file as $line) {
            // do not use commented out lines
            if (strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line);
                $fileArray[$key] = $value;
            }
        }
        return $fileArray;
    }

    /**
     * Serves for putting array with environment variables into .env file or appending new variables we introduce
     *
     * @param array $config
     * @return void
     */
    public function putEnvFile(array $config = [])
    {
        $envData = '';
        foreach ($config as $key => $value) {
            $envData .= $key . '=' . $value . PHP_EOL;
        }

        file_put_contents($this->envFile, $envData);
    }

    /**
     * Retrieves '.env.example' file as associative array.
     *
     * @return array
     */
    public function getEnv(): array
    {
        if (empty($this->env)) {
            $this->env = $this->parseEnvFile();
        }
        return $this->env;
    }
}
