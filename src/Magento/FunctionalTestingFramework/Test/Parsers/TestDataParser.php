<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Parsers;

use Magento\FunctionalTestingFramework\Config\DataInterface;

/**
 * Class TestDataParser
 */
class TestDataParser
{
    /**
     * TestDataParser constructor.
     *
     * @param DataInterface $testData
     */
    public function __construct(DataInterface $testData)
    {
        $this->testData = $testData;
    }

    /**
     * Returns an array of data based on *Test.xml files
     *
     * @return array
     */
    public function readTestData()
    {
        return $this->testData->get();
    }
}
