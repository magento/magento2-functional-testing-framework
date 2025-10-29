<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\Config;

/**
 * Config reader interface.
 */
interface ReaderInterface
{
    /**
     * Read configuration scope
     *
     * @param string|null $scope
     * @return array
     */
    public function read(?string $scope = null);
}
