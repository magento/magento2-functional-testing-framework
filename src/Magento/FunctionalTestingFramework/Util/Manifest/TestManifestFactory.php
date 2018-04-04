<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Manifest;

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
     * @param String $manifestPath
     * @param String $testPath
     * @param String $runConfig
     * @return BaseTestManifest
     */
    public static function makeManifest($manifestPath, $testPath, $runConfig)
    {
        switch ($runConfig) {
            case 'singleRun':
                return new SingleRunTestManifest($manifestPath, $testPath);

            case 'parallel':
                return new ParallelTestManifest($manifestPath, $testPath);

            case 'suite':
                // the suite does not have its own manifest but instead is handled by the other suite types.
                return null;

            default:
                return new DefaultTestManifest($manifestPath, $testPath);

        }
    }
}
