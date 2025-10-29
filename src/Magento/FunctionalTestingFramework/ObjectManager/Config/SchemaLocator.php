<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\ObjectManager\Config;

use Magento\FunctionalTestingFramework\Config\SchemaLocatorInterface;

/**
 * Class SchemaLocator
 *
 * @internal
 */
class SchemaLocator implements SchemaLocatorInterface
{
    /**
     * Get path to merged config schema
     *
     * @return string
     */
    public function getSchema()
    {
        return realpath(__DIR__ .  DIRECTORY_SEPARATOR . '..'  . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . 'config.xsd';
    }

    /**
     * Get path to pre file validation schema
     *
     * @return null
     */
    public function getPerFileSchema()
    {
        return null;
    }
}
