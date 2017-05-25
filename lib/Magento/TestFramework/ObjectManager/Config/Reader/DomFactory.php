<?php
/**
 * Config reader factory
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TestFramework\ObjectManager\Config\Reader;

/**
 * Factory class for \Magento\TestFramework\ObjectManager\Config\Reader\Dom
 */
class DomFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\TestFramework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\TestFramework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\TestFramework\ObjectManagerInterface $objectManager,
        $instanceName = \Magento\TestFramework\ObjectManager\Config\Reader\Dom::class
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\TestFramework\ObjectManager\Config\Reader\Dom
     */
    public function create(array $data = [])
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
