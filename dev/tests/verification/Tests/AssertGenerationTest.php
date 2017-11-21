<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\CestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

class AssertGenerationTest extends TestCase
{
    const BASIC_ASSERT_CEST = 'AssertCest';
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests assert generation.
     */
    public function testAssertGeneration()
    {
        $cest = CestObjectHandler::getInstance()->getObject(self::BASIC_ASSERT_CEST);
        $test = TestGenerator::getInstance(null, [$cest]);
        $test->createAllCestFiles();

        $cestFile = $test->getExportDir() .
            DIRECTORY_SEPARATOR .
            self::BASIC_ASSERT_CEST .
            ".php";

        $this->assertTrue(file_exists($cestFile));

        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . self::BASIC_ASSERT_CEST . ".txt",
            $cestFile
        );
    }
}
