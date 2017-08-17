<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\Config\FileResolver;

use Magento\AcceptanceTestFramework\Util\Iterator\File;
use Magento\AcceptanceTestFramework\Config\FileResolverInterface;
use Magento\AcceptanceTestFramework\Util\ModuleResolver;

/**
 * Provides the list of configuration files collected through modules test folders.
 */
class Module implements FileResolverInterface
{
    /**
     * Resolves module paths based on enabled modules of target Magento instance.
     *
     * @var ModuleResolver
     */
    private $moduleResolver;

    /**
     * Module constructor.
     * @param ModuleResolver|null $moduleResolver
     */
    public function __construct(ModuleResolver $moduleResolver = null)
    {
        $this->moduleResolver = ModuleResolver::getInstance();
    }

    /**
     * Retrieve the list of configuration files with given name that relate to specified scope.
     *
     * @param string $filename
     * @param string $scope
     * @return array|\Iterator,\Countable
     */
    public function get($filename, $scope)
    {
        $modulesPath = $this->moduleResolver->getModulesPath();
        $paths = [];
        foreach ($modulesPath as $modulePath) {
            $path = $modulePath . DIRECTORY_SEPARATOR . $scope . DIRECTORY_SEPARATOR . $filename;
            $paths = array_merge($paths, glob($path));
        }

        $iterator = new File($paths);
        return $iterator;
    }
}
