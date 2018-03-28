<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Manifest;

use Magento\FunctionalTestingFramework\Test\Objects\TestObject;

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
     * An array containing all test names for output.
     *
     * @var string[]
     */
    protected $testNames = [];

    /**
     * DefaultTestManifest constructor.
     * @param string $manifestPath
     * @param string $testPath
     */
    public function __construct($manifestPath, $testPath)
    {
        $this->manifestPath = $manifestPath . DIRECTORY_SEPARATOR . 'testManifest.txt';
        parent::__construct($testPath, self::DEFAULT_CONFIG);
        $fileResource = fopen($this->manifestPath, 'w');
        fclose($fileResource);
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
     * @param int|null $nodes
     * @return void
     */
    public function generate($nodes = null)
    {
        $fileResource = fopen($this->manifestPath, 'a');

        foreach ($this->testNames as $testName) {
            $line = $this->relativeDirPath . DIRECTORY_SEPARATOR . $testName . '.php';
            fwrite($fileResource, $line . PHP_EOL);
        }

        fclose($fileResource);
    }
}
