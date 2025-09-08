<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
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
    }

    /**
     * Returns an array of data based on *Test.xml files
     *
     * @return array
     * @throws TestFrameworkException
     */
    public function readTestData()
    {
        return $this->testData;
    }
}
