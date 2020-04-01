<?php
/**
 * Application entry point
 *
 * Example - run a particular store or website:
 * --------------------------------------------
 * require __DIR__ . '/app/bootstrap.php';
 * $params = $_SERVER;
 * $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'website2';
 * $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'website';
 * $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
 * \/** @var \Magento\Framework\App\Http $app *\/
 * $app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
 * $bootstrap->run($app);
 * --------------------------------------------
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

try {
    require __DIR__ . '/app/bootstrap.php';
} catch (\Exception $e) {
    echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Autoload error</h3>
    </div>
    <p>{$e->getMessage()}</p>
</div>
HTML;
    exit(1);
}

//Patch start
$driver = new pcov\Clobber\Driver\PHPUnit6();
$coverage = new \SebastianBergmann\CodeCoverage\CodeCoverage($driver);
$coverage->filter()->addDirectoryToWhitelist("app/code/Magento/*");
$coverage->filter()->removeDirectoryFromWhitelist("app/code/Magento/*/Test");
$testName = "NO_TEST_NAME";
if (file_exists(__DIR__ . '/CURRENT_TEST')) {
    $testName = file_get_contents(__DIR__ . '/CURRENT_TEST');
}
$id = !empty($testName) ? $testName : "NO_TEST_NAME";
$coverage->start($id);
//Patch end

$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
$bootstrap->run($app);

// Patch start
$coverage->stop();
$writer = new \SebastianBergmann\CodeCoverage\Report\PHP();
$writer->process($coverage, '/var/www/html/coverage/reports/' . $id . "_" . md5(mt_rand()) . '.cov');
// Patch end
