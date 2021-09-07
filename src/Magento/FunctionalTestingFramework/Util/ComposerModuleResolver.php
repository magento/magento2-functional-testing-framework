<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Util;

use Magento\FunctionalTestingFramework\Composer\ComposerInstall;
use Magento\FunctionalTestingFramework\Composer\ComposerPackage;
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

        if (!file_exists($rootComposerFile) || basename($rootComposerFile, '.json') !== 'composer') {
            throw new TestFrameworkException("Invalid root composer json file: {$rootComposerFile}");
        }

        $this->installedTestModules = [];
        $composer = new ComposerInstall($rootComposerFile);

        foreach ($composer->getInstalledTestPackages() as $packageName => $packageData) {
            $suggestedModuleNames = $packageData[ComposerInstall::PACKAGE_SUGGESTED_MAGENTO_MODULES];
            $path = $packageData[ComposerInstall::PACKAGE_INSTALLEDPATH];
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
        $fileList = $this->findComposerJsonFilesAtDepth($normalizedDir, 2);
        foreach ($fileList as $file) {
            // Parse composer json for test module name and path information
            $composerInfo = new ComposerPackage($file);
            if ($composerInfo->isMftfTestPackage()) {
                $modulePath = str_replace(
                    DIRECTORY_SEPARATOR . 'composer.json',
                    '',
                    $file
                );
                $suggestedMagentoModuleNames = $composerInfo->getSuggestedMagentoModules();
                if (array_key_exists($modulePath, $modules)) {
                    $modules[$modulePath] = array_merge($modules[$modulePath], $suggestedMagentoModuleNames);
                } else {
                    $modules[$modulePath] = $suggestedMagentoModuleNames;
                }
            }
        }
        return $modules;
    }

    /**
     * Find absolute paths of all composer json files in a given directory
     *
     * @param string $directory
     * @return array
     */
    private function findAllComposerJsonFiles($directory)
    {
        $directory = realpath($directory);
        $jsonPattern = DIRECTORY_SEPARATOR . "composer.json";
        $subDirectoryPattern = DIRECTORY_SEPARATOR . "*";

        $jsonFileList = [];
        foreach (glob($directory . $subDirectoryPattern, GLOB_ONLYDIR) as $dir) {
            $jsonFileList = array_merge_recursive($jsonFileList, self::findAllComposerJsonFiles($dir));
        }

        $curJsonFiles = glob($directory . $jsonPattern);
        if ($curJsonFiles !== false && !empty($curJsonFiles)) {
            $jsonFileList = array_merge_recursive($jsonFileList, $curJsonFiles);
        }
        return $jsonFileList;
    }

    /**
     * Find absolute paths of all composer json files in a given directory at certain depths
     *
     * @param string  $directory
     * @param integer $depth
     * @return array
     */
    private function findComposerJsonFilesAtDepth($directory, $depth)
    {
        $directory = realpath($directory);
        $jsonPattern = DIRECTORY_SEPARATOR . "composer.json";
        $subDirectoryPattern = DIRECTORY_SEPARATOR . "*";

        $jsonFileList = [];
        if ($depth > 0) {
            foreach (glob($directory . $subDirectoryPattern, GLOB_ONLYDIR) as $dir) {
                $jsonFileList = array_merge_recursive(
                    $jsonFileList,
                    self::findComposerJsonFilesAtDepth($dir, $depth-1)
                );
            }
        } elseif ($depth === 0) {
            $jsonFileList = glob($directory . $jsonPattern);
            if ($jsonFileList === false) {
                $jsonFileList = [];
            }
        }
        return $jsonFileList;
    }
}
