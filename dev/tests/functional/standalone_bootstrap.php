<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

//Do not continue running this bootstrap if PHPUnit is calling it
$fullTrace = debug_backtrace();
$rootFile = array_values(array_slice($fullTrace, -1))[0]['file'];
if (strpos($rootFile, "phpunit") !== false) {
    return;
}

defined('PROJECT_ROOT') || define('PROJECT_ROOT', dirname(dirname(dirname(__DIR__))));

require_once realpath(PROJECT_ROOT . '/vendor/autoload.php');

$envFilePath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
defined('ENV_FILE_PATH') || define('ENV_FILE_PATH', $envFilePath);

//Load constants from .env file
if (file_exists(ENV_FILE_PATH . '.env')) {
    $env = new \Symfony\Component\Dotenv\Dotenv();
    if (function_exists('putenv')) {
        $env->usePutenv();
    }
    $env->populate($env->parse(file_get_contents(ENV_FILE_PATH . '.env'), ENV_FILE_PATH . '.env'), true);


    foreach ($_ENV as $key => $var) {
        defined($key) || define($key, $var);
    }

    if (array_key_exists('MAGENTO_BP', $_ENV)) {
        // TODO REMOVE THIS CODE ONCE WE HAVE STOPPED SUPPORTING dev/tests/acceptance PATH
        // define TEST_PATH and TEST_MODULE_PATH
        defined('TESTS_BP') || define('TESTS_BP', dirname(dirname(__DIR__)));
        $RELATIVE_TESTS_MODULE_PATH = '/tests/functional/tests/MFTF';
        defined('TESTS_MODULE_PATH') || define(
            'TESTS_MODULE_PATH',
            realpath(TESTS_BP . $RELATIVE_TESTS_MODULE_PATH)
        );
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

defined('FW_BP') || define('FW_BP', PROJECT_ROOT);
defined('MAGENTO_BP') || define('MAGENTO_BP', PROJECT_ROOT);
defined('TESTS_BP') || define('TESTS_BP', dirname(dirname(__DIR__)));

$RELATIVE_TESTS_MODULE_PATH = '/tests/functional/tests/MFTF';
defined('TESTS_MODULE_PATH') || define('TESTS_MODULE_PATH', realpath(TESTS_BP . $RELATIVE_TESTS_MODULE_PATH));
