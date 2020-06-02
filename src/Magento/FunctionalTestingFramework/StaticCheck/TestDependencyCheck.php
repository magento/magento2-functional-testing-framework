<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\StaticCheck;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;
use Exception;
use Magento\FunctionalTestingFramework\Util\Script\ScriptUtil;

/**
 * Class TestDependencyCheck
 * @package Magento\FunctionalTestingFramework\StaticCheck
 */
class TestDependencyCheck implements StaticCheckInterface
{
    const EXTENDS_REGEX_PATTERN = '/extends=["\']([^\'"]*)/';
    const ACTIONGROUP_REGEX_PATTERN = '/ref=["\']([^\'"]*)/';

    const ERROR_LOG_FILENAME = 'mftf-dependency-checks';
    const ERROR_LOG_MESSAGE = 'MFTF File Dependency Check';

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
     * Array containing all errors found after running the execute() function.
     * @var array
     */
    private $errors = [];

    /**
     * String representing the output summary found after running the execute() function.
     * @var string
     */
    private $output;

    /**
     * Array containing all entities after resolving references.
     * @var array
     */
    private $allEntities = [];

    /**
     * ScriptUtil instance
     *
     * @var ScriptUtil
     */
    private $scriptUtil;

    /**
     * Checks test dependencies, determined by references in tests versus the dependencies listed in the Magento module
     *
     * @param InputInterface $input
     * @return void
     * @throws Exception
     */
    public function execute(InputInterface $input)
    {
        $this->scriptUtil = new ScriptUtil();
        $allModules = $this->scriptUtil->getAllModulePaths();

        if (!class_exists('\Magento\Framework\Component\ComponentRegistrar')) {
            throw new TestFrameworkException(
                "TEST DEPENDENCY CHECK ABORTED: MFTF must be attached or pointing to Magento codebase."
            );
        }
        $registrar = new \Magento\Framework\Component\ComponentRegistrar();
        $this->moduleNameToPath = $registrar->getPaths(\Magento\Framework\Component\ComponentRegistrar::MODULE);
        $this->moduleNameToComposerName = $this->buildModuleNameToComposerName($this->moduleNameToPath);
        $this->flattenedDependencies = $this->buildComposerDependencyList();

        $filePaths = [
            DIRECTORY_SEPARATOR . 'Test' . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR . 'ActionGroup' . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR,
        ];
        // These files can contain references to other modules.
        $testXmlFiles = $this->scriptUtil->getModuleXmlFilesByScope($allModules, $filePaths[0]);
        $actionGroupXmlFiles = $this->scriptUtil->getModuleXmlFilesByScope($allModules, $filePaths[1]);
        $dataXmlFiles= $this->scriptUtil->getModuleXmlFilesByScope($allModules, $filePaths[2]);

        $this->errors = [];
        $this->errors += $this->findErrorsInFileSet($testXmlFiles);
        $this->errors += $this->findErrorsInFileSet($actionGroupXmlFiles);
        $this->errors += $this->findErrorsInFileSet($dataXmlFiles);

        // hold on to the output and print any errors to a file
        $this->output = $this->scriptUtil->printErrorsToFile(
            $this->errors,
            StaticChecksList::getErrorFilesPath() . DIRECTORY_SEPARATOR . self::ERROR_LOG_FILENAME . '.txt',
            self::ERROR_LOG_MESSAGE
        );
    }

    /**
     * Return array containing all errors found after running the execute() function.
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Return string of a short human readable result of the check. For example: "No Dependency errors found."
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Finds all reference errors in given set of files
     * @param Finder $files
     * @return array
     * @throws XmlException
     */
    private function findErrorsInFileSet($files)
    {
        $testErrors = [];
        foreach ($files as $filePath) {
            $this->allEntities = [];
            $moduleName = $this->getModuleName($filePath);
            // Not a module, is either dev/tests/acceptance or loose folder with test materials
            if ($moduleName == null) {
                continue;
            }

            $contents = file_get_contents($filePath);
            preg_match_all(ActionObject::ACTION_ATTRIBUTE_VARIABLE_REGEX_PATTERN, $contents, $braceReferences);
            preg_match_all(self::ACTIONGROUP_REGEX_PATTERN, $contents, $actionGroupReferences);
            preg_match_all(self::EXTENDS_REGEX_PATTERN, $contents, $extendReferences);

            // Remove Duplicates
            $braceReferences[0] = array_unique($braceReferences[0]);
            $actionGroupReferences[1] = array_unique($actionGroupReferences[1]);
            $braceReferences[1] = array_unique($braceReferences[1]);
            $braceReferences[2] = array_filter(array_unique($braceReferences[2]));

            // resolve entity references
            $this->allEntities = array_merge(
                $this->allEntities,
                $this->scriptUtil->resolveEntityReferences($braceReferences[0], $contents)
            );

            // resolve parameterized references
            $this->allEntities = array_merge(
                $this->allEntities,
                $this->scriptUtil->resolveParametrizedReferences($braceReferences[2], $contents)
            );

            // resolve entity by names
            $this->allEntities = array_merge(
                $this->allEntities,
                $this->scriptUtil->resolveEntityByNames($actionGroupReferences[1])
            );

            // resolve entity by names
            $this->allEntities = array_merge(
                $this->allEntities,
                $this->scriptUtil->resolveEntityByNames($extendReferences[1])
            );

            // Find violating references and set error output
            $violatingReferences = $this->findViolatingReferences($moduleName);
            $testErrors = array_merge($testErrors, $this->setErrorOutput($violatingReferences, $filePath));
        }
        return $testErrors;
    }

    /**
     * Find violating references
     *
     * @param string $moduleName
     * @return array
     */
    private function findViolatingReferences($moduleName)
    {
        // Find Violations
        $violatingReferences = [];
        $currentModule = $this->moduleNameToComposerName[$moduleName];
        $modulesReferencedInTest = $this->getModuleDependenciesFromReferences($this->allEntities);
        $moduleDependencies = $this->flattenedDependencies[$moduleName];
        foreach ($modulesReferencedInTest as $entityName => $files) {
            $valid = false;
            foreach ($files as $module) {
                if (array_key_exists($module, $moduleDependencies) || $module == $currentModule) {
                    $valid = true;
                    break;
                }
            }
            if (!$valid) {
                $violatingReferences[$entityName] = $files;
            }
        }

        return $violatingReferences;
    }

    /**
     * Builds and returns error output for violating references
     *
     * @param array  $violatingReferences
     * @param string $path
     * @return mixed
     */
    private function setErrorOutput($violatingReferences, $path)
    {
        $testErrors = [];

        if (!empty($violatingReferences)) {
            // Build error output
            $errorOutput = "\nFile \"{$path->getRealPath()}\"";
            $errorOutput .= "\ncontains entity references that violate dependency constraints:\n\t\t";
            foreach ($violatingReferences as $entityName => $files) {
                $errorOutput .= "\n\t {$entityName} from module(s): " . implode(", ", $files);
            }
            $testErrors[$path->getRealPath()][] = $errorOutput;
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
            $composerData = json_decode(
                file_get_contents($pathToModule . DIRECTORY_SEPARATOR . "composer.json"),
                true
            );
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
            foreach ($allFiles as $file) {
                $moduleName = $this->getModuleName($file);
                if (isset($this->moduleNameToComposerName[$moduleName])) {
                    $composerModuleName = $this->moduleNameToComposerName[$moduleName];
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
     * @return string|null
     */
    private function getModuleName($filePath)
    {
        $moduleName = null;
        foreach ($this->moduleNameToPath as $name => $path) {
            if (strpos($filePath, $path) !== false) {
                $moduleName = $name;
                break;
            }
        }
        return $moduleName;
    }
}
