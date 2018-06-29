<?php
// @codingStandardsIgnoreFile
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// define framework basepath for schema pathing
defined('FW_BP') || define('FW_BP', realpath(__DIR__ . '/../../../'));

// get the root path of the project (we will always be installed under vendor)
$projectRootPath = substr(FW_BP, 0, strpos(FW_BP, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR));

if (empty($projectRootPath)) {
    // Currently we do not support global execution, so leave this script before pathing is set improperly
    return;
}

// set Magento_BP as Root_Project Path
define('PROJECT_ROOT', $projectRootPath);
defined('MAGENTO_BP') || define('MAGENTO_BP', realpath($projectRootPath));

// load .env (if it exists)
$envFilepath = realpath(MAGENTO_BP . '/dev/tests/acceptance/');
if (file_exists($envFilepath . DIRECTORY_SEPARATOR . '.env')) {
    $env = new \Dotenv\Loader($envFilepath . DIRECTORY_SEPARATOR . '.env');
    $env->load();

    if (array_key_exists('TESTS_MODULE_PATH', $_ENV) xor array_key_exists('TESTS_BP', $_ENV)) {
        throw new Exception(
            'You must define both parameters TESTS_BP and TESTS_MODULE_PATH or neither parameter'
        );
    }

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

// TODO REMOVE THIS CODE ONCE WE HAVE STOPPED SUPPORTING dev/tests/acceptance PATH
// define TEST_PATH and TEST_MODULE_PATH
defined('TESTS_BP') || define('TESTS_BP', realpath(MAGENTO_BP . DIRECTORY_SEPARATOR . 'dev/tests/acceptance/'));

$RELATIVE_TESTS_MODULE_PATH = '/tests/functional/Magento/FunctionalTest';
defined('TESTS_MODULE_PATH') || define(
    'TESTS_MODULE_PATH',
    realpath(TESTS_BP . $RELATIVE_TESTS_MODULE_PATH)
);

// add the debug flag here
$debugMode = $_ENV['MFTF_DEBUG'] ?? false;
if (!(bool)$debugMode && extension_loaded('xdebug')) {
    xdebug_disable();
}
