<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

class BasicCestGenerationTest extends TestCase
{
    const BASIC_FUNCTIONAL_TEST = 'BasicFunctionalTest';
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests flat generation of a hardcoded test file with no external references.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testBasicGeneration()
    {
        $testObject = TestObjectHandler::getInstance()->getObject(self::BASIC_FUNCTIONAL_TEST);
        $test = TestGenerator::getInstance(null, [$testObject]);
        $test->createAllTestFiles();

        $cestFile = $test->getExportDir() .
            DIRECTORY_SEPARATOR .
            $testObject->getCodeceptionName() .
            ".php";

        $this->assertTrue(file_exists($cestFile));

        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . $testObject->getName() . ".txt",
            $cestFile
        );
    }
}
