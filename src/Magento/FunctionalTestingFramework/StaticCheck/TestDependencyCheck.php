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
use Magento\FunctionalTestingFramework\Util\Script\TestDependencyUtil;

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
     * @var TestDependencyUtil
     */
    private $testDependencyUtil;

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
        $this->testDependencyUtil = new TestDependencyUtil();
        $allModules = $this->scriptUtil->getAllModulePaths();

        if (!class_exists('\Magento\Framework\Component\ComponentRegistrar')) {
            throw new TestFrameworkException(
                "TEST DEPENDENCY CHECK ABORTED: MFTF must be attached or pointing to Magento codebase."
            );
        }
        $registrar = new \Magento\Framework\Component\ComponentRegistrar();
        $this->moduleNameToPath = $registrar->getPaths(\Magento\Framework\Component\ComponentRegistrar::MODULE);
        $this->moduleNameToComposerName = $this->testDependencyUtil->buildModuleNameToComposerName(
            $this->moduleNameToPath
        );
        $this->flattenedDependencies = $this->testDependencyUtil->buildComposerDependencyList(
            $this->moduleNameToPath,
            $this->moduleNameToComposerName
        );

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
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Return string of a short human readable result of the check. For example: "No Dependency errors found."
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * Finds all reference errors in given set of files
     * @param Finder $files
     * @return array
     * @throws XmlException
     */
    private function findErrorsInFileSet(Finder $files): array
    {
        $testErrors = [];
        foreach ($files as $filePath) {
            $this->allEntities = [];
            $moduleName = $this->testDependencyUtil->getModuleName($filePath, $this->moduleNameToPath);
            // Not a module, is either dev/tests/acceptance or loose folder with test materials
            if ($moduleName === null) {
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
    private function findViolatingReferences(string $moduleName): array
    {
        // Find Violations
        $violatingReferences = [];
        $currentModule = $this->moduleNameToComposerName[$moduleName];
        $modulesReferencedInTest = $this->testDependencyUtil->getModuleDependenciesFromReferences(
            $this->allEntities,
            $this->moduleNameToComposerName,
            $this->moduleNameToPath
        );
        $moduleDependencies = $this->flattenedDependencies[$moduleName];
        foreach ($modulesReferencedInTest as $entityName => $files) {
            $valid = false;
            foreach ($files as $module) {
                if (array_key_exists($module, $moduleDependencies) || $module === $currentModule) {
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
     * @param  array $violatingReferences
     * @return array
     */
    private function setErrorOutput(array $violatingReferences, $path): array
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
}
