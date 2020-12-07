<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Util;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Util\Logger\MftfLogger;
use Monolog\Handler\TestHandler;
use PHPUnit\Framework\Assert;

class TestLoggingUtil extends Assert
{
    /**
     * @var TestLoggingUtil
     */
    private static $instance;

    /**
     * @var TestHandler
     */
    private $testLogHandler;

    /**
     * TestLoggingUtil constructor.
     */
    private function __construct()
    {
        // private constructor
    }

    /**
     * Static singleton get function
     *
     * @return TestLoggingUtil
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new TestLoggingUtil();
        }

        return self::$instance;
    }

    /**
     * Function which sets a mock instance of the logger for testing purposes.
     *
     * @return void
     */
    public function setMockLoggingUtil()
    {
        $this->testLogHandler = new TestHandler();
        $testLogger = new MftfLogger('testLogger');
        $testLogger->pushHandler($this->testLogHandler);
        $mockLoggingUtil = AspectMock::double(
            LoggingUtil::class,
            ['getLogger' => $testLogger]
        )->make();
        $property = new \ReflectionProperty(LoggingUtil::class, 'instance');
        $property->setAccessible(true);
        $property->setValue($mockLoggingUtil);
    }

    public function validateMockLogEmpty()
    {
        $records = $this->testLogHandler->getRecords();
        $this->assertTrue(empty($records));
    }

    /**
     * Function which validates messages have been logged as intended during test execution.
     *
     * @param string $type
     * @param string $message
     * @param array $context
     * @return void
     */
    public function validateMockLogStatement($type, $message, $context)
    {
        $records = $this->testLogHandler->getRecords();
        $record = $records[count($records)-1]; // we assume the latest record is what requires validation
        $this->assertEquals(strtoupper($type), $record['level_name']);
        $this->assertEquals($message, $record['message']);
        $this->assertEquals($context, $record['context']);
    }

    public function validateMockLogStatmentRegex($type, $regex, $context)
    {
        $records = $this->testLogHandler->getRecords();
        $record = $records[count($records)-1]; // we assume the latest record is what requires validation
        $this->assertEquals(strtoupper($type), $record['level_name']);
        $this->assertMatchesRegularExpression($regex, $record['message']);
        $this->assertEquals($context, $record['context']);
    }

    /**
     * Function which clears the test logger context from the LogginUtil class. Should be run after a test class has
     * executed.
     *
     * @return void
     */
    public function clearMockLoggingUtil()
    {
        AspectMock::clean(LoggingUtil::class);
    }
}
