<?php
putenv("HOSTNAME=127.0.0.1");
putenv("PORT=8080");

require_once __DIR__ . '/vendor/autoload.php';


//$dotenv = new Dotenv\Dotenv(TESTS_BP, '.env');
//Load constants from .env file
$env = new \Dotenv\Loader(__DIR__ . '/.env');
$env->load();
define('TESTS_BP', $env->getEnvironmentVariable('TESTS_BP'));
define('FW_BP', $env->getEnvironmentVariable('FW_BP'));
define('TESTS_MODULE_PATH', TESTS_BP . '/tests/acceptance/Magento/AcceptanceTest');

$objectManager = \Magento\AcceptanceTestFramework\ObjectManagerFactory::getObjectManager();
