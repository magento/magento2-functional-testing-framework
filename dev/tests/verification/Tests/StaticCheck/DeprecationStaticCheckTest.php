<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\StaticCheck\DeprecatedEntityUsageCheck;
use Magento\FunctionalTestingFramework\StaticCheck\StaticChecksList;
use ReflectionProperty;
use Symfony\Component\Console\Input\InputInterface;
use tests\util\MftfStaticTestCase;

class DeprecationStaticCheckTest extends MftfStaticTestCase
{
    const LOG_FILE = self::STATIC_RESULTS_DIR .
    DIRECTORY_SEPARATOR .
    DeprecatedEntityUsageCheck::ERROR_LOG_FILENAME .
    '.txt';

    const TEST_MODULE_PATH = TESTS_MODULE_PATH .
    DIRECTORY_SEPARATOR .
    'DeprecationCheckModule'.
    DIRECTORY_SEPARATOR;

    /**
     * test static-check DeprecatedEntityUsageCheck.
     *
     * @throws \Exception
     */
    public function testDeprecatedEntityUsageCheck()
    {
        $staticCheck = new DeprecatedEntityUsageCheck();

        $input = $this->mockInputInterface(self::TEST_MODULE_PATH);
        $property = new ReflectionProperty(StaticChecksList::class, 'errorFilesPath');
        $property->setAccessible(true);
        $property->setValue(self::STATIC_RESULTS_DIR);

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
     * @inheritdoc
     */
    public function tearDown(): void
    {
        $property = new ReflectionProperty(StaticChecksList::class, 'errorFilesPath');
        $property->setAccessible(true);
        $property->setValue(null);
    }
}
