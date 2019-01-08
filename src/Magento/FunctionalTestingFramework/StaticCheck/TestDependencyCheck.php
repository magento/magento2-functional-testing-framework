<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\StaticCheck;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\Page\Handlers\PageObjectHandler;
use Magento\FunctionalTestingFramework\Page\Handlers\SectionObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Symfony\Component\Console\Input\InputInterface;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;

/**
 * Class TestDependencyCheck
 * @package Magento\FunctionalTestingFramework\StaticCheck
 */
class TestDependencyCheck implements StaticCheckInterface
{
    /**
     * Array of FullModuleName => [dependencies]
     * @var array
     */
    private $allDependencies;

    /**
     * Array of FullModuleName => [dependencies], including flattened dependency tree
     * @var array
     */
    private $flattenedDependencies;

    /**
     * Array of FullModuleName => PathToModule
     * @var array
     */
    private $moduleNameToPath;

    /**
     * Array of FullModuleName => ComposerModuleName
     * @var array
     */
    private $moduleNameToComposerName;

    /**
     * Transactional Array to keep track of what dependencies have already been extracted.
     * @var array
     */
    private $alreadyExtractedDependencies;

    /**
     * Checks test dependencies, determined by references in tests versus the dependencies listed in the Magento module
     *
     * @param InputInterface $input
     * @return string
     */
    public function execute(InputInterface $input)
    {
        $testErrors = [];
        // Check {{data.xml}}, {{section.element}}, {{page.url}} references
        MftfApplicationConfig::create(
            true,
            MftfApplicationConfig::GENERATION_PHASE,
            false,
            false
        );
        $testObjects = TestObjectHandler::getInstance()->getAllObjects();

        $registrar = new \Magento\Framework\Component\ComponentRegistrar();
        $this->moduleNameToPath = $registrar->getPaths(\Magento\Framework\Component\ComponentRegistrar::MODULE);
        $this->moduleNameToComposerName = $this->buildModuleNameToComposerName($this->moduleNameToPath);
        $this->flattenedDependencies = $this->buildComposerDependencyList();

        foreach ($testObjects as $testObject)
        {
            // Find Tests' Base Module Dependencies
            $allFiles = explode(",", $testObject->getFilename());
            $basefile = $allFiles[0];
            $modulePath = dirname(dirname(dirname(dirname($basefile))));
            $moduleFullName = array_search($modulePath, $this->moduleNameToPath) ?? null;

            // Not a module
            if ($moduleFullName == null) {
                continue;
            }

            // Force Test to resolve references
            $testObject->getOrderedActions();

            // Find objects test referenced
            $dataReferences = DataObjectHandler::getInstance()->getAccessedObjects();
            $sectionReferences = SectionObjectHandler::getInstance()->getAccessedObjects();
            $pageReferences = PageObjectHandler::getInstance()->getAccessedObjects();

            // Build list dependencies from test references
            $modulesReferencedInTest = array_merge(
                $this->getModuleDependenciesFromReferences($dataReferences),
                $this->getModuleDependenciesFromReferences($sectionReferences),
                $this->getModuleDependenciesFromReferences($pageReferences)
                );

            // Unset Ref to current module
            unset($modulesReferencedInTest[$this->moduleNameToComposerName[$moduleFullName]]);

            // Calculate differences between module and test references
            $moduleDependencies = $this->flattenedDependencies[$moduleFullName];
            $diff = array_intersect_key($modulesReferencedInTest, $moduleDependencies);
            if (count($diff) != count($modulesReferencedInTest)) {
                $missingDependencies = [];
                foreach ($modulesReferencedInTest as $module => $key) {
                    if (!array_key_exists($module, $diff)) {
                        $missingDependencies[] = $module;
                    }
                }
                $errorOutput = "Test \"{$testObject->getName()}\" ({$basefile}) contains references to following modules:\n\t\t";
                $errorOutput .= implode("\n\t\t", $missingDependencies);
                $testErrors[$testObject->getName()][] = $errorOutput;
            }
        }

        echo 'done';
        $this->clearHandlerObjectCache();

        //print all errors

    }

    private function buildModuleNameToComposerName($array)
    {
        $returnList = [];
        foreach ($array as $moduleName => $path)
        {
            $composerData = json_decode(file_get_contents($path . DIRECTORY_SEPARATOR . "composer.json"));
            $returnList[$moduleName] = $composerData->name;
        }
        return $returnList;
    }

    private function buildComposerDependencyList()
    {
        $flattenedDependencies = [];

        foreach ($this->moduleNameToPath as $moduleName => $pathToModule)
        {
            $composerData = json_decode(file_get_contents($pathToModule . DIRECTORY_SEPARATOR . "composer.json"), true);
            $this->allDependencies[$moduleName] = $composerData['require'];
        }
        foreach ($this->allDependencies as $moduleName => $dependencies)
        {
            $this->alreadyExtractedDependencies = [];
            $flattenedDependencies[$moduleName] = $this->extractSubDependencies($moduleName);
        }
        return $flattenedDependencies;
    }

    private function extractSubDependencies($subDependencyName)
    {
        $flattenedArray = [];

        if (array_search($subDependencyName, $this->alreadyExtractedDependencies) !== false){
            return $flattenedArray;
        }

        if (isset($this->allDependencies[$subDependencyName]))
        {
            $subDependencyArray = $this->allDependencies[$subDependencyName];
            $flattenedArray = array_merge($flattenedArray, $this->allDependencies[$subDependencyName]);

            // Keep track of dependencies that have already been used, prevents circular dependency problems
            $this->alreadyExtractedDependencies[] = $subDependencyName;
            foreach ($subDependencyArray as $composerDependencyName => $version)
            {
                $subDependencyFullName = array_search($composerDependencyName, $this->moduleNameToComposerName) ?? "NOT FOUND";
                $flattenedArray = array_merge($subDependencyArray,
                    $this->extractSubDependencies($subDependencyFullName)
                );
            }
        }
        return $flattenedArray;
    }

    private function getModuleDependenciesFromReferences($array)
    {
        $filenames = [];
        foreach ($array as $item)
        {
            // Should it append ALL filenames, including merges?
            $allFiles = explode(",", $item->getFilename());
            $basefile = $allFiles[0];
            $modulePath = dirname(dirname(dirname(dirname($basefile))));
            $fullModuleName = array_search($modulePath, $this->moduleNameToPath);
            $composerModuleName = $this->moduleNameToComposerName[$fullModuleName];
            if ($composerModuleName == null) {
                echo 'BREAK';
            }
            $filenames[$composerModuleName] = $composerModuleName;
        }
        return $filenames;
    }

    private function clearHandlerObjectCache()
    {
        DataObjectHandler::getInstance()->clearAccessedObjects();
        SectionObjectHandler::getInstance()->clearAccessedObjects();
        PageObjectHandler::getInstance()->clearAccessedObjects();
    }
}
