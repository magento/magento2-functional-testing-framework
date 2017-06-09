<?php

defined('FW_BP') || define('FW_BP', str_replace('\\', '/', __DIR__));
//defined('TESTS_BP') || define('TESTS_BP', dirname(dirname(dirname(dirname(FW_BP)))));
defined('TESTS_BP') || define('TESTS_BP', FW_BP);
defined('TESTS_MODULE_PATH') || define('TESTS_MODULE_PATH', FW_BP .'/src/Magento/AcceptanceTestFramework/Page');

require_once 'bootstrap.php';

/** @var Magento\AcceptanceTestFramework\Dummy $dummy */
$dummy = $objectManager->create(\Magento\AcceptanceTestFramework\Dummy::class);
$dummy->readPageObjects();
