<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Util;

use Magento\FunctionalTestingFramework\Composer\Handlers\ComposerInstaller;
use Magento\FunctionalTestingFramework\Composer\Util\ComposerJsonFinder;
use Magento\FunctionalTestingFramework\Composer\Objects\ComposerFactory;
use Magento\FunctionalTestingFramework\Composer\Handlers\ComposerPackager;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

/**
 * Composer Based Module Resolver
 */
class ComposerModuleResolver
{
    /**
     * Code path array from composer json search
     *
     * @var array
     */
    private $searchedTestModules = null;

    /**
     * Code path array from composer installed test packages
     *
     * @var array
     */
    private $installedTestModules = null;

    /**
     * ComposerModuleResolver constructor
     */
    public function __construct()
    {
    }

    /**
     * Get code paths for installed test modules
     *
     * @param string $rootComposerFile
     * @return array
     * @throws TestFrameworkException
     */
    public function getComposerInstalledTestModules($rootComposerFile)
    {
        if (null !== $this->installedTestModules) {
            return $this->installedTestModules;
        }

        if (!is_file($rootComposerFile) || substr($rootComposerFile, -13) != 'composer.json') {
            throw new TestFrameworkException("Invalid root composer json file: {$rootComposerFile}");
        }

        $this->installedTestModules = [];
        $composerInstaller = new ComposerInstaller(
            new ComposerFactory($rootComposerFile)
        );

        foreach ($composerInstaller->getInstalledTestPackages() as $packageName => $packageData) {
            $suggestedModuleNames = $packageData[ComposerInstaller::KEY_PACKAGE_SUGGESTED_MAGENTO_MODULES];
            $path = $packageData[ComposerInstaller::KEY_PACKAGE_INSTALLEDPATH];
            $this->installedTestModules[$path] = $suggestedModuleNames;
        }
        return $this->installedTestModules;
    }

    /**
     * Get code paths by searching test module composer json file from input directories
     *
     * @param array $directories
     * @return array
     * @throws TestFrameworkException
     */
    public function getTestModulesFromPaths($directories)
    {
        if (null !== $this->searchedTestModules) {
            return $this->searchedTestModules;
        }

        $this->searchedTestModules = [];
        foreach ($directories as $directory) {
            $this->searchedTestModules = array_merge_recursive(
                $this->searchedTestModules,
                $this->getTestModules($directory)
            );
        }
        return $this->searchedTestModules;
    }

    /**
     * Get code paths by searching test module composer json file from input directory
     *
     * @param string $directory
     * @return array
     * @throws TestFrameworkException
     */
    private function getTestModules($directory)
    {
        $normalizedDir = realpath($directory);
        if (!is_dir($normalizedDir)) {
            throw new TestFrameworkException("Invalid directory: {$directory}");
        }

        // Find all composer json files under directory
        $modules = [];
        $jsonFinder = new ComposerJsonFinder();
        $fileList = $jsonFinder->findAllComposerJsonFiles($normalizedDir);
        foreach ($fileList as $file) {
            // Parse composer json for test module name and path information
            $composerInfo = new ComposerPackager(
                new ComposerFactory($file)
            );
            if ($composerInfo->isMftfTestPackage()) {
                //$moduleName = $composerInfo->getName();
                $modulePath = str_replace(
                    DIRECTORY_SEPARATOR . 'composer.json',
                    '',
                    $file
                );
                $suggestedMagentoModuleNames = $composerInfo->getSuggestedMagentoModules();
                $module[$modulePath] = $suggestedMagentoModuleNames;
                $modules = array_merge_recursive($modules, $module);
            }
        }
        return $modules;
    }
}
