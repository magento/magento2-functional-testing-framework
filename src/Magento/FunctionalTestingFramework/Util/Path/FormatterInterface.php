<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Path;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

interface FormatterInterface
{
    /**
     * Return formatted path (file path, url, etc) from input string, or false on error
     *
     * @param string  $input
     * @param boolean $withTrailingSeparator
     * @return string
     * @throws TestFrameworkException
     */
    public static function format($input, $withTrailingSeparator = true);
}
