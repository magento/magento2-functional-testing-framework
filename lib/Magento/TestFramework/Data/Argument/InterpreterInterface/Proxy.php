<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TestFramework\Data\Argument\InterpreterInterface;

/**
 * Proxy class for \Magento\TestFramework\Data\Argument\InterpreterInterface
 */
class Proxy implements \Magento\TestFramework\Data\Argument\InterpreterInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\TestFramework\ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * Proxied instance name
     *
     * @var string
     */
    protected $instanceName = null;

    /**
     * Proxied instance
     *
     * @var \Magento\TestFramework\Data\Argument\InterpreterInterface
     */
    protected $subject = null;

    /**
     * Instance shareability flag
     *
     * @var bool
     */
    protected $isShared = null;

    /**
     * Proxy constructor
     *
     * @param \Magento\TestFramework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     * @param bool $shared
     */
    public function __construct(
        \Magento\TestFramework\ObjectManagerInterface $objectManager,
        $instanceName = \Magento\TestFramework\Data\Argument\InterpreterInterface::class,
        $shared = true
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
        $this->isShared = $shared;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['subject', 'isShared'];
    }

    /**
     * Retrieve ObjectManager from global scope
     */
    public function __wakeup()
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
    }

    /**
     * Clone proxied instance
     */
    public function __clone()
    {
        $this->subject = clone $this->_getSubject();
    }

    /**
     * Get proxied instance
     *
     * @return \Magento\TestFramework\Data\Argument\InterpreterInterface
     */
    protected function _getSubject()
    {
        if (!$this->subject) {
            $this->subject = true === $this->isShared
                ? $this->objectManager->get($this->instanceName)
                : $this->objectManager->create($this->instanceName);
        }
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate(array $data)
    {
        return $this->_getSubject()->evaluate($data);
    }
}
