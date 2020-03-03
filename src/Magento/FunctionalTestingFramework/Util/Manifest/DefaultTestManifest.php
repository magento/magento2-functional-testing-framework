<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Manifest;

use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;

class DefaultTestManifest extends BaseTestManifest
{
    const DEFAULT_CONFIG = 'default';

    /**
     * Path to the test manifest file.
     *
     * @var string
     */
    protected $manifestPath;

    /**
     * A static array to track which test manifests have been cleared to prevent overwriting during generation.
     *
     * @var array
     */
    private static $CLEARED_MANIFESTS = [];

    /**
     * An array containing all test names for output.
     *
     * @var string[]
     */
    protected $testNames = [];

    /**
     * DefaultTestManifest constructor.
     *
     * @param array  $suiteConfiguration
     * @param string $testPath
     */
    public function __construct($suiteConfiguration, $testPath)
    {
        $this->manifestPath = dirname($testPath) . DIRECTORY_SEPARATOR . 'testManifest.txt';
        $this->cleanManifest($this->manifestPath);
        parent::__construct($testPath, self::DEFAULT_CONFIG, $suiteConfiguration);
    }

    /**
     * Takes a test name and set of tests, records the names in a file for codeception to consume.
     *
     * @param TestObject $testObject
     * @return void
     */
    public function addTest($testObject)
    {
        $this->testNames[] = $testObject->getCodeceptionName();
    }

    /**
     * Function which outputs a list of all test files to the defined testManifest.txt file.
     *
     * @return void
     */
    public function generate()
    {
        $fileResource = fopen($this->manifestPath, 'a');

        foreach ($this->testNames as $testName) {
            $line = $this->relativeDirPath . DIRECTORY_SEPARATOR . $testName . '.php';
            fwrite($fileResource, $line . PHP_EOL);
        }

        $this->generateSuiteEntries($fileResource);

        fclose($fileResource);
    }

    /**
     * Function which takes the test suites passed to the manifest and generates corresponding entries in the manifest.
     *
     * @param resource $fileResource
     * @return void
     */
    protected function generateSuiteEntries($fileResource)
    {
        foreach ($this->getSuiteConfig() as $suiteName => $tests) {
            if (count($tests) === 0) {
                continue;
            }
            $line = "-g {$suiteName}";
            fwrite($fileResource, $line . PHP_EOL);
        }
    }

    /**
     * Function which checks the path for an existing test manifest and clears if the file has not already been cleared
     * during current runtime.
     *
     * @param string $path
     * @return void
     */
    private function cleanManifest($path)
    {
        // if we have already cleared the file then simply return
        if (in_array($path, self::$CLEARED_MANIFESTS)) {
            return;
        }

        // if the file exists remove
        if (file_exists($path)) {
            unlink($path);
        }

        self::$CLEARED_MANIFESTS[] = $path;
    }
}
