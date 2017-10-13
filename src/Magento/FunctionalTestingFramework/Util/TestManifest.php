<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

use Magento\FunctionalTestingFramework\Test\Objects\TestObject;

class TestManifest
{
    const SINGLE_RUN_CONFIG = 'singleRun';
    const DEFAULT_BROWSER = 'chrome';

    /**
     * Test Manifest file path.
     *
     * @var string
     */
    private $filePath;

    /**
     * Test Manifest environment flag. This is added to each dir or file in order for tests to execute properly.
     *
     * @var string $environment
     */
    private $environment = self::DEFAULT_BROWSER;

    /**
     * Type of manifest to generate. (Currently describes whether to path to a dir or for each test).
     *
     * @var string
     */
    private $runTypeConfig;

    /**
     * Relative dir path from functional yml file. For devOps execution flexibility.
     *
     * @var string
     */
    private $relativeDirPath;

    /**
     * TestManifest constructor.
     *
     * @param string $path
     * @param string $runConfig
     * @param string $env
     */
    public function __construct($path, $runConfig, $env)
    {
        $this->relativeDirPath = substr($path, strlen(dirname(dirname(TESTS_BP))) + 1);
        $filePath = $path .  DIRECTORY_SEPARATOR . 'testManifest.txt';
        $this->filePath = $filePath;
        $fileResource = fopen($filePath, 'w');
        fclose($fileResource);

        $this->runTypeConfig = $runConfig;

        if ($env) {
            $this->environment = $env;
        }
    }

    /**
     * Returns a string indicating the generation config (e.g. singleRun).
     *
     * @return string
     */
    public function getManifestConfig()
    {
        return $this->runTypeConfig;
    }

    /**
     * Takes a cest name and set of tests, records the names in a file for codeception to consume.
     *
     * @param string $cestName
     * @param TestObject $tests
     * @return void
     */
    public function recordCest($cestName, $tests)
    {
        $fileResource = fopen($this->filePath, 'a');

        foreach ($tests as $test) {
            $line = $this->relativeDirPath . DIRECTORY_SEPARATOR . $cestName . '.php:' . $test->getName();
            fwrite($fileResource, $this->appendDefaultBrowser($line) ."\n");
        }

        fclose($fileResource);
    }

    /**
     * Function which simple prints the export dir as part of the manifest file rather than an itemized list of
     * cestFile:testname.
     *
     * @return void
     */
    public function recordPathToExportDir()
    {
        $fileResource = fopen($this->filePath, 'a');

        $line = $this->relativeDirPath . DIRECTORY_SEPARATOR;
        fwrite($fileResource, $this->appendDefaultBrowser($line) ."\n");

        fclose($fileResource);
    }

    /**
     * Function which appends the --env flag to the test. This is needed to properly execute all tests in codeception.
     *
     * @param string $line
     * @return string
     */
    private function appendDefaultBrowser($line)
    {
        return "${line} --env " . $this->environment;
    }
}
