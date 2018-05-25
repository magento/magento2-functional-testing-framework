<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define('PROJECT_ROOT', dirname(dirname(dirname(__DIR__))));
require_once realpath(PROJECT_ROOT . '/vendor/autoload.php');

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
}
