<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TestFramework\Config;

/**
 * Interface DataInterface
 */
interface DataInterface
{
    /**
     * Merge config data to the object
     *
     * @param array $config
     * @return void
     */
    public function merge(array $config);

    /**
     * Get config value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key = null, $default = null);

    /**
     * Load config data
     *
     * @param string|null $scope
     * @return void
     */
    public function load($scope = null);

    /**
     * Set name of the config file
     *
     * @param string $fileName
     * @return self
     */
    public function setFileName($fileName);
}
