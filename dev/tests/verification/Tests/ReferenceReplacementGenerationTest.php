<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use tests\util\MftfTestCase;

class ReferenceReplacementGenerationTest extends MftfTestCase
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
        $this->generateAndCompareTest(self::DATA_REPLACEMENT_TEST);
    }

    /**
     * Tests replacement of $data.key$ references.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testPersistedeferenceReplacementCest()
    {
        $this->generateAndCompareTest(self::PERSISTED_REPLACEMENT_TEST);
    }

    /**
     * Tests replacement of {{page.url}} references. Includes parameterized urls.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testPageReferenceReplacementCest()
    {
        $this->generateAndCompareTest(self::PAGE_REPLACEMENT_TEST);
    }

    /**
     * Tests replacement of {{page.url}} reference for external page and incompatible action
     */
    public function testExternalPageBadReference()
    {
        $this->expectException(TestReferenceException::class);
        $this->generateAndCompareTest("ExternalPageTestBadReference");
    }

    /**
     * Tests replacement of {{Section.Element}} references. Includes parameterized elements.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testSectionReferenceReplacementCest()
    {
        $this->generateAndCompareTest(self::SECTION_REPLACEMENT_TEST);
    }

    /**
     * Tests replacement of all characters into string literal references.
     * Used to ensure users can input everything but single quotes into 'stringLiteral' in parameterized selectors
     */
    public function testCharacterReplacementCest()
    {
        $this->generateAndCompareTest("CharacterReplacementTest");
    }
}
