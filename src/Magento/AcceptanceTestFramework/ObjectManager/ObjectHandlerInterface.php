<?php

namespace Magento\AcceptanceTestFramework\ObjectManager;

interface ObjectHandlerInterface
{
    /**
     * Function to enforce singleton design pattern
     * @return ObjectHandlerInterface
     */
    public static function getInstance();

    /**
     * Function to return a single object by name
     * @param string $jsonDefitionName
     * @return object
     */
    public function getObject($jsonDefitionName);

    /**
     * Function to return all objects the handler is responsible for
     * @return array
     */
    public function getAllObjects();
}
