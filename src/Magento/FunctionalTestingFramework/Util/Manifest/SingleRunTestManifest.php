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
     * @param string $manifestPath
     * @param string $testPath
     */
    public function __construct($manifestPath, $testPath)
    {
        parent::__construct($manifestPath, $testPath);
        $this->runTypeConfig = self::SINGLE_RUN_CONFIG;
        $fileResource = fopen($this->manifestPath, 'a');
        fclose($fileResource);
    }

    /**
     * Function which generates the actual manifest once the relevant tests have been added to the array.
     *
     * @param array $testsReferencedInSuites
     * @param int|null $nodes
     * @return void
     */
    public function generate($testsReferencedInSuites, $nodes = null)
    {
        $fileResource = fopen($this->manifestPath, 'a');
        $line = $this->relativeDirPath . DIRECTORY_SEPARATOR;
        fwrite($fileResource, $line . PHP_EOL);
        $this->generateSuiteEntries($testsReferencedInSuites, $fileResource);
        fclose($fileResource);
    }
}
