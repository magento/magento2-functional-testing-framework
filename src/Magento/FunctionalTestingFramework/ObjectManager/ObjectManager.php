<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\ObjectManager;

/**
 * Class ObjectManager
 */
class ObjectManager implements \Magento\FunctionalTestingFramework\ObjectManagerInterface
{
    /**
     * Create instance with call time arguments.
     *
     * @var \Magento\FunctionalTestingFramework\ObjectManager\FactoryInterface
     */
    protected $factory;

    /**
     * List of shared instances
     *
     * @var array
     */
    protected $sharedInstances = [];

    /**
     * Class config.
     *
     * @var Config\Config
     */
    protected $config;

    /**
     * ObjectManager constructor.
     * @param FactoryInterface $factory
     * @param ConfigInterface  $config
     * @param array            $sharedInstances
     */
    public function __construct(FactoryInterface $factory, ConfigInterface $config, array $sharedInstances = [])
    {
        $this->config = $config;
        $this->factory = $factory;
        $this->sharedInstances = $sharedInstances;
        $this->sharedInstances[\Magento\FunctionalTestingFramework\ObjectManagerInterface::class] = $this;
    }

    /**
     * Create new object instance
     *
     * @param string $type
     * @param array  $arguments
     * @return object
     */
    public function create($type, array $arguments = [])
    {
        return $this->factory->create($this->config->getPreference($type), $arguments);
    }

    /**
     * Retrieve cached object instance
     *
     * @param string $type
     * @return object
     */
    public function get($type)
    {
        $type = $this->config->getPreference($type);
        if (!isset($this->sharedInstances[$type])) {
            $this->sharedInstances[$type] = $this->factory->create($type);
        }
        return $this->sharedInstances[$type];
    }

    /**
     * Configure di instance
     *
     * @param array $configuration
     * @return void
     */
    public function configure(array $configuration)
    {
        $this->config->extend($configuration);
    }

    /**
     * Avoid to serialize Closure properties
     *
     * @return array
     */
    public function __sleep()
    {
        return [];
    }
}
