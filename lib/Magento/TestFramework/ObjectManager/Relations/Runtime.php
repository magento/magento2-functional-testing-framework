<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TestFramework\ObjectManager\Relations;

/**
 * Class Runtime
 */
class Runtime implements \Magento\TestFramework\ObjectManager\RelationsInterface
{
    /**
     * @var \Magento\TestFramework\Code\Reader\ClassReader
     */
    protected $_classReader;

    /**
     * Default behavior
     *
     * @var array
     */
    protected $_default = [];

    /**
     * @param \Magento\TestFramework\Code\Reader\ClassReader $classReader
     */
    public function __construct(\Magento\TestFramework\Code\Reader\ClassReader $classReader = null)
    {
        $this->_classReader = $classReader ? : new \Magento\TestFramework\Code\Reader\ClassReader();
    }

    /**
     * Check whether requested type is available for read
     *
     * @param string $type
     * @return bool
     */
    public function has($type)
    {
        return class_exists($type) || interface_exists($type);
    }

    /**
     * Retrieve list of parents
     *
     * @param string $type
     * @return array
     */
    public function getParents($type)
    {
        if (!class_exists($type)) {
            return $this->_default;
        }
        return $this->_classReader->getParents($type) ? : $this->_default;
    }
}
