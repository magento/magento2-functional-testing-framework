<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

error_reporting(~E_USER_NOTICE);
define('PROJECT_ROOT', dirname(dirname(__DIR__)));
require_once PROJECT_ROOT . '/vendor/autoload.php';

// Set up AspectMock
$kernel = \AspectMock\Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'includePaths' => [PROJECT_ROOT . '/src']
]);

// Load needed framework env params
$TEST_ENVS = [
    'MAGENTO_BASE_URL' => 'http://baseurl:8080',
    'MAGENTO_BACKEND_NAME' => 'admin',
    'MAGENTO_ADMIN_USERNAME' => 'admin',
    'MAGENTO_ADMIN_PASSWORD' => 'admin123'
];

foreach ($TEST_ENVS as $key => $value) {
    $_ENV[$key] = $value;
    putenv("{$key}=${value}");
}

// Add our test module to the whitelist
putenv('MODULE_WHITELIST=Magento_TestModule');

// Define our own set of paths for the tests
defined('FW_BP') || define('FW_BP', PROJECT_ROOT);

$RELATIVE_TESTS_MODULE_PATH = DIRECTORY_SEPARATOR . 'verification';

defined('TESTS_BP') || define('TESTS_BP', __DIR__);
defined('TESTS_MODULE_PATH') || define('TESTS_MODULE_PATH', TESTS_BP . $RELATIVE_TESTS_MODULE_PATH);

$utilDir = DIRECTORY_SEPARATOR . 'Util'. DIRECTORY_SEPARATOR . '*.php';

//Load required util files from functional dir
$functionalUtilFiles = glob(TESTS_BP . DIRECTORY_SEPARATOR . 'verification' . $utilDir);
foreach (sortInterfaces($functionalUtilFiles) as $functionalUtilFile) {
    require($functionalUtilFile);
}

//Load required util files from unit dir
$unitUtilFiles = glob(TESTS_BP . DIRECTORY_SEPARATOR . 'unit' . $utilDir);
foreach (sortInterfaces($unitUtilFiles) as $unitUtilFile) {
    require($unitUtilFile);
}

function sortInterfaces($files)
{
    $bottom = [];
    $top = [];
    foreach ($files as $file) {
        if (strstr(strtolower($file), 'interface')) {
            $top[] = $file;
            continue;
        }

        $bottom[] = $file;
    }

    return array_merge($top, $bottom);
}
