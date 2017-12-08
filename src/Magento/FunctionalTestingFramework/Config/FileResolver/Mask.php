<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Config\FileResolver;

use Magento\FunctionalTestingFramework\Config\FileResolverInterface;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;
use Magento\FunctionalTestingFramework\Util\Iterator\File;

/**
 * Class Mask
 * @package Magento\FunctionalTestingFramework\Config\FileResolver
 */
class Mask implements FileResolverInterface
{
    /**
     * Resolves module paths based on enabled modules of target Magento instance.
     *
     * @var ModuleResolver
     */
    protected $moduleResolver;

    /**
     * Constructor
     *
     * @param ModuleResolver|null $moduleResolver
     */
    public function __construct(ModuleResolver $moduleResolver = null)
    {
        if ($moduleResolver) {
            $this->moduleResolver = $moduleResolver;
        } else {
            $this->moduleResolver = ModuleResolver::getInstance();
        }
    }

    /**
     * Retrieve the list of configuration files with given name that relate to specified scope
     *
     * @param string $filename
     * @param string $scope
     * @return array|\Iterator,\Countable
     */
    public function get($filename, $scope)
    {
        $paths = $this->getFileCollection($filename, $scope);

        return new File($paths);
    }

    /**
     * Get scope of paths.
     *
     * @param string $filename
     * @param string $scope
     * @return array
     */
    protected function getFileCollection($filename, $scope)
    {
        $paths = [];
        $modulesPath = $this->moduleResolver->getModulesPath();

        foreach ($modulesPath as $modulePath) {
            $path = $modulePath . DIRECTORY_SEPARATOR . $scope . DIRECTORY_SEPARATOR;
            if (is_readable($path)) {
                $directoryIterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        $path,
                        \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS
                    )
                );
                $regexpIterator = new \RegexIterator($directoryIterator, $filename);
                /** @var \SplFileInfo $file */
                foreach ($regexpIterator as $file) {
                    if ($file->isFile() && $file->isReadable()) {
                        $paths[] = $file->getRealPath();
                    }
                }
            }
        }

        return $this->moduleResolver->sortFilesByModuleSequence($paths);
    }
}
