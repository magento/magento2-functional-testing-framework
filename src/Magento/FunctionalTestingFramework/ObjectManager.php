<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework;

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
class ObjectManager extends \Magento\FunctionalTestingFramework\ObjectManager\ObjectManager
{
    /**
     * Object manager factory.
     *
     * @var \Magento\FunctionalTestingFramework\ObjectManager\Factory
     */
    protected $factory;

    /**
     * Object manager instance.
     *
     * @var ObjectManager
     */
    protected static $instance;

    /**
     * ObjectManager constructor.
     * @param ObjectManager\Factory|null         $factory
     * @param ObjectManager\ConfigInterface|null $config
     * @param array                              $sharedInstances
     */
    public function __construct(
        ?\Magento\FunctionalTestingFramework\ObjectManager\Factory $factory = null,
        ?\Magento\FunctionalTestingFramework\ObjectManager\ConfigInterface $config = null,
        array $sharedInstances = []
    ) {
        parent::__construct($factory, $config, $sharedInstances);
        $this->sharedInstances[\Magento\FunctionalTestingFramework\ObjectManager::class] = $this;
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
        return $this->factory->getParameters($type, $method);
    }

    /**
     * Resolve and prepare arguments for class method
     *
     * @param object $object
     * @param string $method
     * @param array  $arguments
     * @return array
     */
    public function prepareArguments($object, $method, array $arguments = [])
    {
        return $this->factory->prepareArguments($object, $method, $arguments);
    }

    // @codingStandardsIgnoreStart
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
        return $this->factory->invoke($object, $method, $arguments);
    }
    // @codingStandardsIgnoreEnd

    /**
     * Set object manager instance
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public static function setInstance(ObjectManager $objectManager)
    {
        self::$instance = $objectManager;
    }

    /**
     * Retrieve object manager
     *
     * @return ObjectManager|boolean
     * @throws \RuntimeException
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof ObjectManager) {
            return false;
        }
        return self::$instance;
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
