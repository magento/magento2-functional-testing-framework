<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\ObjectManager;

/**
 * Interface ObjectHandlerInterface
 */
interface ObjectHandlerInterface
{
    const OBJ_DEPRECATED = 'deprecated';

    /**
     * Function to enforce singleton design pattern
     *
     * @return ObjectHandlerInterface
     */
    public static function getInstance();

    /**
     * Function to return a single object by name
     *
     * @param string $objectName
     * @return mixed
     */
    public function getObject($objectName);

    /**
     * Function to return all objects the handler is responsible for
     *
     * @return array
     */
    public function getAllObjects();
}
