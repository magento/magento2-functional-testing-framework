<?php

namespace Magento\AcceptanceTestFramework\Test;

use Magento\AcceptanceTestFramework\Config\DataInterface;

class TestDataParser
{
    /**
     * TestDataParser constructor.
     * @constructor
     * @param DataInterface $testData
     */
    public function __construct(DataInterface $testData)
    {
        $this->testData = $testData;
    }

    /**
     * Returns an array of data based on *Cest.xml files
     * @return array
     */
    public function readTestData()
    {
        return $this->testData->get();
    }
}
