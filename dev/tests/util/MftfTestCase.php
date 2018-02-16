<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\util;

use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

abstract class MftfTestCase extends TestCase
{
    const RESOURCES_PATH = __DIR__ . '/../verification/Resources';

    /**
     * Private function which takes a test name, generates the test and compares with a correspondingly named txt file
     * with expected contents.
     *
     * @param string $testName
     */
    public function generateAndCompareTest($testName)
    {
        $testObject = TestObjectHandler::getInstance()->getObject($testName);
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