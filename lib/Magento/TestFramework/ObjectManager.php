<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework;

/**
 * Class ObjectManager
 *
 * Responsible for instantiating objects taking into account:
 * - constructor arguments (using configured, and provided parameters)
 * - class instances life style (singleton, transient)
 * - interface preferences
 *
 * @api
 */
class ObjectManager extends \Magento\TestFramework\ObjectManager\ObjectManager
{
    /**
     * @var \Magento\TestFramework\ObjectManager\Factory
     */
    protected $_factory;

    /**
     * @var ObjectManager
     */
    protected static $_instance;

    /**
     * @constructor
     * @param \Magento\TestFramework\ObjectManager\Factory $factory
     * @param \Magento\TestFramework\ObjectManager\ConfigInterface $config
     * @param array $sharedInstances
     */
    public function __construct(
        \Magento\TestFramework\ObjectManager\Factory $factory = null,
        \Magento\TestFramework\ObjectManager\ConfigInterface $config = null,
        array $sharedInstances = []
    ) {
        parent::__construct($factory, $config, $sharedInstances);
        $this->_sharedInstances['Magento\TestFramework\ObjectManager'] = $this;
    }

    /**
     * Get list of parameters for class method
     *
     * @param string $type
     * @param string $method
     * @return array|null
     */
    public function getParameters($type, $method)
    {
        return $this->_factory->getParameters($type, $method);
    }

    /**
     * Resolve and prepare arguments for class method
     *
     * @param object $object
     * @param string $method
     * @param array $arguments
     * @return array
     */
    public function prepareArguments($object, $method, array $arguments = [])
    {
        return $this->_factory->prepareArguments($object, $method, $arguments);
    }

    /**
     * Invoke class method with prepared arguments
     *
     * @param object $object
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function invoke($object, $method, array $arguments = [])
    {
        return $this->_factory->invoke($object, $method, $arguments);
    }

    /**
     * Set object manager instance
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public static function setInstance(ObjectManager $objectManager)
    {
        self::$_instance = $objectManager;
    }

    /**
     * Retrieve object manager
     *
     * @return ObjectManager
     * @throws \RuntimeException
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof ObjectManager) {
            return false;
        }
        return self::$_instance;
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
