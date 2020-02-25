<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Filesystem;

class RecursiveGlobUtil
{
    /**
     * Recursively glob full pathnames matching a pattern in a given directory
     *
     * @param string $pattern
     * @param string $directory
     * @return array
     */
    public static function glob($pattern, $directory)
    {
        $directory = realpath($directory);
        if ($directory === false) {
            return [];
        }
        $pattern = DIRECTORY_SEPARATOR . ltrim($pattern, DIRECTORY_SEPARATOR);
        $subDirectoryPattern = DIRECTORY_SEPARATOR . "*";

        $fileList = [];
        foreach (glob($directory . $subDirectoryPattern, GLOB_ONLYDIR) as $dir) {
            $fileList = array_merge_recursive($fileList, self::glob($pattern, $dir));
        }

        $curFiles = glob($directory . $pattern);
        if ($curFiles !== false && !empty($curFiles)) {
            $fileList = array_merge_recursive($fileList, $curFiles);
        }
        return $fileList;
    }

    /**
     * Recursive glob full pathnames matching a pattern in a given directory at certain depths
     *
     * @param string  $pattern
     * @param string  $directory
     * @param integer $depth
     * @return array
     */
    public static function globAtDepth($pattern, $directory, $depth)
    {
        $directory = realpath($directory);
        if ($directory === false) {
            return [];
        }
        $pattern = DIRECTORY_SEPARATOR . ltrim($pattern, DIRECTORY_SEPARATOR);
        $subDirectoryPattern = DIRECTORY_SEPARATOR . "*";

        $fileList = [];
        if ($depth > 0) {
            foreach (glob($directory . $subDirectoryPattern, GLOB_ONLYDIR) as $dir) {
                $fileList = array_merge_recursive(
                    $fileList,
                    self::globAtDepth($pattern, $dir, $depth-1)
                );
            }
        } elseif ($depth == 0) {
            $fileList = glob($directory . $pattern);
            if ($fileList === false) {
                $fileList = [];
            }
        }
        return $fileList;
    }
}
