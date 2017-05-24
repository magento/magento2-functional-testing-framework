<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TestFramework\Config;

/**
 * Class SchemaLocator
 * Scenario configuration schema locator
 */
class SchemaLocator implements \Magento\TestFramework\Config\SchemaLocatorInterface
{
    /**
     * XSD schema path
     *
     * @var string
     */
    protected $schemaPath;

    /**
     * Class constructor
     *
     * @constructor
     * @param string $schemaPath
     */
    public function __construct($schemaPath)
    {
        if (constant('BP') && file_exists(BP . '/' . $schemaPath)) {
            $this->schemaPath =  BP . '/' . $schemaPath;
        } else {
            $path = dirname(dirname(dirname(__DIR__)));
            $path = str_replace('\\', '/', $path);
            $this->schemaPath =  $path . '/' . $schemaPath;
        }
    }

    /**
     * Get path to merged config schema
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->schemaPath;
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
