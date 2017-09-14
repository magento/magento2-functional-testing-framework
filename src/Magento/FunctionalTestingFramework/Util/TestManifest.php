<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

use Magento\FunctionalTestingFramework\Test\Objects\TestObject;

class TestManifest
{
    /**
     * Test Manifest file path.
     *
     * @var string
     */
    private $filePath;

    /**
     * TestManifest constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $filePath = $path .  DIRECTORY_SEPARATOR . 'testManifest.txt';
        $this->filePath = $filePath;
        $fileResource = fopen($filePath, 'w');
        fclose($fileResource);
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

        foreach ($tests as $test)
        {
            $line = $cestName . ':' . $test->getName();
            fwrite($fileResource, $line ."\n");
        }

        fclose($fileResource);
    }
}