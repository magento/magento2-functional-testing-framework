<?php
/**
 * Reader responsible for retrieving provided scope of configuration from storage
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
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
    public function read($scope = null);
}
