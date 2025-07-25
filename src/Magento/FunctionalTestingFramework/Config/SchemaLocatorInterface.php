<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\Config;

/**
 * Interface SchemaLocatorInterface
 */
interface SchemaLocatorInterface
{
    /**
     * Get path to merged config schema
     *
     * @return string|null
     */
    public function getSchema();

    /**
     * Get path to per file validation schema
     *
     * @return string|null
     */
    public function getPerFileSchema();
}
