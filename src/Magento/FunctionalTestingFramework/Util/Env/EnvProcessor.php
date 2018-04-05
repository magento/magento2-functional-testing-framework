<?php
// @codingStandardsIgnoreFile
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
     * EnvProcessor constructor.
     * @param string $envFile
     */
    public function __construct(
        string $envFile = ''
    ) {
        $this->envFile = $envFile;
        $this->envExampleFile = $envFile . '.example';
    }

    /**
     * Serves for parsing '.env.example' file into associative array.
     *
     * @return array
     */
    public function parseEnvFile(): array
    {
        $envLines = file(
            $this->envExampleFile,
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );
        $env = [];
        foreach ($envLines as $line) {
            // do not use commented out lines
            if (strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line);
                $env[$key] = $value;
            }
        }
        return $env;
    }

    /**
     * Serves for putting array with environment variables into .env file.
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
