<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Parsers;

use Magento\AcceptanceTestFramework\Config\DataInterface;

class DataProfileSchemaParser
{

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
     * @return array
     */
    public function readDataProfiles()
    {
        return $this->dataProfiles->get();
    }
}
