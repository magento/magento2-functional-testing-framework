<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\StaticCheck;

use Magento\FunctionalTestingFramework\Config\Data;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\Page\Handlers\PageObjectHandler;
use Magento\FunctionalTestingFramework\Page\Handlers\SectionObjectHandler;
use Magento\FunctionalTestingFramework\Page\Objects\SectionObject;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class TestDependencyCheck
 * @package Magento\FunctionalTestingFramework\StaticCheck
 */
class TestDependencyCheck implements StaticCheckInterface
{
    const EXTENDS_REGEX_PATTERN = '/extends=["\']([^\'"]*)/';
    const ACTIONGROUP_REGEX_PATTERN = '/ref=["\']([^\'"]*)/';


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

        ModuleResolver::getInstance()->getModulesPath();
        if (!class_exists('\Magento\Framework\Component\ComponentRegistrar')) {
            return "TEST DEPENDENCY CHECK ABORTED: MFTF must be attached or pointing to Magento codebase.";
        }
        $registrar = new \Magento\Framework\Component\ComponentRegistrar();
        $this->moduleNameToPath = $registrar->getPaths(\Magento\Framework\Component\ComponentRegistrar::MODULE);
        $this->moduleNameToComposerName = $this->buildModuleNameToComposerName($this->moduleNameToPath);
        $this->flattenedDependencies = $this->buildComposerDependencyList();

        $allModules = [];

        // Trim non-magento modules from search pool.
        foreach (ModuleResolver::getInstance()->getModulesPath() as $module){
            $tempModule = rtrim($module, '/Test/Mftf');
            if (array_search($tempModule, $this->moduleNameToPath)) {
                $allModules[] = $module;
            }
        }

        $testErrors = [];

        $filePaths = [
            DIRECTORY_SEPARATOR . 'Test' . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR . 'ActionGroup' . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR,
        ];
        // These files can contain references to other modules.
        $testXmlFiles = $this->buildFileList($allModules, $filePaths[0]);
        $actionGroupXmlFiles = $this->buildFileList($allModules, $filePaths[1]);
        $dataXmlFiles= $this->buildFileList($allModules, $filePaths[2]);

        $testErrors += $this->findErrorsInFileSet($testXmlFiles);
        $testErrors += $this->findErrorsInFileSet($actionGroupXmlFiles);
        $testErrors += $this->findErrorsInFileSet($dataXmlFiles);

        //print all errors to file
        return $this->printErrorsToFile($testErrors);
    }

    private function findErrorsInFileSet($files)
    {
        $testErrors = [];
        foreach ($files as $filePath) {
            $modulePath = dirname(dirname(dirname(dirname($filePath))));
            $moduleFullName = array_search($modulePath, $this->moduleNameToPath) ?? null;
            // Not a module, is either dev/tests/acceptance or loose folder with test materials
            if ($moduleFullName == null) {
                continue;
            }

            $contents = file_get_contents($filePath);
            $allEntities = [];
            preg_match_all(ActionObject::ACTION_ATTRIBUTE_VARIABLE_REGEX_PATTERN, $contents, $braceReferences);
            preg_match_all(self::ACTIONGROUP_REGEX_PATTERN, $contents, $actionGroupReferences);
            preg_match_all(self::EXTENDS_REGEX_PATTERN, $contents, $extendReferences);

            // Remove Duplicates
            $braceReferences[0] = array_unique($braceReferences[0]);
            $actionGroupReferences[1] = array_unique($actionGroupReferences[1]);
            $braceReferences[1] = array_unique($braceReferences[1]);

            foreach ($braceReferences[0] as $reference) {
                // trim `{{data.field}}` to `data`
                preg_match('/{{([^.]+)/', $reference, $entityName);
                $entity = $this->findEntity($entityName[1]);
                if ($entity !== null) {
                    $allEntities[$entity->getName()] = $entity;
                }
            }
            foreach ($actionGroupReferences[1] as $reference) {
                // find actionGroupObject
                $entity = $this->findEntity($reference);
                if ($entity !== null) {
                    $allEntities[$entity->getName()] = $entity;
                }
            }
            foreach ($extendReferences[1] as $reference) {
                // find extended object
                $entity = $this->findEntity($reference);
                if ($entity !== null) {
                    $allEntities[$entity->getName()] = $entity;
                }
            }

            $modulesReferencedInTest = $this->getModuleDependenciesFromReferences($allEntities);
            unset($modulesReferencedInTest[$this->moduleNameToComposerName[$moduleFullName]]);
            $moduleDependencies = $this->flattenedDependencies[$moduleFullName];
            $diff = array_intersect_key($modulesReferencedInTest, $moduleDependencies);

            if (count($diff) != count($modulesReferencedInTest)) {
                $missingDependencies = [];
                foreach ($modulesReferencedInTest as $module => $key) {
                    if (!array_key_exists($module, $diff)) {
                        $missingDependencies[] = $module;
                    }
                }
                $referenceErrors = $this->matchReferencesToMissingDependecies($allEntities, $missingDependencies);
                // Build error output
                $errorOutput = "\nFile \"{$filePath->getRealPath()}\"\n contains references to following modules:\n\t\t";
                foreach ($missingDependencies as $missingDependency) {
                    $errorOutput .= "\n\t{$missingDependency}";
                    foreach ($referenceErrors[$missingDependency] as $entityName => $filename) {
                        $errorOutput .= "\n\t\t {$entityName} from {$filename}";
                    }
                }
                $testErrors[$filePath->getRealPath()][] = $errorOutput;
            }
        }
        return $testErrors;
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

    private function buildFileList($modulePaths, $path)
    {
        $filesAggregate = [];
        $finder = new Finder();
        foreach ($modulePaths as $modulePath) {
            if (!realpath($modulePath . $path)) {
                continue;
            }
            $finder->files()->in($modulePath . $path)->name("*.xml");
            foreach ($finder->files() as $file) {
                $filesAggregate[] = $file->getFilename();
            }
        }
        return $finder->files();
    }

    private function findEntity($name)
    {
        if ($name == '_ENV' || $name == '_CREDS') {
            return null;
        }

        if (DataObjectHandler::getInstance()->getObject($name)) {
            return DataObjectHandler::getInstance()->getObject($name);
        } elseif (PageObjectHandler::getInstance()->getObject($name)) {
            return PageObjectHandler::getInstance()->getObject($name);
        } elseif (SectionObjectHandler::getInstance()->getObject($name)) {
            return SectionObjectHandler::getInstance()->getObject($name);
        }

        try {
            return ActionGroupObjectHandler::getInstance()->getObject($name);
        } catch (TestReferenceException $e) {
        }
        try {
            return TestObjectHandler::getInstance()->getObject($name);
        } catch (TestReferenceException $e) {
        }
        return null;
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
}
