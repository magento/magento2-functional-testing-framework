<?php
/**
 * Config reader factory
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\ObjectManager\Config\Reader;

/**
 * Factory class for \Magento\FunctionalTestingFramework\ObjectManager\Config\Reader\Dom
 */
class DomFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\FunctionalTestingFramework\ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\FunctionalTestingFramework\ObjectManagerInterface $objectManager
     * @param string                                                     $instanceName
     */
    public function __construct(
        \Magento\FunctionalTestingFramework\ObjectManagerInterface $objectManager,
        $instanceName = \Magento\FunctionalTestingFramework\ObjectManager\Config\Reader\Dom::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\FunctionalTestingFramework\ObjectManager\Config\Reader\Dom
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}
