<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

class ReferenceReplacementGenerationTest extends TestCase
{
    const DATA_REPLACEMENT_TEST = 'DataReplacementTest';
    const PERSISTED_REPLACEMENT_TEST = 'PersistedReplacementTest';
    const PAGE_REPLACEMENT_TEST = 'PageReplacementTest';
    const ADMIN_PAGE_TEST = 'AdminPageTest';
    const SECTION_REPLACEMENT_TEST = 'SectionReplacementTest';
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests replacement of {{data.key}} references.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testDataReferenceReplacementCest()
    {
        $this->runComparisonTest(self::DATA_REPLACEMENT_TEST);
    }

    /**
     * Tests replacement of $data.key$ references.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testPersistedeferenceReplacementCest()
    {
        $this->runComparisonTest(self::PERSISTED_REPLACEMENT_TEST);
    }

    /**
     * Tests replacement of {{page.url}} references. Includes parameterized urls.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testPageReferenceReplacementCest()
    {
        $this->runComparisonTest(self::PAGE_REPLACEMENT_TEST);
    }

    /**
     * Tests replacement of {{page.url}} reference for external page and incompatible action
     */
    public function testExternalPageBadReference()
    {
        $this->expectException(TestReferenceException::class);
        $this->runComparisonTest("ExternalPageTestBadReference");
    }

    /**
     * Tests replacement of {{Section.Element}} references. Includes parameterized elements.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testSectionReferenceReplacementCest()
    {
        $this->runComparisonTest(self::SECTION_REPLACEMENT_TEST);
    }

    /**
     * Instantiates TestObjectHandler and TestGenerator, then compares given test against flat txt equivalent.
     * @param string $testName
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    private function runComparisonTest($testName)
    {
        $testObject = TestObjectHandler::getInstance()->getObject($testName);
        $test = TestGenerator::getInstance(null, [$testObject]);
        $test->createAllTestFiles();

        $testFile = $test->getExportDir() .
            DIRECTORY_SEPARATOR .
            $testObject->getCodeceptionName() .
            ".php";

        $this->assertTrue(file_exists($testFile));

        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . $testName . ".txt",
            $testFile
        );
    }
}
