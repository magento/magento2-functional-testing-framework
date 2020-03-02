<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Util\Script;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Filesystem\FinderUtil;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;
use Symfony\Component\Finder\Finder;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;

/**
 * ScriptUtil class that contains helper functions for static and upgrade scripts
 *
 * @package Magento\FunctionalTestingFramework\Util\Script
 */
class ScriptUtil
{
    const ROOT_SUITE_DIR = 'tests/_suite';

    /**
     * Return all installed Magento module paths
     *
     * @return array
     * @throws TestFrameworkException
     */
    public static function getAllModulePaths()
    {
        MftfApplicationConfig::create(
            true,
            MftfApplicationConfig::UNIT_TEST_PHASE,
            false,
            MftfApplicationConfig::LEVEL_NONE,
            true
        );

        return ModuleResolver::getInstance()->getModulesPath();
    }

    /**
     * Prints out given errors to file, and returns summary result string
     * @param array  $errors
     * @param string $filename
     * @param string $message
     * @return string
     */
    public static function printErrorsToFile($errors, $filename, $message)
    {
        if (empty($errors)) {
            return $message . ": No errors found.";
        }

        $outputPath = getcwd() . DIRECTORY_SEPARATOR . $filename . ".txt";
        $fileResource = fopen($outputPath, 'w');

        foreach ($errors as $test => $error) {
            fwrite($fileResource, $error[0] . PHP_EOL);
        }

        fclose($fileResource);
        $errorCount = count($errors);
        $output = $message . ": Errors found across {$errorCount} file(s). Error details output to {$outputPath}";

        return $output;
    }

    /**
     * Return all XML files for $scope in given module paths, empty array if no path is valid
     *
     * @param array  $modulePaths
     * @param string $scope
     * @return Finder|array
     */
    public static function getModuleXmlFilesByScope($modulePaths, $scope)
    {
        $found = false;
        $scopePath = DIRECTORY_SEPARATOR . ucfirst($scope) . DIRECTORY_SEPARATOR;
        $finder = new Finder();

        foreach ($modulePaths as $modulePath) {
            if (!realpath($modulePath . $scopePath)) {
                continue;
            }
            $finder->files()->followLinks()->in($modulePath . $scopePath)->name("*.xml");
            $found = true;
        }
        return $found ? $finder->files() : [];
    }

    /**
     * Return root Suite XML files, empty array if root suite file is not valid
     *
     * @return Finder|array
     * @throws TestFrameworkException
     */
    public static function getRootSuiteXmlFiles()
    {
        //$rootSuitePath = FilePathFormatter::format(TESTS_BP) . self::ROOT_SUITE_DIR;
        $rootSuitePath = FilePathFormatter::format(TESTS_BP) . 'tests/verification/_suite';
        $finder = new Finder();
        if (!realpath($rootSuitePath)) {
            return [];
        }
        $finder->files()->followLinks()->in($rootSuitePath)->name("*.xml");

        return $finder->files();
    }
}
