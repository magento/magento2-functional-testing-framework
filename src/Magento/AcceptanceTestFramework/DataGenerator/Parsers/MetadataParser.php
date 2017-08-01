<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Parsers;

use Magento\AcceptanceTestFramework\Config\DataInterface;

class MetadataParser
{
    public function __construct(DataInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    public function readMetadata()
    {
        return $this->metadata->get();
    }
}