<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Config;

/**
 * Class Data
 */
class Data implements \Magento\FunctionalTestingFramework\Config\DataInterface
{
    /**
     * Configuration reader model
     *
     * @var \Magento\FunctionalTestingFramework\Config\ReaderInterface
     */
    protected $reader;

    /**
     * Config data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Constructor
     *
     * @param \Magento\FunctionalTestingFramework\Config\ReaderInterface $reader
     */
    public function __construct(\Magento\FunctionalTestingFramework\Config\ReaderInterface $reader)
    {
        $this->reader = $reader;
        $this->load();
    }

    /**
     * Merge config data to the object
     *
     * @param array $config
     * @return void
     */
    public function merge(array $config)
    {
        $this->data = array_replace_recursive($this->data, $config);
    }

    // @codingStandardsIgnoreStart
    /**
     * Get config value by key
     *
     * @param string $path
     *
     * @param null|mixed $default
     * @return array|mixed|null
     */
    public function get(mixed $path = null, mixed $default = null)
    {
        if ($path === null) {
            return $this->data;
        }
        $keys = explode('/', $path);
        $data = $this->data;
        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return $default;
            }
        }
        return $data;
    }
    // @codingStandardsIgnoreEnd

    /**
     * Set name of the config file
     *
     * @param string $fileName
     * @return self
     */
    public function setFileName($fileName)
    {
        if ($fileName !== null) {
            $this->reader->setFileName($fileName);
        }
        return $this;
    }

    /**
     * Load config data
     *
     * @param string|null $scope
     * @return void
     */
    public function load(?string $scope = null)
    {
        $this->merge(
            $this->reader->read($scope)
        );
    }
}
