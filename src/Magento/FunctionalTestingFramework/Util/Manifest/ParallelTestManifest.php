<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Manifest;

use Codeception\Suite;
use Magento\Framework\Exception\RuntimeException;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;
use Magento\FunctionalTestingFramework\Util\Sorter\ParallelGroupSorter;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class ParallelTestManifest extends BaseTestManifest
{
    const PARALLEL_CONFIG = 'parallel';

    /**
     * An associate array of test name to size of test.
     *
     * @var string[]
     */
    private $testNameToSize = [];

    /**
     * Class variable to store resulting group config.
     *
     * @var array
     */
    private $testGroups;

    /**
     * An instance of the group sorter which will take suites and tests organizing them to be run together.
     *
     * @var ParallelGroupSorter
     */
    private $parallelGroupSorter;

    /**
     * Path to the directory that will contain all test group files
     *
     * @var string
     */
    private $dirPath;

    /**
     * TestManifest constructor.
     *
     * @param array  $suiteConfiguration
     * @param string $testPath
     */
    public function __construct($suiteConfiguration, $testPath)
    {
        $this->dirPath = dirname($testPath) . DIRECTORY_SEPARATOR . 'groups';
        $this->parallelGroupSorter = new ParallelGroupSorter();
        parent::__construct($testPath, self::PARALLEL_CONFIG, $suiteConfiguration);
    }

    /**
     * Takes a test name and set of tests, records the names in a file for codeception to consume.
     *
     * @param TestObject $testObject
     * @return void
     */
    public function addTest($testObject)
    {
        $this->testNameToSize[$testObject->getCodeceptionName()] = $testObject->getEstimatedDuration();
    }

    /**
     * Function which generates test groups based on arg passed. The function builds groups using the args as an upper
     * limit.
     *
     * @param integer $time
     * @return void
     */
    public function createTestGroups($time)
    {
        $this->testGroups = $this->parallelGroupSorter->getTestsGroupedBySize(
            $this->getSuiteConfig(),
            $this->testNameToSize,
            $time
        );

        $this->suiteConfiguration = $this->parallelGroupSorter->getResultingSuiteConfig();
    }

    /**
     * Function which generates the actual manifest once the relevant tests have been added to the array.
     *
     * @return void
     */
    public function generate()
    {
        DirSetupUtil::createGroupDir($this->dirPath);
        $suites = $this->getFlattenedSuiteConfiguration($this->suiteConfiguration ?? []);

        foreach ($this->testGroups as $groupNumber => $groupContents) {
            $this->generateGroupFile($groupContents, $groupNumber, $suites);
        }
    }

    /**
     * Function which simply returns the private sorter used by the manifest.
     *
     * @return ParallelGroupSorter
     */
    public function getSorter()
    {
        return $this->parallelGroupSorter;
    }

    /**
     * Function which takes an array containing entries representing the test execution as well as the associated group
     * for the entry in order to generate a txt file used by devops for parllel execution in Jenkins. The results
     * are checked against a flattened list of suites in order to generate proper entries.
     *
     * @param array   $testGroup
     * @param integer $nodeNumber
     * @param array   $suites
     * @return void
     */
    private function generateGroupFile($testGroup, $nodeNumber, $suites)
    {
        foreach ($testGroup as $entryName => $testValue) {
            $fileResource = fopen($this->dirPath . DIRECTORY_SEPARATOR . "group{$nodeNumber}.txt", 'a');

            $line = null;
            if (array_key_exists($entryName, $suites)) {
                $line = "-g {$entryName}";
            } else {
                $line = $this->relativeDirPath . DIRECTORY_SEPARATOR . $entryName . '.php';
            }
            fwrite($fileResource, $line . PHP_EOL);
            fclose($fileResource);
        }
    }

    /**
     * Function which recusrively parses a given potentially multidimensional array of suites containing their split
     * groups. The result is a flattened array of suite names to relevant tests for generation of the manifest.
     *
     * @param array $multiDimensionalSuites
     * @return array
     */
    private function getFlattenedSuiteConfiguration($multiDimensionalSuites)
    {
        $suites = [];
        foreach ($multiDimensionalSuites as $suiteName => $suiteContent) {
            $value = array_values($suiteContent)[0];
            if (is_array($value)) {
                $suites = array_merge($suites, $this->getFlattenedSuiteConfiguration($suiteContent));
                continue;
            }

            $suites[$suiteName] = $suiteContent;
        }

        return $suites;
    }
}
