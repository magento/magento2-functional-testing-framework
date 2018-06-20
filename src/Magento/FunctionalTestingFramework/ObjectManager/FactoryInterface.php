<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\ObjectManager;

/**
 * Interface FactoryInterface
 */
interface FactoryInterface
{
    /**
     * Create instance with call time arguments
     *
     * @param string $requestedType
     * @param array  $arguments
     * @return object
     * @throws \LogicException
     * @throws \BadMethodCallException
     */
    public function create($requestedType, array $arguments = []);
}
