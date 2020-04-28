<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Extension;

use tests\unit\Util\MagentoTestCase;
use Magento\FunctionalTestingFramework\Extension\BrowserLogUtil;

class BrowserLogUtilTest extends MagentoTestCase
{
    public function testGetLogsOfType()
    {
        $entryOne = [
            "level" => "WARNING",
            "message" => "warningMessage",
            "source" => "console-api",
            "timestamp" => 1234567890
        ];
        $entryTwo = [
            "level" => "ERROR",
            "message" => "errorMessage",
            "source" => "other",
            "timestamp" => 1234567890
        ];
        $entryThree = [
            "level" => "LOG",
            "message" => "logMessage",
            "source" => "javascript",
            "timestamp" => 1234567890
        ];
        $log = [
            $entryOne,
            $entryTwo,
            $entryThree
        ];

        $actual = BrowserLogUtil::getLogsOfType($log, 'console-api');

        self::assertEquals($entryOne, $actual[0]);
    }

    public function testFilterLogsOfType()
    {
        $entryOne = [
            "level" => "WARNING",
            "message" => "warningMessage",
            "source" => "console-api",
            "timestamp" => 1234567890
        ];
        $entryTwo = [
            "level" => "ERROR",
            "message" => "errorMessage",
            "source" => "other",
            "timestamp" => 1234567890
        ];
        $entryThree = [
            "level" => "LOG",
            "message" => "logMessage",
            "source" => "javascript",
            "timestamp" => 1234567890
        ];
        $log = [
            $entryOne,
            $entryTwo,
            $entryThree
        ];

        $actual = BrowserLogUtil::filterLogsOfType($log, 'console-api');

        self::assertEquals($entryTwo, $actual[0]);
        self::assertEquals($entryThree, $actual[1]);
    }
}
