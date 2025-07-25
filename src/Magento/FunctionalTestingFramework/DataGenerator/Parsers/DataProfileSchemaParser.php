<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Parsers;

use Magento\FunctionalTestingFramework\Config\DataInterface;

/**
 * Class DataProfileSchemaParser
 */
class DataProfileSchemaParser
{
    /**
     * Data Profiles.
     *
     * @var DataInterface
     */
    private $dataProfiles;

    /**
     * DataProfileSchemaParser constructor.
     * @param DataInterface $dataProfiles
     */
    public function __construct(DataInterface $dataProfiles)
    {
        $this->dataProfiles = $dataProfiles;
    }

    /**
     * Function to return data as array from data.xml files
     *
     * @return array
     */
    public function readDataProfiles()
    {
        return $this->dataProfiles->get();
    }
}
