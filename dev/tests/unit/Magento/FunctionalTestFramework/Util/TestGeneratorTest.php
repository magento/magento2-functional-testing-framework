<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Util;

use AspectMock\Test as AspectMock;

use Exception;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Filter\FilterList;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use tests\unit\Util\MagentoTestCase;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use tests\unit\Util\TestLoggingUtil;
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;

class TestGeneratorTest extends MagentoTestCase
{
    public function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        AspectMock::clean();
        GenerationErrorHandler::getInstance()->reset();
    }

    /**
     * @throws Exception
     */
    public function testEntityException()
    {
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => '{{someEntity.entity}}'
        ]);

        $testObject = new TestObject("sampleTest", ["merge123" => $actionObject], [], [], "filename");
        $test = $this->createMock(TestObjectHandler::class);
        $test->expects($this->once())->method('initTestData')->willReturn('');

        $testGeneratorObject = TestGenerator::getInstance("", ["sampleTest" => $testObject]);

        $test1 = $this->createMock(TestGenerator::class);
        $test1->expects($this->once())->method('loadAllTestObjects')->willReturn([
            'sampleTest' => $testObject
        ]);

        $testGeneratorObject->createAllTestFiles(null, []);

        $errorMessage = '/' . preg_quote("Removed invalid test object sampleTest") . '/';
        TestLoggingUtil::getInstance()->validateMockLogStatmentRegex('error', $errorMessage, []);
        $testErrors = GenerationErrorHandler::getInstance()->getErrorsByType('test');
        $this->assertArrayHasKey('sampleTest', $testErrors);
    }

    /**
     * @throws TestReferenceException
     */
    public function testSkippedNoGeneration()
    {
        $actionInput = 'fakeInput';
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => $actionInput
        ]);

        $annotations = ['skip' => ['issue']];
        $testObject = new TestObject("sampleTest", ["merge123" => $actionObject], $annotations, [], "filename");

        $testGeneratorObject = TestGenerator::getInstance("", ["sampleTest" => $testObject]);
        $output = $testGeneratorObject->assembleTestPhp($testObject);

        $this->assertStringContainsString('This test is skipped', $output);
        $this->assertStringNotContainsString($actionInput, $output);
    }

    /**
     * @throws TestReferenceException
     */
    public function testAllowSkipped()
    {
        $test = $this->createMock(MftfApplicationConfig::class);
        $test->expects($this->once())->method('allowSkipped')->willReturn(true);

        $actionInput = 'fakeInput';
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => $actionInput
        ]);
        $beforeActionInput = 'beforeInput';
        $beforeActionObject = new ActionObject('beforeAction', 'comment', [
            'userInput' => $beforeActionInput
        ]);

        $annotations = ['skip' => ['issue']];
        $beforeHook = new TestHookObject("before", "sampleTest", ['beforeAction' => $beforeActionObject]);
        $testObject = new TestObject(
            "sampleTest",
            ["fakeAction" => $actionObject],
            $annotations,
            ["before" => $beforeHook],
            "filename"
        );

        $testGeneratorObject = TestGenerator::getInstance("", ["sampleTest" => $testObject]);
        $output = $testGeneratorObject->assembleTestPhp($testObject);

        $this->assertStringNotContainsString('This test is skipped', $output);
        $this->assertStringContainsString($actionInput, $output);
        $this->assertStringContainsString($beforeActionInput, $output);
    }

    /**
     * @throws TestReferenceException
     * @throws Exception
     */
    public function testFilter()
    {
        $fileList = new FilterList(['severity' => ["CRITICAL"]]);
        $test = $this->createMock(MftfApplicationConfig::class);
        $test->expects($this->once())->method('getFilterList')->willReturn($fileList);

        $actionInput = 'fakeInput';
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => $actionInput
        ]);

        $annotation1 = ['severity' => ['CRITICAL']];
        $annotation2 = ['severity' => ['MINOR']];
        $test1 = new TestObject(
            "test1",
            ["fakeAction" => $actionObject],
            $annotation1,
            [],
            "filename"
        );
        $test2 = new TestObject(
            "test2",
            ["fakeAction" => $actionObject],
            $annotation2,
            [],
            "filename"
        );
        $test3 = $this->createMock(TestGenerator::class);
        $test3->expects($this->once())->method('loadAllTestObjects')->willReturn([
            'sampleTest' => $test1,
            'test2' => $test2
        ]);

        $generatedTests = [];
        $test4 = $this->createMock(TestGenerator::class);
        $test4->expects($this->once())->method('createCestFile')->willReturn([
            function ($arg1, $arg2) use (&$generatedTests) {
                $generatedTests[$arg2] = true;
            }
        ]);

        $testGeneratorObject = TestGenerator::getInstance("", ["sampleTest" => $test1, "test2" => $test2]);
        $testGeneratorObject->createAllTestFiles(null, []);

        $this->assertArrayHasKey('test1Cest', $generatedTests);
        $this->assertArrayNotHasKey('test2Cest', $generatedTests);
    }
}
