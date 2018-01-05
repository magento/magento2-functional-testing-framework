<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Suite\Parsers;

use Magento\FunctionalTestingFramework\Config\DataInterface;

class SuiteDataParser
{
    /**
     * Suite data interface for parser.
     *
     * @var DataInterface
     */
    private $suiteData;

    /**
     * TestDataParser constructor.
     *
     * @param DataInterface $suiteData
     */
    public function __construct(DataInterface $suiteData)
    {
        $this->suiteData = $suiteData;
    }

    /**
     * Returns an array of data based on *Test.xml files
     *
     * @return array
     */
    public function readSuiteData()
    {
        return $this->suiteData->get();
    }
}
