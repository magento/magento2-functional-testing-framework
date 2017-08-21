<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AcceptanceTestFramework\Data\Argument\InterpreterInterface;

/**
 * Proxy class for \Magento\AcceptanceTestFramework\Data\Argument\InterpreterInterface
 */
class Proxy implements \Magento\AcceptanceTestFramework\Data\Argument\InterpreterInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\AcceptanceTestFramework\ObjectManagerInterface
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
     * @var \Magento\AcceptanceTestFramework\Data\Argument\InterpreterInterface
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
     * @param \Magento\AcceptanceTestFramework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     * @param bool $shared
     */
    public function __construct(
        \Magento\AcceptanceTestFramework\ObjectManagerInterface $objectManager,
        $instanceName = \Magento\AcceptanceTestFramework\Data\Argument\InterpreterInterface::class,
        $shared = true
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
        $this->isShared = $shared;
    }

    /**
     * Definition of field which should be serialized.
     *
     * @return array
     */
    public function __sleep()
    {
        return ['subject', 'isShared'];
    }

    /**
     * Retrieve ObjectManager from global scope
     * @return void
     */
    public function __wakeup()
    {
        $this->objectManager = \Magento\AcceptanceTestFramework\ObjectManager::getInstance();
    }

    /**
     * Clone proxied instance
     * @return void
     */
    public function __clone()
    {
        $this->subject = clone $this->getSubject();
    }

    /**
     * Get proxied instance
     *
     * @return \Magento\AcceptanceTestFramework\Data\Argument\InterpreterInterface
     */
    protected function getSubject()
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
        return $this->getSubject()->evaluate($data);
    }
}
