<?php

require_once __DIR__ . '/vendor/autoload.php';

//Load constants from .env file
$env = new \Dotenv\Loader(__DIR__ . '/.env');
$env->load();

foreach ($_ENV as $key => $var) {
    defined($key) || define($key, $var);
}

defined('FW_BP') || define('FW_BP', str_replace('\\', '/', (__DIR__)));
defined('TESTS_BP') || define('TESTS_BP', dirname(dirname(dirname(FW_BP))));
define('TESTS_MODULE_PATH', TESTS_BP . '/tests/acceptance/Magento/AcceptanceTest');

$objectManager = \Magento\AcceptanceTestFramework\ObjectManagerFactory::getObjectManager();
