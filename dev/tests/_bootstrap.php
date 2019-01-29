<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

error_reporting(~E_USER_NOTICE);
define('PROJECT_ROOT', dirname(dirname(__DIR__)));

$vendorAutoloadPath = realpath(PROJECT_ROOT . '/vendor/autoload.php');
$mftfTestCasePath = realpath(PROJECT_ROOT . '/dev/tests/util/MftfTestCase.php');

require_once $vendorAutoloadPath;
require_once $mftfTestCasePath;

// Set up AspectMock
$kernel = \AspectMock\Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'includePaths' => [PROJECT_ROOT . DIRECTORY_SEPARATOR . 'src'],
    'cacheDir' => PROJECT_ROOT .
        DIRECTORY_SEPARATOR .
        'dev' .
        DIRECTORY_SEPARATOR .
        'tests' .
        DIRECTORY_SEPARATOR .
        '.cache'
]);

// set mftf appplication context
\Magento\FunctionalTestingFramework\Config\MftfApplicationConfig::create(
    true,
    \Magento\FunctionalTestingFramework\Config\MftfApplicationConfig::UNIT_TEST_PHASE,
    true,
    false
);

// Load needed framework env params
$TEST_ENVS = [
    'MAGENTO_BASE_URL' => 'http://baseurl:8080',
    'MAGENTO_BACKEND_NAME' => 'admin',
    'MAGENTO_ADMIN_USERNAME' => 'admin',
    'MAGENTO_ADMIN_PASSWORD' => 'admin123',
    'DEFAULT_TIMEZONE' => 'America/Los_Angeles'
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
defined('MAGENTO_BP') || define('MAGENTO_BP', __DIR__);

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


// Mocks suite files location getter return to get files in verification/_suite Directory
// This mocks the paths of the suite files but still parses the xml files
$suiteDirectory =  TESTS_BP . DIRECTORY_SEPARATOR . "verification" . DIRECTORY_SEPARATOR . "_suite";

$paths = [
    $suiteDirectory . DIRECTORY_SEPARATOR . 'functionalSuite.xml',
    $suiteDirectory . DIRECTORY_SEPARATOR . 'functionalSuiteHooks.xml',
    $suiteDirectory . DIRECTORY_SEPARATOR . 'functionalSuiteExtends.xml'
];

// create and return the iterator for these file paths
$iterator = new Magento\FunctionalTestingFramework\Util\Iterator\File($paths);
try {
    AspectMock\Test::double(
        Magento\FunctionalTestingFramework\Config\FileResolver\Root::class,
        ['get' => $iterator]
    )->make();
} catch (Exception $e) {
    echo "Suite directory not mocked.";
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
