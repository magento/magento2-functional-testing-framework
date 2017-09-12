<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Config\FileResolver;

use Magento\FunctionalTestingFramework\Util\Iterator\File;
use Magento\FunctionalTestingFramework\Config\FileResolverInterface;

/**
 * Provides the list of global configuration files.
 *
 * @internal
 */
class Primary implements FileResolverInterface
{
    /**
     * Retrieve the configuration files with given name that relate to configuration
     *
     * @param string $filename
     * @param string $scope
     * @return array
     */
    public function get($filename, $scope)
    {
        if (!$filename) {
            return [];
        }
        $scope = str_replace('\\', '/', $scope);
        return new File($this->getFilePaths($filename, $scope));
    }

    /**
     * Get list of configuration files
     *
     * @param string $filename
     * @param string $scope
     * @return array
     */
    private function getFilePaths($filename, $scope)
    {
        $paths = [];
        foreach ($this->getPathPatterns($filename, $scope) as $pattern) {
            $paths = array_merge($paths, glob($pattern));
        }
        return array_combine($paths, $paths);
    }

    /**
     * Retrieve patterns for glob function
     *
     * @param string $filename
     * @param string $scope
     * @return array
     */
    private function getPathPatterns($filename, $scope)
    {
        if (substr($scope, 0, strlen(FW_BP)) === FW_BP) {
            $patterns = [
                $scope . '/' . $filename,
                $scope . '/*/' . $filename
            ];
        } else {
            $defaultPath = dirname(dirname(dirname(dirname(__DIR__))));
            $defaultPath = str_replace('\\', '/', $defaultPath);
            $patterns = [
                $defaultPath . '/' . $scope . '/' . $filename,
                $defaultPath . '/' . $scope . '/*/' . $filename,
                FW_BP . '/' . $scope . '/' . $filename,
                FW_BP . '/' . $scope . '/*/' . $filename
            ];
        }
        return str_replace('/', DIRECTORY_SEPARATOR, $patterns);
    }
}
