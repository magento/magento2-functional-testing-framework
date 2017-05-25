<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Util;

/**
 * Class ModuleResolver, resolve module path.
 *
 * @api
 */
class ModuleResolver
{
    /**
     * ModuleResolver instance.
     *
     * @var ModuleResolver
     */
    private static $instance = null;

    /**
     * Get ModuleResolver instance.
     *
     * @return ModuleResolver
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new ModuleResolver();
        }
        return self::$instance;
    }

    /**
     * ModuleResolver constructor.
     */
    private function __construct()
    {
        //
    }

    /**
     * Return the modules path based on which modules are enabled in the target Magento instance.
     *
     * @return array
     */
    public function getModulesPath()
    {
        $modulePath = BP . '/src/Magento/Xxyyzz/Page';
        $allModulePaths = glob($modulePath . '/*');

        return $allModulePaths;
    }
}
