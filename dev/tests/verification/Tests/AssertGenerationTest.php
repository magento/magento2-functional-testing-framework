<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

class AssertGenerationTest extends TestCase
{
    const BASIC_ASSERT_TEST = 'AssertTest';
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests assert generation.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testAssertGeneration()
    {
        $testObject = TestObjectHandler::getInstance()->getObject(self::BASIC_ASSERT_TEST);
        $test = TestGenerator::getInstance(null, [$testObject]);
        $test->createAllTestFiles();

        $testFile = $test->getExportDir() .
            DIRECTORY_SEPARATOR .
            $testObject->getCodeceptionName() .
            ".php";

        $this->assertTrue(file_exists($testFile));

        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . $testObject->getName() . ".txt",
            $testFile
        );
    }
}
