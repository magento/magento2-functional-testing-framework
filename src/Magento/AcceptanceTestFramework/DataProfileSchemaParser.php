<?php
/**
 * Created by PhpStorm.
 * User: imeron
 * Date: 6/9/17
 * Time: 1:53 PM
 */

namespace Magento\AcceptanceTestFramework;

use Magento\AcceptanceTestFramework\Config\DataInterface;

class DataProfileSchemaParser
{

    public function __construct(DataInterface $dataProfiles)
    {
        $this->dataProfiles = $dataProfiles;
    }

    public function readDataProfiles()
    {
        return $this->dataProfiles->get();
    }
}