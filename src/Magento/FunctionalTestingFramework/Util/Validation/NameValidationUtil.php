<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Validation;

use Exception;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

class NameValidationUtil
{
    const PHP_CLASS_REGEX_PATTERN = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';

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

    /**
     * Validates data entity names.
     *
     * @param string $str
     * @param string $filename
     * @throws Exception
     * @return void
     */
    public function validateDataEntityName($str, $filename = null)
    {
        if (!ctype_upper($str[0])) {
            $message = "The data entity name {$str} should be PascalCase with an uppercase first letter.";

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
     * Validates data entity key names.
     *
     * @param string $str
     * @param string $filename
     * @throws Exception
     * @return void
     */
    public function validateDataEntityKey($str, $filename = null)
    {
        if (!ctype_lower($str[0])) {
            $message = "The data entity key {$str} should be camelCase with a lowercase first letter.";

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
     * Validates metadata operation names.
     *
     * @param string $str
     * @param string $filename
     * @throws Exception
     * @return void
     */
    public function validateMetadataOperationName($str, $filename = null)
    {
        if (!ctype_upper($str[0])) {
            $message = "The metadata operation name {$str} should be PascalCase with an uppercase first letter.";

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
     * Validates page names.
     *
     * @param string $str
     * @param string $filename
     * @throws Exception
     * @return void
     */
    public function validatePageName($str, $filename = null)
    {
        $isPrefixAdmin = substr($str, 0, 5) === "Admin";
        $isPrefixStorefront = substr($str, 0, 10) === "Storefront";
        $isSuffixPage = substr($str, -4) === "Page";

        if ((!$isPrefixAdmin && !$isPrefixStorefront) || !$isSuffixPage) {
            $message = "The page name {$str} should follow the pattern {Admin or Storefront}{Description}Page.";

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
     * Validates section names.
     *
     * @param string $str
     * @param string $filename
     * @throws Exception
     * @return void
     */
    public function validateSectionName($str, $filename = null)
    {
        $isPrefixAdmin = substr($str, 0, 5) === "Admin";
        $isPrefixStorefront = substr($str, 0, 10) === "Storefront";
        $isSuffixSection = substr($str, -7) === "Section";

        if ((!$isPrefixAdmin && !$isPrefixStorefront) || !$isSuffixSection) {
            $message = "The section name {$str} should follow the pattern {Admin or Storefront}{Description}Section.";

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
     * Validates section element names.
     *
     * @param string $str
     * @param string $filename
     * @throws Exception
     * @return void
     */
    public function validateElementName($str, $filename = null)
    {
        if (!ctype_lower($str[0])) {
            $message = "The element name {$str} should be camelCase with a lowercase first letter.";

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
     * Validates action group names.
     *
     * @param string $str
     * @param string $filename
     * @throws Exception
     * @return void
     */
    public function validateActionGroupName($str, $filename = null)
    {
        if (!ctype_upper($str[0])) {
            $message = "The action group name {$str} should be PascalCase with an uppercase first letter.";

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
     * Validates test names.
     *
     * @param string $str
     * @param string $filename
     * @throws Exception
     * @return void
     */
    public function validateTestName($str, $filename = null)
    {
        if (!ctype_upper($str[0])) {
            $message = "The test name {$str} should be PascalCase with an uppercase first letter.";

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
     * @throws Exception
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
