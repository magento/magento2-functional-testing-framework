<?php

namespace Magento\FunctionalTestingFramework\Util\Filesystem;

use FilesystemIterator;
use RecursiveDirectoryIterator;

class DirSetupUtil
{
    /**
     * Method used to clean export dir if needed and create new empty export dir.
     *
     * @return void
     */
    public static function createGroupDir($fullPath)
    {
        if (file_exists($fullPath)) {
            self::rmDirRecursive($fullPath);
        }

        mkdir($fullPath, 0777, true);
    }

    /**
     * Takes a directory path and recursively deletes all files and folders.
     *
     * @param string $directory
     * @return void
     */
    private static function rmdirRecursive($directory)
    {
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