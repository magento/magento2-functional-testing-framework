<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Config\FileResolver;

use Magento\FunctionalTestingFramework\Config\FileResolverInterface;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Iterator\File;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;

class Root extends Module
{
    const ROOT_SUITE_DIR = "tests/_suite";

    /**
     * Retrieve the list of configuration files with given name that relate to specified scope at the root level as well
     * as any extension based suite configuration.
     *
     * @param string $filename
     * @param string $scope
     * @return array|\Iterator,\Countable
     * @throws TestFrameworkException
     */
    public function get($filename, $scope)
    {
        // first pick up the root level test suite dir
        $paths = glob(
            FilePathFormatter::format(TESTS_BP) . self::ROOT_SUITE_DIR
            . DIRECTORY_SEPARATOR . $filename
        );

        // then merge this path into the module based paths
        // Since we are sharing this code with Module based resolution we will unncessarily glob against modules in the
        // dev/tests dir tree, however as we plan to migrate to app/code this will be a temporary unneeded check.
        $paths = array_merge($paths, $this->getPaths($filename, $scope));

        // create and return the iterator for these file paths
        $iterator = new File($paths);
        return $iterator;
    }
}
