<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Exceptions\XmlException;

class TestNameValidationUtil
{
    const BLACKLISTED_CHAR = [" ", ",", "'", "\"" , "{", "}", "$", "(", ")" ];

    /**
     * Function which runs a validation against the blacklisted char defined in this class. Validation occurs to insure
     * allure report does not error/future devOps builds do not error against illegal char.
     *
     * @param string $testName
     * @return void
     * @throws XmlException
     */
    public static function validateName($testName)
    {
        $testChars = str_split($testName);

        $diff = array_diff($testChars, self::BLACKLISTED_CHAR);
        if (count($diff) != count($testChars)) {
            throw new XmlException("Test name ${testName} contains illegal characters, please fix and re-run");
        }
    }
}
