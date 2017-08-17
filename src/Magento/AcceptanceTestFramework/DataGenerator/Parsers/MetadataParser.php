<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\DataGenerator\Parsers;

use Magento\AcceptanceTestFramework\Config\DataInterface;

/**
 * Class MetadataParser
 */
class MetadataParser
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
     * Returns metadata.
     *
     * @return array|null
     */
    public function readMetadata()
    {
        return $this->metadata->get();
    }
}
