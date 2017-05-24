<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Config;

/**
 * Interface ReaderInterface
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

    /**
     * Set name of the config file
     *
     * @param string $fileName
     * @return void
     */
    public function setFileName($fileName);
}
