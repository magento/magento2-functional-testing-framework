<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Filesystem;

use FilesystemIterator;
use RecursiveDirectoryIterator;

class DirSetupUtil
{
    /**
     * Array which will track any previously cleared directories, to prevent any unintended removal.
     *
     * @var array
     */
    private static $DIR_CONTEXT = [];

    /**
     * Method used to clean export dir if needed and create new empty export dir.
     *
     * @param string $fullPath
     * @return void
     */
    public static function createGroupDir($fullPath)
    {
        //prevent redundant calls to these directories
        $sanitizedPath = rtrim($fullPath, DIRECTORY_SEPARATOR);
        // make sure we haven't already cleaned up this directory at any point before deletion
        if (in_array($sanitizedPath, self::$DIR_CONTEXT)) {
            return;
        }

        if (file_exists($sanitizedPath)) {
            self::rmDirRecursive($sanitizedPath);
        }

        mkdir($sanitizedPath, 0777, true);
        self::$DIR_CONTEXT[] = $sanitizedPath;
    }

    /**
     * Takes a directory path and recursively deletes all files and folders.
     *
     * @param string $directory
     * @return void
     */
    public static function rmdirRecursive($directory)
    {
        if (!is_dir($directory)) {
            return;
        }

        $it = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);

        while ($it->valid()) {
            $path = $directory . DIRECTORY_SEPARATOR . $it->getFilename();
            if ($it->isDir()) {
                self::rmDirRecursive($path);
            } else {
                unlink($path);
            }

            $it->next();
        }

        rmdir($directory);
    }
}
