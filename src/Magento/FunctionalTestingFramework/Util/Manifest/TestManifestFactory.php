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
     * @param String $path
     * @param String $runConfig
     * @return BaseTestManifest
     */
    public static function makeManifest($path, $runConfig)
    {
        switch ($runConfig) {
            case 'singleRun':
                return new SingleRunTestManifest($path);

            case 'parallel':
                return new ParallelTestManifest($path);

            default:
                return new DefaultTestManifest($path);

        }
    }
}
