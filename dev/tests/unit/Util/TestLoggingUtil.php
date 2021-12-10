<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Util;

use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Util\Logger\MftfLogger;
use Monolog\Handler\TestHandler;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class TestLoggingUtil extends TestCase
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
     * Private constructor.
     */
    private function __construct()
    {
        parent::__construct(null, [], '');
    }

    /**
     * Static singleton get function.
     *
     * @return TestLoggingUtil
     */
    public static function getInstance(): TestLoggingUtil
    {
        if (self::$instance === null) {
            self::$instance = new TestLoggingUtil();
        }
        return self::$instance;
    }

    /**
     * Function which sets a mock instance of the logger for testing purposes.
     *
     * @return void
     */
    public function setMockLoggingUtil(): void
    {
        $this->testLogHandler = new TestHandler();
        $testLogger = new MftfLogger('testLogger');
        $testLogger->pushHandler($this->testLogHandler);

        $mockLoggingUtil = $this->createMock(LoggingUtil::class);
        $mockLoggingUtil
            ->method('getLogger')
            ->willReturn($testLogger);

        $property = new ReflectionProperty(LoggingUtil::class, 'instance');
        $property->setAccessible(true);
        $property->setValue($mockLoggingUtil);
    }

    /**
     * Check if mock log is empty.
     *
     * @return void
     */
    public function validateMockLogEmpty(): void
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
     *
     * @return void
     */
    public function validateMockLogStatement(string $type, string $message, array $context): void
    {
        $records = $this->testLogHandler->getRecords();
        $record = $records[count($records)-1]; // we assume the latest record is what requires validation
        $this->assertEquals(strtoupper($type), $record['level_name']);
        $this->assertEquals($message, $record['message']);
        $this->assertEquals($context, $record['context']);
    }

    /**
     * Check mock log statement regular expression.
     *
     * @param string $type
     * @param string $regex
     * @param array $context
     *
     * @return void
     */
    public function validateMockLogStatmentRegex(string $type, string $regex, array $context): void
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
    public function clearMockLoggingUtil(): void
    {
        $property = new ReflectionProperty(LoggingUtil::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null);
    }
}
