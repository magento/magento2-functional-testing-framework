<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Parsers;

use Magento\FunctionalTestingFramework\Config\DataInterface;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Filter\FilterInterface;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

/**
 * Class TestDataParser
 */
class TestDataParser
{
    /**
     * @var DataInterface
     */
    private $testData;

    /**
     * @var \Magento\FunctionalTestingFramework\Filter\FilterList
     */
    private $filterList;

    /**
     * TestDataParser constructor.
     *
     * @param DataInterface $testData
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException
     */
    public function __construct(DataInterface $testData)
    {
        $this->testData = array_filter($testData->get('tests'), function ($value) {
            return is_array($value);
        });

        $this->filterList = MftfApplicationConfig::getConfig()->getFilterList();
    }

    /**
     * Returns an array of data based on *Test.xml files
     *
     * @return array
     * @throws TestFrameworkException
     */
    public function readTestData()
    {
        /** @var FilterInterface $filter */
        foreach ($this->filterList->getFilters() as $filter) {
            $filter->filter($this->testData);
        }

        if (empty($this->testData)) {
            throw new TestFrameworkException("No tests found.");
        }

        return $this->testData;
    }
}
