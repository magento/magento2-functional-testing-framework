<?php
 /**
  * Copyright © Magento, Inc. All rights reserved.
  * See COPYING.txt for license details.
  */

namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\CestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

class ActionGroupMergeGenerationTest extends TestCase
{
    const MERGE_FUNCTIONAL_CEST = 'MergeFunctionalCest';
    const ACTION_GROUP_FUNCTIONAL_CEST = "ActionGroupFunctionalCest";
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests flat generation of a hardcoded cest file with no external references.
     */
    public function testMergeFunctionalCest()
    {
        $this->runComparisonTest(self::MERGE_FUNCTIONAL_CEST);
    }

    /**
     * Test generation of a cest file with action group references.
     */
    public function testActionGroupFunctionalCest()
    {
        $this->runComparisonTest(self::ACTION_GROUP_FUNCTIONAL_CEST);
    }

    /**
     * Generate a Cest by name and assert that it equals the corresponding .txt source of truth
     *
     * @param string $cestName
     */
    private function runComparisonTest($cestName)
    {
        $cest = CestObjectHandler::getInstance()->getObject($cestName);
        $test = TestGenerator::getInstance(null, [$cest]);
        $test->createAllCestFiles();

        $cestFile = $test->getExportDir() .
            DIRECTORY_SEPARATOR .
            $cestName .
            ".php";

        $this->assertTrue(file_exists($cestFile));

        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . $cestName . ".txt",
            $cestFile
        );
    }
}
