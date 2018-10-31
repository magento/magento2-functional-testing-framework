<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Validation;

use Magento\FunctionalTestingFramework\Exceptions\XmlException;

class NameValidationUtil
{
    const PHP_CLASS_REGEX_PATTERN = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';

    /**
     * Function which runs a validation against the blacklisted char defined in this class. Validation occurs to insure
     * allure report does not error/future devOps builds do not error against illegal char.
     *
     * @param string $name
     * @param string $type
     * @return void
     * @throws XmlException
     */
    public static function validateName($name, $type)
    {
        $startingPos = 0;
        $illegalCharArray = [];
        $nameToEvaluate = $name;

        while ($startingPos < strlen($nameToEvaluate)) {
            $startingPos++;
            $partialName = substr($nameToEvaluate, 0, $startingPos);
            $valid = boolval(preg_match(self::PHP_CLASS_REGEX_PATTERN, $partialName));

            if (!$valid) {
                $illegalChar = str_split($partialName)[$startingPos -1];
                $illegalCharArray[] = $illegalChar;
                $nameToEvaluate = str_replace($illegalChar, "", $nameToEvaluate);
                $startingPos--;
            }
        }

        if (!empty($illegalCharArray)) {
            $errorMessage = "{$type} name \"${name}\" contains illegal characters, please fix and re-run.";

            foreach ($illegalCharArray as $diffChar) {
                $errorMessage .= "\nTest names cannot contain '{$diffChar}'";
            }

            throw new XmlException($errorMessage);
        }
    }
}
