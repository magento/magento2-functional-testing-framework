<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\Config\FileResolver;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Iterator\File;
use Magento\FunctionalTestingFramework\Config\FileResolverInterface;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;

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
        $scope = str_replace('\\', DIRECTORY_SEPARATOR, $scope);
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
     * @throws TestFrameworkException
     */
    private function getPathPatterns($filename, $scope)
    {
        if (substr($scope, 0, strlen(FW_BP)) === FW_BP) {
            $patterns = [
                $scope . DIRECTORY_SEPARATOR . $filename,
                $scope . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . $filename
            ];
        } else {
            $defaultPath = dirname(dirname(dirname(dirname(__DIR__))));
            $defaultPath = str_replace('\\', DIRECTORY_SEPARATOR, $defaultPath);
            $patterns = [
                $defaultPath . DIRECTORY_SEPARATOR . $scope . DIRECTORY_SEPARATOR . $filename,
                $defaultPath . DIRECTORY_SEPARATOR . $scope . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR
                . $filename,
                FilePathFormatter::format(FW_BP) . $scope . DIRECTORY_SEPARATOR . $filename,
                FilePathFormatter::format(FW_BP)  . $scope . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR
                . $filename
            ];
        }
        return str_replace(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $patterns);
    }
}
