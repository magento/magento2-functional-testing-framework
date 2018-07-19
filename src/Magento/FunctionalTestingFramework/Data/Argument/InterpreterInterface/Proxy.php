<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Data\Argument\InterpreterInterface;

/**
 * Proxy class for \Magento\FunctionalTestingFramework\Data\Argument\InterpreterInterface
 */
class Proxy implements \Magento\FunctionalTestingFramework\Data\Argument\InterpreterInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\FunctionalTestingFramework\ObjectManagerInterface
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
     * @var \Magento\FunctionalTestingFramework\Data\Argument\InterpreterInterface
     */
    protected $subject = null;

    /**
     * Instance shareability flag
     *
     * @var boolean
     */
    protected $isShared = null;

    /**
     * Proxy constructor
     *
     * @param \Magento\FunctionalTestingFramework\ObjectManagerInterface $objectManager
     * @param string                                                     $instanceName
     * @param boolean                                                    $shared
     */
    public function __construct(
        \Magento\FunctionalTestingFramework\ObjectManagerInterface $objectManager,
        $instanceName = \Magento\FunctionalTestingFramework\Data\Argument\InterpreterInterface::class,
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
        $this->objectManager = \Magento\FunctionalTestingFramework\ObjectManager::getInstance();
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
     * @return \Magento\FunctionalTestingFramework\Data\Argument\InterpreterInterface
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
     * @return mixed
     */
    public function evaluate(array $data)
    {
        return $this->getSubject()->evaluate($data);
    }
}
