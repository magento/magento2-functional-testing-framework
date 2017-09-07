<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Config;

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

    // @codingStandardsIgnoreStart
    /**
     * Get config value by key
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get($key = null, $default = null);
    // @codingStandardsIgnoreEnd

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
