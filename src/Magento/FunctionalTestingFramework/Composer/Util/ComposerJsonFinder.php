<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Composer\Util;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

/**
 * Class ComposerJsonFinder searches composer json file for possible test module code paths
 */
class ComposerJsonFinder
{
    /**
     * Find absolute paths of all composer json files in a given directory
     *
     * @param string $directory
     * @return array
     */
    public function findAllComposerJsonFiles($directory)
    {
        $directory = realpath($directory);
        $jsonPattern = DIRECTORY_SEPARATOR . "composer.json";
        $subDirectoryPattern = DIRECTORY_SEPARATOR . "*";

        $jsonFileList = glob($directory . $jsonPattern);
        if ($jsonFileList !== false && !empty($jsonFileList)) {
            return $jsonFileList;
        } else {
            $jsonFileList = [];
            foreach (glob($directory . $subDirectoryPattern, GLOB_ONLYDIR) as $dir) {
                $jsonFileList = array_merge_recursive($jsonFileList, self::findAllComposerJsonFiles($dir));
            }
            return $jsonFileList;
        }
    }

    /**
     * Find absolute paths of all composer json files in a given directory at certain depths
     *
     * @param string  $directory
     * @param integer $depth
     * @return array
     */
    public function finComposerJsonFilesAtDepth($directory, $depth)
    {
        $directory = realpath($directory);
        $jsonPattern = DIRECTORY_SEPARATOR . "composer.json";
        $subDirectoryPattern = DIRECTORY_SEPARATOR . "*";

        $jsonFileList = [];
        if ($depth > 0) {
            foreach (glob($directory . $subDirectoryPattern, GLOB_ONLYDIR) as $dir) {
                $jsonFileList = array_merge_recursive(
                    $jsonFileList,
                    self::finComposerJsonFilesAtDepth($dir, $depth-1)
                );
            }
        } elseif ($depth == 0) {
            $jsonFileList = glob($directory . $jsonPattern);
            if ($jsonFileList === false) {
                $jsonFileList = [];
            }
        }
        return $jsonFileList;
    }
}
