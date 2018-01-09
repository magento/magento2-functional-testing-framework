<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

class ParameterArrayTest extends TestCase
{
    const PARAMETER_ARRAY_TEST = 'ParameterArrayTest';
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests flat generation of a hardcoded test file with no external references.
     */
    public function testParameterArrayGeneration()
    {
        $testObject = TestObjectHandler::getInstance()->getObject(self::PARAMETER_ARRAY_TEST);
        $test = TestGenerator::getInstance(null, [$testObject]);
        $test->createAllTestFiles();

        $testFile = $test->getExportDir() .
            DIRECTORY_SEPARATOR .
            $testObject->getCodeceptionName() .
            ".php";

        $this->assertTrue(file_exists($testFile));

        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . self::PARAMETER_ARRAY_TEST . ".txt",
            $testFile
        );
    }
}
