<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Manifest;

use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
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
     * Suite configuration in the format suite name to test name. Overwritten during a custom configuration.
     *
     * @var array
     */
    protected $suiteConfiguration;

    /**
     * TestManifest constructor.
     *
     * @param string $path
     * @param string $runConfig
     * @param array  $suiteConfiguration
     */
    public function __construct($path, $runConfig, $suiteConfiguration)
    {
        $this->runTypeConfig = $runConfig;
        $relativeDirPath = substr($path, strlen(TESTS_BP));
        $this->relativeDirPath = ltrim($relativeDirPath, DIRECTORY_SEPARATOR);
        $this->suiteConfiguration = $suiteConfiguration;
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
     * @return void
     */
    abstract public function generate();

    /**
     * Getter for the suite configuration.
     *
     * @return array
     */
    public function getSuiteConfig()
    {
        if ($this->suiteConfiguration === null) {
            return [];
        }

        $suiteToTestNames = [];
        if (empty($this->suiteConfiguration)) {
            // if there is no configuration passed we can assume the user wants all suites generated as specified.
            foreach (SuiteObjectHandler::getInstance()->getAllObjects() as $suite => $suiteObj) {
                /** @var SuiteObject $suitObj */
                $suiteToTestNames[$suite] = array_keys($suiteObj->getTests());
            }
        } else {
            // we need to loop through the configuration to make sure we capture suites with no specific config
            foreach ($this->suiteConfiguration as $suiteName => $test) {
                if (empty($test)) {
                    $suiteToTestNames[$suiteName] =
                        array_keys(SuiteObjectHandler::getInstance()->getObject($suiteName)->getTests());
                    continue;
                }

                $suiteToTestNames[$suiteName] = $test;
            }
        }

        return $suiteToTestNames;
    }
}
