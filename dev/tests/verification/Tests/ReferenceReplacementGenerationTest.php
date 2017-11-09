<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\CestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;
use tests\verification\Util\FileDiffUtil;

class ReferenceReplacementGenerationTest extends TestCase
{
    const DATA_REPLACEMENT_CEST = 'DataReplacementCest';
    const PERSISTED_REPLACEMENT_CEST = 'PersistedReplacementCest';
    const PAGE_REPLACEMENT_CEST = 'PageReplacementCest';
    const SECTION_REPLACEMENT_CEST = 'SectionReplacementCest';
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests replacement of {{data.key}} references.
     */
    public function testDataReferenceReplacementCest()
    {
        $this->runComparisonTest(self::DATA_REPLACEMENT_CEST);
    }

    /**
     * Tests replacement of $data.key$ references.
     */
    public function testPersistedeferenceReplacementCest()
    {
        $this->runComparisonTest(self::PERSISTED_REPLACEMENT_CEST);
    }

    /**
     * Tests replacement of {{page.url}} references. Includes parameterized urls.
     */
    public function testPageReferenceReplacementCest()
    {
        $this->runComparisonTest(self::PAGE_REPLACEMENT_CEST);
    }

    /**
     * Tests replacement of {{Section.Element}} references. Includes parameterized elements.
     */
    public function testSectionReferenceReplacementCest()
    {
        $this->runComparisonTest(self::SECTION_REPLACEMENT_CEST);
    }

    /**
     * Instantiates CestObjectHandler and TestGenerator, then compares given cest against flat txt equivalent.
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
