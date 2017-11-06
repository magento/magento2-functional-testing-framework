<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\CestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

class PersistedReplacementGenerationTest extends TestCase
{
    const PERSISTED_REPLACEMENT_CEST = 'PersistedReplacementCest';
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests flat generation of a hardcoded cest file with no external references.
     */
    public function testPersistedReplacementGeneration()
    {
        $cest = CestObjectHandler::getInstance()->getObject(self::PERSISTED_REPLACEMENT_CEST);
        $test = TestGenerator::getInstance(null, [$cest]);
        $test->createAllCestFiles();

        $cestFile = $test->getExportDir() .
            DIRECTORY_SEPARATOR .
            self::PERSISTED_REPLACEMENT_CEST .
            ".php";

        $this->assertTrue(file_exists($cestFile));

        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . self::PERSISTED_REPLACEMENT_CEST . ".txt",
            $cestFile
        );
    }
}
