<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

error_reporting(~E_USER_NOTICE);
define('PROJECT_ROOT', dirname(dirname(__DIR__)));

$vendorAutoloadPath = realpath(PROJECT_ROOT . '/vendor/autoload.php');
$mftfTestCasePath = realpath(PROJECT_ROOT . '/dev/tests/util/MftfTestCase.php');
$mftfStaticTestCasePath = realpath(PROJECT_ROOT . '/dev/tests/util/MftfStaticTestCase.php');

require_once $vendorAutoloadPath;
require_once $mftfTestCasePath;
require_once $mftfStaticTestCasePath;

// set mftf appplication context
\Magento\FunctionalTestingFramework\Config\MftfApplicationConfig::create(
    true,
    \Magento\FunctionalTestingFramework\Config\MftfApplicationConfig::UNIT_TEST_PHASE,
    true,
    \Magento\FunctionalTestingFramework\Config\MftfApplicationConfig::LEVEL_DEFAULT,
    false
);

// Load needed framework env params
$TEST_ENVS = [
    'MAGENTO_BASE_URL' => 'http://baseurl:8080',
    'MAGENTO_BACKEND_NAME' => 'admin',
    'MAGENTO_ADMIN_USERNAME' => 'admin',
    'MAGENTO_ADMIN_PASSWORD' => 'admin123',
    'DEFAULT_TIMEZONE' => 'America/Los_Angeles',
    'WAIT_TIMEOUT' => '10'
];

foreach ($TEST_ENVS as $key => $value) {
    $_ENV[$key] = $value;
    putenv("{$key}=${value}");
}

// Add our test module to the allowlist
putenv('MODULE_ALLOWLIST=Magento_TestModule');

// Define our own set of paths for the tests
defined('FW_BP') || define('FW_BP', PROJECT_ROOT);

$RELATIVE_TESTS_MODULE_PATH = DIRECTORY_SEPARATOR . 'verification';

defined('TESTS_BP') || define('TESTS_BP', __DIR__);
defined('TESTS_MODULE_PATH') || define('TESTS_MODULE_PATH', TESTS_BP . $RELATIVE_TESTS_MODULE_PATH);
defined('MAGENTO_BP') || define('MAGENTO_BP', __DIR__);
define('DOCS_OUTPUT_DIR',
    FW_BP .
    DIRECTORY_SEPARATOR .
    "dev" .
    DIRECTORY_SEPARATOR .
    "tests" .
    DIRECTORY_SEPARATOR .
    "unit" .
    DIRECTORY_SEPARATOR .
    "_output"
);
define('RESOURCE_DIR',
    FW_BP .
    DIRECTORY_SEPARATOR .
    "dev" .
    DIRECTORY_SEPARATOR .
    "tests" .
    DIRECTORY_SEPARATOR .
    "unit" .
    DIRECTORY_SEPARATOR .
    "Resources"
);

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
