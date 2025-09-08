<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Util\Path;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

interface FormatterInterface
{
    /**
     * Return formatted path (file path, url, etc) from input string, or false on error.
     *
     * @param string  $input
     * @param boolean $withTrailingSeparator
     *
     * @return string
     * @throws TestFrameworkException
     */
    public static function format(string $input, bool $withTrailingSeparator = true): string;
}
