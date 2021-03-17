<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Manifest;

use Magento\Framework\Exception\RuntimeException;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;
use Magento\FunctionalTestingFramework\Util\Sorter\ParallelGroupSorter;

class ParallelByGroupTestManifest extends BaseParallelTestManifest
{
    const PARALLEL_CONFIG = 'parallelByGroup';

    /**
     * ParallelByGroupTestManifest constructor.
     *
     * @param array  $suiteConfiguration
     * @param string $testPath
     */
    public function __construct($suiteConfiguration, $testPath)
    {
        parent::__construct($suiteConfiguration, self::PARALLEL_CONFIG, $testPath);
    }

    /**
     * Function which generates test groups based on arg passed.
     *
     * @param integer $totalGroups
     * @return void
     * @throws TestFrameworkException
     */
    public function createTestGroups($totalGroups)
    {
        $this->testGroups = $this->parallelGroupSorter->getTestsGroupedByFixedGroupCount(
            $this->getSuiteConfig(),
            $this->testNameToSize,
            $totalGroups
        );

        $this->suiteConfiguration = $this->parallelGroupSorter->getResultingSuiteConfig();
    }
}
