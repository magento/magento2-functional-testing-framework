<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Manifest;

use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;

class TestManifestFactory
{
    /**
     * TestManifestFactory constructor.
     */
    private function __construct()
    {
        // private constructor
    }

    /**
     * Static function which takes path and config to return the appropriate manifest output type.
     *
     * @param string $runConfig
     * @param array  $suiteConfiguration
     * @param string $testPath
     * @return BaseTestManifest
     */
    public static function makeManifest($runConfig, $suiteConfiguration, $testPath = TestGenerator::DEFAULT_DIR)
    {
        $testDirFullPath = TESTS_MODULE_PATH
        . DIRECTORY_SEPARATOR
        . TestGenerator::GENERATED_DIR
        . DIRECTORY_SEPARATOR
        . $testPath;

        switch ($runConfig) {
            case 'singleRun':
                return new SingleRunTestManifest($suiteConfiguration, $testDirFullPath);

            case 'parallel':
                return new ParallelTestManifest($suiteConfiguration, $testDirFullPath);

            default:
                return new DefaultTestManifest($suiteConfiguration, $testDirFullPath);
        }
    }
}
