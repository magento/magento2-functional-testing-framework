<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\DataGenerator\Parsers;

use Magento\AcceptanceTestFramework\Config\DataInterface;

/**
 * Class DataProfileSchemaParser
 */
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
     * Returns all data profiles.
     *
     * @return array|null
     */
    public function readDataProfiles()
    {
        return $this->dataProfiles->get();
    }
}
