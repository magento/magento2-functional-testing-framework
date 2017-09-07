<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Parsers;

use Magento\FunctionalTestingFramework\Config\DataInterface;

/**
 * Class OperationMetadataParser
 */
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
     *
     * @return array
     */
    public function readOperationMetadata()
    {
        return $this->metadata->get();
    }
}
