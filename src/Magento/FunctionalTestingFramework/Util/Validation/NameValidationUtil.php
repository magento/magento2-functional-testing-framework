<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Validation;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

class NameValidationUtil
{
    const PHP_CLASS_REGEX_PATTERN = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';

    const DATA_ENTITY_NAME = "data entity name";
    const DATA_ENTITY_KEY = "data entity key";
    const METADATA_OPERATION_NAME = "metadata operation name";
    const PAGE = "Page";
    const SECTION = "Section";
    const SECTION_ELEMENT_NAME = "section element name";
    const ACTION_GROUP_NAME = "action group name";
    const TEST_NAME = "test name";

    /**
     * The number of violations this instance has detected.
     *
     * @var integer
     */
    private $count;

    /**
     * NameValidationUtil constructor.
     *
     */
    public function __construct()
    {
        $this->count = 0;
    }

    /**
     * Function which runs a validation against the blocklisted char defined in this class. Validation occurs to insure
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
            $errorMessage = "{$type} name \"{$name}\" contains illegal characters, please fix and re-run.";

            foreach ($illegalCharArray as $diffChar) {
                $errorMessage .= "\nTest names cannot contain '{$diffChar}'";
            }

            throw new XmlException($errorMessage);
        }
    }

    /**
     * Validates that the string is PascalCase.
     *
     * @param string $str
     * @param string $type
     * @param string $filename
     * @throws TestFrameworkException
     * @return void
     */
    public function validatePascalCase($str, $type, $filename = null)
    {
        if (!is_string($str) || !ctype_upper($str[0])) {
            $message = "The {$type} {$str} should be PascalCase with an uppercase first letter.";

            if ($filename !== null) {
                $message .= " See file {$filename}.";
            }

            LoggingUtil::getInstance()->getLogger(self::class)->notification(
                $message,
                [],
                false
            );

            $this->count++;
        }
    }

    /**
     * Validates that the string is camelCase.
     *
     * @param string $str
     * @param string $type
     * @param string $filename
     * @throws TestFrameworkException
     * @return void
     */
    public function validateCamelCase($str, $type, $filename = null)
    {
        if (!is_string($str) || !ctype_lower($str[0])) {
            $message = "The {$type} {$str} should be camelCase with a lowercase first letter.";

            if ($filename !== null) {
                $message .= " See file {$filename}.";
            }

            LoggingUtil::getInstance()->getLogger(self::class)->notification(
                $message,
                [],
                false
            );

            $this->count++;
        }
    }

    /**
     * Validates that the string is of the pattern {Admin or Storefront}{Description}{Type}.
     *
     * @param string $str
     * @param string $type
     * @param string $filename
     * @throws TestFrameworkException
     * @return void
     */
    public function validateAffixes($str, $type, $filename = null)
    {
        $isPrefixAdmin = substr($str, 0, 5) === "Admin";
        $isPrefixStorefront = substr($str, 0, 10) === "Storefront";
        $isSuffixType = substr($str, -strlen($type)) === $type;

        if ((!$isPrefixAdmin && !$isPrefixStorefront) || !$isSuffixType) {
            $message = "The {$type} name {$str} should follow the pattern {Admin or Storefront}{Description}{$type}.";

            if ($filename !== null) {
                $message .= " See file {$filename}.";
            }

            LoggingUtil::getInstance()->getLogger(self::class)->notification(
                $message,
                [],
                false
            );

            $this->count++;
        }
    }

    /**
     * Outputs the number of validations detected by this instance.
     *
     * @param string $type
     * @throws TestFrameworkException
     * @return void
     */
    public function summarize($type)
    {
        if ($this->count > 0) {
            LoggingUtil::getInstance()->getLogger(self::class)->notification(
                "{$this->count} {$type} violations detected. See mftf.log for details.",
                [],
                true
            );
        }
    }
}
