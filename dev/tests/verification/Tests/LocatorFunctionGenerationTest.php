<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\CestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

class LocatorFunctionGenerationTest extends TestCase
{
    const LOCATOR_FUNCTION_CEST = 'LocatorFunctionCest';
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests generation of actions using elements that have a LocatorFunction.
     */
    public function testLocatorFunctionGeneration()
    {
        $cest = CestObjectHandler::getInstance()->getObject(self::LOCATOR_FUNCTION_CEST);
        $test = TestGenerator::getInstance(null, [$cest]);
        $test->createAllCestFiles();

        $cestFile = $test->getExportDir() .
            DIRECTORY_SEPARATOR .
            self::LOCATOR_FUNCTION_CEST .
            ".php";

        $this->assertTrue(file_exists($cestFile));

        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . self::LOCATOR_FUNCTION_CEST . ".txt",
            $cestFile
        );
    }
}
