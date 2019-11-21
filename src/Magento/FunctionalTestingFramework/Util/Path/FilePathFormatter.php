<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Path;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

class FilePathFormatter implements FormatterInterface
{
    /**
     * Return formatted full file path from input string, or false on error
     *
     * @param string  $path
     * @param boolean $withTrailingSeparator
     * @return string
     * @throws TestFrameworkException
     */
    public static function format($path, $withTrailingSeparator = true)
    {
        $validPath = realpath($path);

        if ($validPath) {
            return $withTrailingSeparator ? $validPath . DIRECTORY_SEPARATOR : $validPath;
        }

        throw new TestFrameworkException("Invalid or non-existing file: $path\n");
    }
}
