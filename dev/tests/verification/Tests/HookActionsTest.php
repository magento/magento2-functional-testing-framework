<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;

class HookActionsTest extends TestCase
{
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests flat generation of a hardcoded test file with no external references.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testHookGenerationWithPersistedDeclarations()
    {
        $testName = 'HookActionsTest';
        $test = TestObjectHandler::getInstance()->getObject($testName);
        $testHandler = TestGenerator::getInstance(null, [$test]);
        $testHandler->createAllTestFiles();

        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . $testName . ".txt",
            $testHandler->getExportDir() . DIRECTORY_SEPARATOR . $test->getCodeceptionName() . ".php"
        );
    }
}
