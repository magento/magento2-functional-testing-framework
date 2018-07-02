<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\ObjectManager;

/**
 * Interface ConfigInterface
 */
interface ConfigInterface
{
    /**
     * Retrieve list of arguments per type
     *
     * @param string $type
     * @return array
     */
    public function getArguments($type);

    /**
     * Check whether type is shared
     *
     * @param string $type
     * @return boolean
     */
    public function isShared($type);

    /**
     * Retrieve instance type
     *
     * @param string $instanceName
     * @return string
     */
    public function getInstanceType($instanceName);

    /**
     * Retrieve preference for type
     *
     * @param string $type
     * @return string
     * @throws \LogicException
     */
    public function getPreference($type);

    /**
     * Extend configuration
     *
     * @param array $configuration
     * @return void
     */
    public function extend(array $configuration);
}
