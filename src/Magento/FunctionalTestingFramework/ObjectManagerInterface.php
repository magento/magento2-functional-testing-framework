<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework;

/**
 * Interface ObjectManagerInterface
 */
interface ObjectManagerInterface
{
    /**
     * Create new object instance
     *
     * @param string $type
     * @param array  $arguments
     * @return object
     */
    public function create($type, array $arguments = []);

    /**
     * Retrieve cached object instance
     *
     * @param string $type
     * @return object
     */
    public function get($type);

    /**
     * Configure object manager
     *
     * @param array $configuration
     * @return void
     */
    public function configure(array $configuration);
}
