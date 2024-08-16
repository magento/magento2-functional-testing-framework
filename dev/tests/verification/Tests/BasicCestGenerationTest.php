<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class BasicCestGenerationTest extends MftfTestCase
{
    /**
     * BasicFunctionalTest:
     * Tests flat generation of a hardcoded test file with no external references.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testBasicGeneration()
    {
        $this->generateAndCompareTest('BasicFunctionalTest');
    }

    /**
     * MergeMassViaInsertAfter:
     * Tests flat generation of a hardcoded test file with no external references.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMergeMassViaInsertAfter()
    {
        $this->generateAndCompareTest('MergeMassViaInsertAfter');
    }

    /**
     * MergeMassViaInsertBefore:
     * Tests flat generation of a hardcoded test file with no external references.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMergeMassViaInsertBefore()
    {
        $this->generateAndCompareTest('MergeMassViaInsertBefore');
    }

    /**
     * Tests flat generation of a hardcoded test file with no external references and with XML comments in:
     * - root `tests` element
     * - test body
     * - test before and after blocks
     * - annotations block
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testWithXmlComments()
    {
        $this->generateAndCompareTest('XmlCommentedTest');
    }

    /**
     * Tests magentoCLI and magentoCLISecret commands with env 'MAGENTO_CLI_WAIT_TIMEOUT' set
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMagentoCli()
    {
        putenv("MAGENTO_CLI_WAIT_TIMEOUT=45");
        $this->generateAndCompareTest('MagentoCliTest');
        putenv("MAGENTO_CLI_WAIT_TIMEOUT");
    }
}
