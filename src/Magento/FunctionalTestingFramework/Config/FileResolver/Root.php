<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Config\FileResolver;

use Magento\FunctionalTestingFramework\Config\FileResolverInterface;
use Magento\FunctionalTestingFramework\Util\Iterator\File;

class Root implements FileResolverInterface
{

    /**
     * Retrieve the list of configuration files with given name that relate to specified scope at the tests level
     *
     * @param string $filename
     * @param string $scope
     * @return array|\Iterator,\Countable
     */
    public function get($filename, $scope)
    {
        $paths = glob(dirname(TESTS_BP) . DIRECTORY_SEPARATOR . $scope . DIRECTORY_SEPARATOR . $filename);

        return new File($paths);
    }
}
