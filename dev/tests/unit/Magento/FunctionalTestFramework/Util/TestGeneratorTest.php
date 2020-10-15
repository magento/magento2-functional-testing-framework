<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Util;

use AspectMock\Test as AspectMock;

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
    /**
     * Before method functionality
     */
    public function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * After method functionality
     *
     * @return void
     */
    public function tearDown(): void
    {
        AspectMock::clean();
        GenerationErrorHandler::getInstance()->reset();
    }

    /**
     * Basic test to check exceptions for incorrect entities.
     *
     * @throws \Exception
     */
    public function testEntityException()
    {
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => '{{someEntity.entity}}'
        ]);

        $testObject = new TestObject("sampleTest", ["merge123" => $actionObject], [], [], "filename");

        AspectMock::double(TestObjectHandler::class, ['initTestData' => '']);

        $testGeneratorObject = TestGenerator::getInstance("", ["sampleTest" => $testObject]);

        AspectMock::double(TestGenerator::class, ['loadAllTestObjects' => ["sampleTest" => $testObject]]);

        $testGeneratorObject->createAllTestFiles(null, []);

        // assert that no exception for createAllTestFiles and generation error is stored in GenerationErrorHandler
        $errorMessage = '/' . preg_quote("Removed invalid test object sampleTest") . '/';
        TestLoggingUtil::getInstance()->validateMockLogStatmentRegex('error', $errorMessage, []);
        $testErrors = GenerationErrorHandler::getInstance()->getErrorsByType('test');
        $this->assertArrayHasKey('sampleTest', $testErrors);
    }

    /**
     * Tests that skipped tests do not have a fully generated body
     *
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
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
     * Tests that skipped tests have a fully generated body when --allowSkipped is passed in
     *
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testAllowSkipped()
    {
        // Mock allowSkipped for TestGenerator
        AspectMock::double(MftfApplicationConfig::class, ['allowSkipped' => true]);

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
     * Tests that TestGenerator createAllTestFiles correctly filters based on severity
     *
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testFilter()
    {
        // Mock filters for TestGenerator
        AspectMock::double(
            MftfApplicationConfig::class,
            ['getFilterList' => new FilterList(['severity' => ["CRITICAL"]])]
        );

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
        AspectMock::double(TestGenerator::class, ['loadAllTestObjects' => ["sampleTest" => $test1, "test2" => $test2]]);

        // Mock createCestFile to return name of tests that testGenerator tried to create
        $generatedTests = [];
        AspectMock::double(TestGenerator::class, ['createCestFile' => function ($arg1, $arg2) use (&$generatedTests) {
            $generatedTests[$arg2] = true;
        }]);

        $testGeneratorObject = TestGenerator::getInstance("", ["sampleTest" => $test1, "test2" => $test2]);
        $testGeneratorObject->createAllTestFiles(null, []);

        // Ensure Test1 was Generated but not Test 2
        $this->assertArrayHasKey('test1Cest', $generatedTests);
        $this->assertArrayNotHasKey('test2Cest', $generatedTests);
    }
}
