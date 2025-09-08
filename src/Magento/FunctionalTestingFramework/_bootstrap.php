<?php
// @codingStandardsIgnoreFile
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */


// define framework basepath for schema pathing
use Symfony\Component\Dotenv\Exception\PathException;

defined('FW_BP') || define('FW_BP', realpath(__DIR__ . '/../../../'));
// get the root path of the project
$projectRootPath = substr(FW_BP, 0, strpos(FW_BP, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR));
if (empty($projectRootPath)) {
    // If ProjectRootPath is empty, we are not under vendor and are executing standalone.
    require_once (realpath(FW_BP . "/dev/tests/functional/standalone_bootstrap.php"));
    return;
}
defined('PROJECT_ROOT') || define('PROJECT_ROOT', $projectRootPath);

$envFilePath = realpath($projectRootPath . '/dev/tests/acceptance/') . DIRECTORY_SEPARATOR;
defined('ENV_FILE_PATH') || define('ENV_FILE_PATH', $envFilePath);

//Load constants from .env file
if (file_exists(ENV_FILE_PATH . '.env')) {
    $env = new \Symfony\Component\Dotenv\Dotenv();
    if (function_exists('putenv')) {
        $env->usePutenv();
    }
    $env->populate($env->parse(file_get_contents(ENV_FILE_PATH . '.env'), ENV_FILE_PATH . '.env'), true);

    if (array_key_exists('TESTS_MODULE_PATH', $_ENV) xor array_key_exists('TESTS_BP', $_ENV)) {
        throw new Exception(
            'You must define both parameters TESTS_BP and TESTS_MODULE_PATH or neither parameter'
        );
    }

    foreach ($_ENV as $key => $var) {
        defined($key) || define($key, $var);
    }

    if (array_key_exists('MAGENTO_BP', $_ENV)) {
        defined('TESTS_BP') || define('TESTS_BP', realpath(PROJECT_ROOT . DIRECTORY_SEPARATOR . 'dev/tests/acceptance'));
    }

    defined('MAGENTO_CLI_COMMAND_PATH') || define(
        'MAGENTO_CLI_COMMAND_PATH',
        'dev/tests/acceptance/utils/command.php'
    );
    defined('MAGENTO_CLI_COMMAND_PARAMETER') || define('MAGENTO_CLI_COMMAND_PARAMETER', 'command');
    defined('DEFAULT_TIMEZONE') || define('DEFAULT_TIMEZONE', 'America/Los_Angeles');
    defined('WAIT_TIMEOUT') || define('WAIT_TIMEOUT', 30);
    defined('VERBOSE_ARTIFACTS') || define('VERBOSE_ARTIFACTS', false);
    $env->populate(
        [
            'MAGENTO_CLI_COMMAND_PATH' => MAGENTO_CLI_COMMAND_PATH,
            'MAGENTO_CLI_COMMAND_PARAMETER' => MAGENTO_CLI_COMMAND_PARAMETER,
            'DEFAULT_TIMEZONE' => DEFAULT_TIMEZONE,
            'WAIT_TIMEOUT' => WAIT_TIMEOUT,
            'VERBOSE_ARTIFACTS' => VERBOSE_ARTIFACTS,
        ],
        true
    );

    try {
        new DateTimeZone(DEFAULT_TIMEZONE);
    } catch (\Exception $e) {
        throw new \Exception("Invalid DEFAULT_TIMEZONE in .env: " . DEFAULT_TIMEZONE . PHP_EOL);
    }
}


defined('MAGENTO_BP') || define('MAGENTO_BP', realpath(PROJECT_ROOT));
// TODO REMOVE THIS CODE ONCE WE HAVE STOPPED SUPPORTING dev/tests/acceptance PATH
// define TEST_PATH and TEST_MODULE_PATH
defined('TESTS_BP') || define('TESTS_BP', realpath(MAGENTO_BP . DIRECTORY_SEPARATOR . 'dev/tests/acceptance'));

$RELATIVE_TESTS_MODULE_PATH = '/tests/functional/Magento';
defined('TESTS_MODULE_PATH') || define(
    'TESTS_MODULE_PATH',
    realpath(TESTS_BP . $RELATIVE_TESTS_MODULE_PATH)
);
