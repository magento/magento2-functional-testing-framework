<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Parsers;

use Magento\FunctionalTestingFramework\Config\DataInterface;

/**
 * Class OperationDefinitionParser
 */
class OperationDefinitionParser
{
    /**
     * Meta Data.
     *
     * @var DataInterface
     */
    private $metadata;

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
