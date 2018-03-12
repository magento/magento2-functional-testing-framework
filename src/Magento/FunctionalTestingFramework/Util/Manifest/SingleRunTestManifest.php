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
     * @param string $path
     */
    public function __construct($path)
    {
        $this->manifestPath = $path . DIRECTORY_SEPARATOR . 'testManifest.txt';
        parent::__construct($path, self::SINGLE_RUN_CONFIG);

        $fileResource = fopen($this->manifestPath, 'w');
        fclose($fileResource);
    }

    /**
     * Function which generates the actual manifest once the relevant tests have been added to the array.
     *
     * @param int|null $nodes
     * @return void
     */
    public function generate($nodes = null)
    {
        $fileResource = fopen($this->manifestPath, 'a');
        $line = $this->relativeDirPath . DIRECTORY_SEPARATOR;
        fwrite($fileResource, $line . PHP_EOL);
        fclose($fileResource);
    }
}
