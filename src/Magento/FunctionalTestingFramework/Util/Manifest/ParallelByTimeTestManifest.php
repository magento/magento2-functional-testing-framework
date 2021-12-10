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

class ParallelByTimeTestManifest extends BaseParallelTestManifest
{
    const PARALLEL_CONFIG = 'parallelByTime';

    /**
     * GroupBasedParallelTestManifest constructor.
     *
     * @param array  $suiteConfiguration
     * @param string $testPath
     */
    public function __construct($suiteConfiguration, $testPath)
    {
        parent::__construct($suiteConfiguration, self::PARALLEL_CONFIG, $testPath);
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
}
