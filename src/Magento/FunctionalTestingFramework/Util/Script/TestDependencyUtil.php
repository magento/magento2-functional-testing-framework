<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Util\Script;

/**
 * TestDependencyUtil class that contains helper functions for static and upgrade scripts
 *
 * @package Magento\FunctionalTestingFramework\Util\Script
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TestDependencyUtil
{
    /**
     * Array of FullModuleName => [dependencies]
     * @var array
     */
    private $allDependencies;

    /**
     * Transactional Array to keep track of what dependencies have already been extracted.
     * @var array
     */
    private $alreadyExtractedDependencies;

    /**
     * Builds and returns array of FullModuleNae => composer name
     * @param array $moduleNameToPath
     * @return array
     */
    public function buildModuleNameToComposerName(array $moduleNameToPath): array
    {
        $moduleNameToComposerName = [];
        foreach ($moduleNameToPath as $moduleName => $path) {
            $composerData = json_decode(file_get_contents($path . DIRECTORY_SEPARATOR . "composer.json"));
            $moduleNameToComposerName[$moduleName] = $composerData->name;
        }
        return $moduleNameToComposerName;
    }

    /**
     * Builds and returns flattened dependency list based on composer dependencies
     * @param array $moduleNameToPath
     * @param array $moduleNameToComposerName
     * @return array
     */
    public function buildComposerDependencyList(array $moduleNameToPath, array $moduleNameToComposerName): array
    {
        $flattenedDependencies = [];

        foreach ($moduleNameToPath as $moduleName => $pathToModule) {
            $composerData = json_decode(
                file_get_contents($pathToModule . DIRECTORY_SEPARATOR . "composer.json"),
                true
            );
            $this->allDependencies[$moduleName] = $composerData['require'];
        }
        foreach ($this->allDependencies as $moduleName => $dependencies) {
            $this->alreadyExtractedDependencies = [];
            $flattenedDependencies[$moduleName] = $this->extractSubDependencies($moduleName, $moduleNameToComposerName);
        }
        return $flattenedDependencies;
    }

    /**
     * Recursive function to fetch dependencies of given dependency, and its child dependencies
     * @param string $subDependencyName
     * @param array  $moduleNameToComposerName
     * @return array
     */
    private function extractSubDependencies(string $subDependencyName, array $moduleNameToComposerName): array
    {
        $flattenedArray = [];

        if (in_array($subDependencyName, $this->alreadyExtractedDependencies)) {
            return $flattenedArray;
        }

        if (isset($this->allDependencies[$subDependencyName])) {
            $subDependencyArray = $this->allDependencies[$subDependencyName];
            $flattenedArray = array_merge($flattenedArray, $this->allDependencies[$subDependencyName]);

            // Keep track of dependencies that have already been used, prevents circular dependency problems
            $this->alreadyExtractedDependencies[] = $subDependencyName;
            foreach ($subDependencyArray as $composerDependencyName => $version) {
                $subDependencyFullName = array_search($composerDependencyName, $moduleNameToComposerName);
                $flattenedArray = array_merge(
                    $flattenedArray,
                    $this->extractSubDependencies($subDependencyFullName, $moduleNameToComposerName)
                );
            }
        }
        return $flattenedArray;
    }

    /**
     * Finds unique array composer dependencies of given testObjects
     * @param array $allEntities
     * @param array $moduleComposerName
     * @param array $moduleNameToPath
     * @return array
     */
    public function getModuleDependenciesFromReferences(
        array $allEntities,
        array $moduleComposerName,
        array $moduleNameToPath
    ): array {
        $filenames = [];
        foreach ($allEntities as $item) {
            // Should it append ALL filenames, including merges?
            $allFiles = explode(",", $item->getFilename());
            foreach ($allFiles as $file) {
                $moduleName = $this->getModuleName($file, $moduleNameToPath);
                if (isset($moduleComposerName[$moduleName])) {
                    $composerModuleName = $moduleComposerName[$moduleName];
                    $filenames[$item->getName()][] = $composerModuleName;
                }
            }
        }
        return $filenames;
    }

    /**
     * Return module name for a file path
     *
     * @param string $filePath
     * @param array  $moduleNameToPath
     * @return string|null
     */
    public function getModuleName(string $filePath, array $moduleNameToPath): ?string
    {
        $moduleName = null;
        foreach ($moduleNameToPath as $name => $path) {
            if (strpos($filePath, $path. "/") !== false) {
                $moduleName = $name;
                break;
            }
        }
        return $moduleName;
    }

    /**
     * Return array of merge test modules and file path with same test name.
     * @param array $testDependencies
     * @param array $extendedTestMapping
     * @return array
     */
    public function mergeDependenciesForExtendingTests(array $testDependencies, array $extendedTestMapping = []): array
    {
        $temp_array = array_reverse(array_column($testDependencies, "test_name"), true);
        if (!empty($extendedTestMapping)) {
            foreach ($extendedTestMapping as $value) {
                $key = array_search($value["parent_test_name"], $temp_array);
                if ($key !== false) {
                    #if parent test found merge this to child, for doing so just replace test name with child.
                    $testDependencies[$key]["test_name"] = $value["child_test_name"];
                }
            }
        }
        $temp_array = [];
        foreach ($testDependencies as $testDependency) {
            $temp_array[$testDependency["test_name"]][] = $testDependency;
        }
        $testDependencies = [];
        foreach ($temp_array as $testDependencyArray) {
            $testDependencies[] = [
                "file_path" => array_column($testDependencyArray, 'file_path'),
                "full_name" => $testDependencyArray[0]["full_name"],
                "test_name" => $testDependencyArray[0]["test_name"],
                "test_modules" =>array_values(
                    array_unique(
                        call_user_func_array(
                            'array_merge',
                            array_column($testDependencyArray, 'test_modules')
                        )
                    )
                ),
            ];
        }
        return $testDependencies;
    }
}
