<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\ObjectManager;

use Magento\TestFramework\ObjectManager\Config\Config as ObjectManagerConfig;

/**
 * Class Config
 * Filesystem configuration loader. Loads configuration from XML files, split by scopes
 *
 * @internal
 */
class Config extends ObjectManagerConfig
{
    /**
     * @var \ReflectionClass[]
     */
    protected $_nonSharedRefClasses = [];

    /**
     * Check whether type is shared
     *
     * @param string $type
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function isShared($type)
    {
        if (isset($this->_nonShared[$type])) {
            return false;
        }

        if (isset($this->_virtualTypes[$type])) {
            return true;
        }

        if (!isset($this->_nonSharedRefClasses[$type])) {
            $this->_nonSharedRefClasses[$type] = new \ReflectionClass($type);
        }
        foreach ($this->_nonShared as $noneShared => $flag) {
            if ($this->_nonSharedRefClasses[$type]->isSubclassOf($noneShared)) {
                return false;
            }
        }

        return true;
    }
}
