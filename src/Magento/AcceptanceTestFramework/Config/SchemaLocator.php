<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AcceptanceTestFramework\Config;

/**
 * Class SchemaLocator
 * Scenario configuration schema locator
 */
class SchemaLocator implements \Magento\AcceptanceTestFramework\Config\SchemaLocatorInterface
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
     * @param string $schemaPath
     */
    public function __construct($schemaPath)
    {
        if (constant('FW_BP') && file_exists(FW_BP . '/' . $schemaPath)) {
            $this->schemaPath =  FW_BP . '/' . $schemaPath;
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
