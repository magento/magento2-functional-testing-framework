<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Util\Path;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

class FilePathFormatter implements FormatterInterface
{
    /**
     * Return formatted full file path from input string, or false on error.
     *
     * @param string  $path
     * @param boolean $withTrailingSeparator
     *
     * @return string
     * @throws TestFrameworkException
     */
    public static function format(string $path, bool $withTrailingSeparator = true): string
    {
        $validPath = realpath($path);

        if ($validPath) {
            return $withTrailingSeparator ? $validPath . DIRECTORY_SEPARATOR : $validPath;
        }

        throw new TestFrameworkException("Invalid or non-existing file: $path\n");
    }
}
