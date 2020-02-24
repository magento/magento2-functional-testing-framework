<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Util\Script;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
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
     * Builds list of all XML files in given modulePaths + path given
     * Return empty array if Finder is not run
     *
     * @param array  $modulePaths
     * @param string $path
     * @return Finder|array
     */
    public static function buildFileList($modulePaths, $path)
    {
        $finderRun = false;
        $finder = new Finder();
        foreach ($modulePaths as $modulePath) {
            if (!realpath($modulePath . $path)) {
                continue;
            }
            $finder->files()->in($modulePath . $path)->name("*.xml");
            $finderRun = true;
        }
        if ($finderRun) {
            return $finder->files();
        } else {
            return [];
        }
    }
}
