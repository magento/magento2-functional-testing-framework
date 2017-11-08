<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Exceptions\XmlException;

class TestNameValidationUtil
{
    const BLACKLISTED_CHAR = [
        " " => "spaces",
        "," => "commas",
        "'" => "single quotes",
        "\"" => "double quotes",
        "{" => "curly braces",
        "}" => "curly braces",
        "$" => "dollar signs",
        "(" => "parenthesis",
        ")" => "parenthesis"
    ];

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

        $diff = array_intersect($testChars, array_keys(self::BLACKLISTED_CHAR));
        if (count($diff) > 0) {
            $errorMessage = "Test name \"${testName}\" contains illegal characters, please fix and re-run.";
            $uniqueDiff = array_unique(array_map(['self', 'nameMapper'], $diff));

            foreach ($uniqueDiff as $diffChar) {
                $errorMessage .= "\nTest names cannot contain " . $diffChar;
            }

            throw new XmlException($errorMessage);
        }
    }

    /**
     * Function which maps the blacklisted char to its name, function is used by the array map above.
     *
     * @param string $val
     * @return string
     */
    private static function nameMapper($val)
    {
        return self::BLACKLISTED_CHAR[$val];
    }
}
