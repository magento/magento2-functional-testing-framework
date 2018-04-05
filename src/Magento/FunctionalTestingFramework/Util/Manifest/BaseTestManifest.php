<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Manifest;

use Magento\FunctionalTestingFramework\Test\Objects\TestObject;

abstract class BaseTestManifest
{
    /**
     * Type of manifest to generate. (Currently describes whether to path to a dir or for each test).
     *
     * @var string
     */
    protected $runTypeConfig;

    /**
     * Relative dir path from functional yml file. For devOps execution flexibility.
     *
     * @var string
     */
    protected $relativeDirPath;

    /**
     * TestManifest constructor.
     *
     * @param string $path
     * @param string $runConfig
     */
    public function __construct($path, $runConfig)
    {
        $this->runTypeConfig = $runConfig;
        $this->relativeDirPath = substr($path, strlen(dirname(dirname(TESTS_BP))) + 1);
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
     * Takes a test name and set of tests, records the names in a file for codeception to consume.
     *
     * @param TestObject $testObject
     * @return void
     */
    abstract public function addTest($testObject);

    /**
     * Function which generates the actual manifest(s) once the relevant tests have been added to the array.
     *
     * @param array $testsReferencedInSuites
     * @param int|null $nodes
     * @return void
     */
    abstract public function generate($testsReferencedInSuites, $nodes = null);
}
