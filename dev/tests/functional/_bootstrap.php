<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

defined('PROJECT_ROOT') || define('PROJECT_ROOT', dirname(dirname(dirname(__DIR__))));
require_once realpath(PROJECT_ROOT . '/vendor/autoload.php');

//Do not continue running this bootstrap if PHPUnit is calling it
$fullTrace = debug_backtrace();
$rootFile = array_values(array_slice($fullTrace, -1))[0]['file'];
if (strpos($rootFile, "phpunit") !== false) {
    return;
}

//Load constants from .env file
defined('FW_BP') || define('FW_BP', PROJECT_ROOT);

// add the debug flag here
$debug_mode = $_ENV['MFTF_DEBUG'] ?? false;
if (!(bool)$debug_mode && extension_loaded('xdebug')) {
    xdebug_disable();
}

$RELATIVE_TESTS_MODULE_PATH = '/tests/functional/tests/MFTF';

defined('MAGENTO_BP') || define('MAGENTO_BP', PROJECT_ROOT);
defined('TESTS_BP') || define('TESTS_BP', dirname(dirname(__DIR__)));
defined('TESTS_MODULE_PATH') || define('TESTS_MODULE_PATH', realpath(TESTS_BP . $RELATIVE_TESTS_MODULE_PATH));

if (file_exists(TESTS_BP . DIRECTORY_SEPARATOR . '.env')) {
    $env = new \Dotenv\Loader(TESTS_BP . DIRECTORY_SEPARATOR . '.env');
    $env->load();

    foreach ($_ENV as $key => $var) {
        defined($key) || define($key, $var);
    }

    defined('MAGENTO_CLI_COMMAND_PATH') || define(
        'MAGENTO_CLI_COMMAND_PATH',
        'dev/tests/acceptance/utils/command.php'
    );
    $env->setEnvironmentVariable('MAGENTO_CLI_COMMAND_PATH', MAGENTO_CLI_COMMAND_PATH);

    defined('MAGENTO_CLI_COMMAND_PARAMETER') || define('MAGENTO_CLI_COMMAND_PARAMETER', 'command');
    $env->setEnvironmentVariable('MAGENTO_CLI_COMMAND_PARAMETER', MAGENTO_CLI_COMMAND_PARAMETER);
}
