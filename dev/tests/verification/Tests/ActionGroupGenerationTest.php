<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\CestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

class ActionGroupGenerationTest extends TestCase
{
    const ACTION_GROUP_CEST = 'ActionGroupCest';
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests flat generation of a hardcoded cest file with no external references.
     */
    public function testBasicGeneration()
    {
        $cest = CestObjectHandler::getInstance()->getObject(self::ACTION_GROUP_CEST);
        $test = TestGenerator::getInstance(null, [$cest]);
        $test->createAllCestFiles();

        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . self::ACTION_GROUP_CEST . ".txt",
            $test->getExportDir() . DIRECTORY_SEPARATOR . self::ACTION_GROUP_CEST . ".php"
        );
    }
}
