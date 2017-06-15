<?php

defined('FW_BP') || define('FW_BP', str_replace('\\', '/', (__DIR__)));
defined('TESTS_BP') || define('TESTS_BP', dirname(dirname(dirname(FW_BP))));

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(TESTS_BP, '.env');
$dotenv->load();

$objectManager = \Magento\AcceptanceTestFramework\ObjectManagerFactory::getObjectManager();
