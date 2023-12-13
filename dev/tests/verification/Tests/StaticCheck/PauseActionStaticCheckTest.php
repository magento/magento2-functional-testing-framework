<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Exception;
use Magento\FunctionalTestingFramework\StaticCheck\PauseActionUsageCheck;
use Magento\FunctionalTestingFramework\StaticCheck\StaticChecksList;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use ReflectionProperty;
use Symfony\Component\Console\Input\InputInterface;
use tests\util\MftfStaticTestCase;
use ReflectionClass;

class PauseActionStaticCheckTest extends MftfStaticTestCase
{
    const LOG_FILE = self::STATIC_RESULTS_DIR .
    DIRECTORY_SEPARATOR .
    PauseActionUsageCheck::ERROR_LOG_FILENAME .
    '.txt';

    const TEST_MODULE_PATH = TESTS_MODULE_PATH .
    DIRECTORY_SEPARATOR .
    'PauseCheckModule'.
    DIRECTORY_SEPARATOR;

    /**
     * test static-check PauseActionUsageCheck.
     *
     * @throws Exception
     */
    public function testPauseActionUsageCheck()
    {
        $staticCheck = new PauseActionUsageCheck();

        $input = $this->mockInputInterface(self::TEST_MODULE_PATH);
        $reflectionClass = new ReflectionClass(StaticChecksList::class);
        $reflectionClass->setStaticPropertyValue('errorFilesPath', self::STATIC_RESULTS_DIR);

        /** @var InputInterface $input */
        $staticCheck->execute($input);

        $this->assertTrue(file_exists(self::LOG_FILE));
        $this->assertFileEquals(
            self::RESOURCES_PATH.
            DIRECTORY_SEPARATOR .
            PauseActionUsageCheck::ERROR_LOG_FILENAME .
            ".txt",
            self::LOG_FILE
        );
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass(): void
    {
        $reflectionClass = new ReflectionClass(StaticChecksList::class);
        $reflectionClass->setStaticPropertyValue('errorFilesPath', null);
    }
}
