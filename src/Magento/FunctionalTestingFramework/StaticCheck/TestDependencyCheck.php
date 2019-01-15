<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\StaticCheck;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\Page\Handlers\PageObjectHandler;
use Magento\FunctionalTestingFramework\Page\Handlers\SectionObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Symfony\Component\Console\Input\InputInterface;

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
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(InputInterface $input)
    {
        MftfApplicationConfig::create(
            true,
            MftfApplicationConfig::UNIT_TEST_PHASE,
            false,
            false
        );

        $testObjects = TestObjectHandler::getInstance()->getAllObjects();
        if (!class_exists('\Magento\Framework\Component\ComponentRegistrar')) {
            return "TEST DEPENDENCY CHECK ABORTED: MFTF must be attached or pointing to Magento codebase.";
        }
        $registrar = new \Magento\Framework\Component\ComponentRegistrar();
        $this->moduleNameToPath = $registrar->getPaths(\Magento\Framework\Component\ComponentRegistrar::MODULE);
        $this->moduleNameToComposerName = $this->buildModuleNameToComposerName($this->moduleNameToPath);
        $this->flattenedDependencies = $this->buildComposerDependencyList();

        $testErrors = [];
        foreach ($testObjects as $testObject) {
            // Find testobject's module
            $allFiles = explode(",", $testObject->getFilename());
            $basefile = $allFiles[0];
            $modulePath = dirname(dirname(dirname(dirname($basefile))));
            $moduleFullName = array_search($modulePath, $this->moduleNameToPath) ?? null;

            // Not a module, is either dev/tests/acceptance or loose folder with test materials
            if ($moduleFullName == null) {
                continue;
            }

            // Force Test to resolve references
            $testObject->getOrderedActions();

            // Find objects test referenced
            $dataReferences = DataObjectHandler::getInstance()->getAccessedObjects();
            $sectionReferences = SectionObjectHandler::getInstance()->getAccessedObjects();
            $pageReferences = PageObjectHandler::getInstance()->getAccessedObjects();
            $actionGroupReferences = ActionGroupObjectHandler::getInstance()->getAccessedObjects();

            // If test extends, find reference
            $testReferences = [];
            if ($testObject->getParentName() !== null) {
                $testReferences[] = TestObjectHandler::getInstance()->getObject($testObject->getParentName());
            }

            // Build list dependencies from test references
            $modulesReferencedInTest = array_merge(
                $this->getModuleDependenciesFromReferences($dataReferences),
                $this->getModuleDependenciesFromReferences($sectionReferences),
                $this->getModuleDependenciesFromReferences($pageReferences),
                $this->getModuleDependenciesFromReferences($actionGroupReferences),
                $this->getModuleDependenciesFromReferences($testReferences)
            );

            // Unset ref to current module, will not appear in dependencies ever.
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
                // Find offending elements
                $allReferences = array_merge(
                    $dataReferences,
                    $sectionReferences,
                    $pageReferences,
                    $actionGroupReferences,
                    $testReferences
                );
                $referenceErrors = $this->matchReferencesToMissingDependecies($allReferences, $missingDependencies);

                // Build error output
                $errorOutput = "\nTest \"{$testObject->getName()}\"";
                $errorOutput .= " in {$basefile} contains references to following modules:\n\t\t";
                foreach ($missingDependencies as $missingDependency) {
                    $errorOutput .= "\n\t{$missingDependency}";
                    foreach ($referenceErrors[$missingDependency] as $entityName => $filename) {
                        $errorOutput .= "\n\t\t {$entityName} from {$filename}";
                    }
                }
                $testErrors[$testObject->getName()][] = $errorOutput;
            }
            $this->clearHandlerObjectCache();
        }
        //print all errors to file
        return $this->printErrorsToFile($testErrors);
    }

    /**
     * Builds and returns array of FullModuleNae => composer name
     * @param array $array
     * @return array
     */
    private function buildModuleNameToComposerName($array)
    {
        $returnList = [];
        foreach ($array as $moduleName => $path) {
            $composerData = json_decode(file_get_contents($path . DIRECTORY_SEPARATOR . "composer.json"));
            $returnList[$moduleName] = $composerData->name;
        }
        return $returnList;
    }

    /**
     * Builds and returns flattened dependency list based on composer dependencies
     * @return array
     */
    private function buildComposerDependencyList()
    {
        $flattenedDependencies = [];

        foreach ($this->moduleNameToPath as $moduleName => $pathToModule) {
            $composerData = json_decode(file_get_contents($pathToModule . DIRECTORY_SEPARATOR . "composer.json"), true);
            $this->allDependencies[$moduleName] = $composerData['require'];
        }
        foreach ($this->allDependencies as $moduleName => $dependencies) {
            $this->alreadyExtractedDependencies = [];
            $flattenedDependencies[$moduleName] = $this->extractSubDependencies($moduleName);
        }
        return $flattenedDependencies;
    }

    /**
     * Recursive function to fetch dependencies of given dependency, and its child dependencies
     * @param string $subDependencyName
     * @return array
     */
    private function extractSubDependencies($subDependencyName)
    {
        $flattenedArray = [];

        if (array_search($subDependencyName, $this->alreadyExtractedDependencies) !== false) {
            return $flattenedArray;
        }

        if (isset($this->allDependencies[$subDependencyName])) {
            $subDependencyArray = $this->allDependencies[$subDependencyName];
            $flattenedArray = array_merge($flattenedArray, $this->allDependencies[$subDependencyName]);

            // Keep track of dependencies that have already been used, prevents circular dependency problems
            $this->alreadyExtractedDependencies[] = $subDependencyName;
            foreach ($subDependencyArray as $composerDependencyName => $version) {
                $subDependencyFullName = array_search($composerDependencyName, $this->moduleNameToComposerName);
                $flattenedArray = array_merge(
                    $flattenedArray,
                    $this->extractSubDependencies($subDependencyFullName)
                );
            }
        }
        return $flattenedArray;
    }

    /**
     * Finds unique array ofcomposer dependencies of given testObjects
     * @param array $array
     * @return array
     */
    private function getModuleDependenciesFromReferences($array)
    {
        $filenames = [];
        foreach ($array as $item) {
            // Should it append ALL filenames, including merges?
            $allFiles = explode(",", $item->getFilename());
            $basefile = $allFiles[0];
            $modulePath = dirname(dirname(dirname(dirname($basefile))));
            $fullModuleName = array_search($modulePath, $this->moduleNameToPath);
            $composerModuleName = $this->moduleNameToComposerName[$fullModuleName];
            $filenames[$composerModuleName] = $composerModuleName;
        }
        return $filenames;
    }

    /**
     * Matches references given to list of missing dependencies, returning array of ReferenceName => filename
     * @param array $allReferences
     * @param array $missingDependencies
     * @return array
     */
    private function matchReferencesToMissingDependecies($allReferences, $missingDependencies)
    {
        $referenceErrors = [];
        foreach ($allReferences as $reference) {
            $allFiles = explode(",", $reference->getFilename());
            $basefile = $allFiles[0];
            $modulePath = dirname(dirname(dirname(dirname($basefile))));
            $fullModuleName = array_search($modulePath, $this->moduleNameToPath);
            $composerModuleName = $this->moduleNameToComposerName[$fullModuleName];
            if (array_search($composerModuleName, $missingDependencies) !== false) {
                $referenceErrors[$composerModuleName][$reference->getName()] = $basefile;
            }
        }
        return $referenceErrors;
    }

    /**
     * Prints out given errors to file, and returns summary result string
     * @param array $errors
     * @return string
     */
    private function printErrorsToFile($errors)
    {
        if (empty($errors)) {
            return "No Test Dependency errors found.";
        }
        $outputPath = getcwd() . DIRECTORY_SEPARATOR . "mftf-dependency-checks.txt";
        $fileResource = fopen($outputPath, 'w');
        $header = "MFTF Test Dependency Check:\n";
        fwrite($fileResource, $header);
        foreach ($errors as $test => $error) {
            fwrite($fileResource, $error[0] . PHP_EOL);
        }
        fclose($fileResource);
        $errorCount = count($errors);
        $output = "Test Dependency errors found across {$errorCount} test(s). Error details output to {$outputPath}";
        return $output;
    }
    /**
     * Clears all handler's accessed object cache.
     * @throws \Magento\FunctionalTestingFramework\Exceptions\XmlException
     * @return void
     */
    private function clearHandlerObjectCache()
    {
        DataObjectHandler::getInstance()->clearAccessedObjects();
        SectionObjectHandler::getInstance()->clearAccessedObjects();
        PageObjectHandler::getInstance()->clearAccessedObjects();
        TestObjectHandler::getInstance()->clearAccessedObjects();
        ActionGroupObjectHandler::getInstance()->clearAccessedObjects();
    }
}
