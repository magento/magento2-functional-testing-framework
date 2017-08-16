<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Parsers;

use Magento\AcceptanceTestFramework\Config\DataInterface;

class OperationMetadataParser
{
    /**
     * MetadataParser constructor.
     * @param DataInterface $metadata
     */
    public function __construct(DataInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Returns an array containing all data read from operations.xml files.
     * @return array
     */
    public function readOperationMetadata()
    {
        return $this->metadata->get();
    }
}