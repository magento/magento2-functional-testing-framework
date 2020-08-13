<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\StaticCheck\DeprecatedEntityUsageCheck;
use Magento\FunctionalTestingFramework\StaticCheck\StaticChecksList;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;

class DeprecationStaticCheckTest extends TestCase
{
    const STATIC_RESULTS_DIR  = TESTS_MODULE_PATH .
    DIRECTORY_SEPARATOR .
    '_output' .
    DIRECTORY_SEPARATOR .
    'static-results';

    const LOG_FILE = self::STATIC_RESULTS_DIR .
    DIRECTORY_SEPARATOR .
    DeprecatedEntityUsageCheck::ERROR_LOG_FILENAME .
    '.txt';

    const TEST_MODULE_PATH = TESTS_MODULE_PATH .
    DIRECTORY_SEPARATOR .
    'DeprecationCheckModule'.
    DIRECTORY_SEPARATOR;

    const RESOURCES_PATH =   TESTS_MODULE_PATH .
    DIRECTORY_SEPARATOR .
    "Resources" .
    DIRECTORY_SEPARATOR .
    'StaticChecks';

    public static function setUpBeforeClass(): void
    {
        // remove static-results if it exists
        if (file_exists(self::STATIC_RESULTS_DIR)) {
            DirSetupUtil::rmdirRecursive(self::STATIC_RESULTS_DIR);
        }
    }

    /**
     * test static-check DeprecatedEntityUsageCheck.
     *
     * @throws \Exception
     */
    public function testDeprecatedEntityUsageCheck()
    {
        $staticCheck = new DeprecatedEntityUsageCheck();

        $input = $this->mockInputInterface(self::TEST_MODULE_PATH);
        AspectMock::double(StaticChecksList::class, ['getErrorFilesPath' => self::STATIC_RESULTS_DIR]);

        /** @var InputInterface $input */
        $staticCheck->execute($input);

        $this->assertTrue(file_exists(self::LOG_FILE));
        $this->assertFileEquals(
            self::RESOURCES_PATH.
            DIRECTORY_SEPARATOR .
            DeprecatedEntityUsageCheck::ERROR_LOG_FILENAME .
            ".txt",
            self::LOG_FILE
        );
    }

    /**
     * Sets input interface
     * @param $path
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    public function mockInputInterface($path)
    {
        $input = $this->getMockBuilder(InputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $input->method('getOption')
            ->with('path')
            ->willReturn($path);
        return $input;
    }

    public function tearDown(): void
    {
        DirSetupUtil::rmdirRecursive(self::STATIC_RESULTS_DIR);
    }
}
