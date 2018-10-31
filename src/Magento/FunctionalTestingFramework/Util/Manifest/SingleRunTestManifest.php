<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Manifest;

class SingleRunTestManifest extends DefaultTestManifest
{
    const SINGLE_RUN_CONFIG = 'singleRun';

    /**
     * SingleRunTestManifest constructor.
     *
     * @param array  $suiteConfiguration
     * @param string $testPath
     */
    public function __construct($suiteConfiguration, $testPath)
    {
        parent::__construct($suiteConfiguration, $testPath);
        $this->runTypeConfig = self::SINGLE_RUN_CONFIG;
    }

    /**
     * Function which generates the actual manifest once the relevant tests have been added to the array.
     *
     * @return void
     */
    public function generate()
    {
        $fileResource = fopen($this->manifestPath, 'a');
        $line = $this->relativeDirPath . DIRECTORY_SEPARATOR;
        fwrite($fileResource, $line . PHP_EOL);
        $this->generateSuiteEntries($fileResource);
        fclose($fileResource);
    }
}
