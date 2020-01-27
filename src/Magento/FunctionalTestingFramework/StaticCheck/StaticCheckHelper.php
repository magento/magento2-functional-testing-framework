<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\StaticCheck;

use Symfony\Component\Finder\Finder;

class StaticCheckHelper
{
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
     * @param array  $modulePaths
     * @param string $path
     * @return Finder
     */
    public static function buildFileList($modulePaths, $path)
    {
        $finder = new Finder();
        foreach ($modulePaths as $modulePath) {
            if (!realpath($modulePath . $path)) {
                continue;
            }
            $finder->files()->in($modulePath . $path)->name("*.xml");
        }
        return $finder->files();
    }
}
