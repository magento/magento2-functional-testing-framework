<?php

namespace Magento\AcceptanceTestFramework\Test;

use Magento\AcceptanceTestFramework\Config\DataInterface;

class TestDataParser
{
    public function __construct(DataInterface $testData)
    {
        $this->testData = $testData;
    }

    public function readTestData()
    {
        return $this->testData->get();
    }
}