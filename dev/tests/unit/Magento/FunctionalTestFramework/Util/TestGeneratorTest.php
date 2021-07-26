<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Util;

use Exception;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Filter\FilterList;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\Filesystem\CestFileCreatorUtil;
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\TestLoggingUtil;

class TestGeneratorTest extends MagentoTestCase
{
    /**
     * Before method functionality.
     *
     * @return void
     */
    protected function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
        // Used to mock initTestData method running.
        $shouldSkipInitTestDataProperty = new ReflectionProperty(TestObjectHandler::class, 'shouldSkipInitTestData');
        $shouldSkipInitTestDataProperty->setAccessible(true);
        $shouldSkipInitTestDataProperty->setValue(true);
    }

    /**
     * After method functionality.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        GenerationErrorHandler::getInstance()->reset();

        $shouldSkipInitTestDataProperty = new ReflectionProperty(TestObjectHandler::class, 'shouldSkipInitTestData');
        $shouldSkipInitTestDataProperty->setAccessible(true);
        $shouldSkipInitTestDataProperty->setValue(false);
    }

    /**
     * Basic test to check exceptions for incorrect entities.
     *
     * @return void
     * @throws Exception
     */
    public function testEntityException(): void
    {
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => '{{someEntity.entity}}'
        ]);

        $testObject = new TestObject('sampleTest', ['merge123' => $actionObject], [], [], 'filename');
        $testGeneratorObject = TestGenerator::getInstance('', ['sampleTest' => $testObject]);
        $testGeneratorObject->createAllTestFiles(null, []);

        // assert that no exception for createAllTestFiles and generation error is stored in GenerationErrorHandler
        $errorMessage = '/' . preg_quote('Removed invalid test object sampleTest') . '/';
        TestLoggingUtil::getInstance()->validateMockLogStatmentRegex('error', $errorMessage, []);
        $testErrors = GenerationErrorHandler::getInstance()->getErrorsByType('test');
        $this->assertArrayHasKey('sampleTest', $testErrors);
    }

    /**
     * Tests that skipped tests do not have a fully generated body.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testSkippedNoGeneration(): void
    {
        $actionInput = 'fakeInput';
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => $actionInput
        ]);

        $annotations = ['skip' => ['issue']];
        $testObject = new TestObject('sampleTest', ['merge123' => $actionObject], $annotations, [], 'filename');

        $testGeneratorObject = TestGenerator::getInstance('', ['sampleTest' => $testObject]);
        $output = $testGeneratorObject->assembleTestPhp($testObject);

        $this->assertStringContainsString('This test is skipped', $output);
        $this->assertStringNotContainsString($actionInput, $output);
    }

    /**
     * Tests that skipped tests have a fully generated body when --allowSkipped is passed in.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testAllowSkipped(): void
    {
        // Mock allowSkipped for TestGenerator
        $mockConfig = $this->createMock(MftfApplicationConfig::class);
        $mockConfig
            ->method('allowSkipped')
            ->willReturn(true);

        $property = new ReflectionProperty(MftfApplicationConfig::class, 'MFTF_APPLICATION_CONTEXT');
        $property->setAccessible(true);
        $property->setValue($mockConfig);

        $actionInput = 'fakeInput';
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => $actionInput
        ]);
        $beforeActionInput = 'beforeInput';
        $beforeActionObject = new ActionObject('beforeAction', 'comment', [
            'userInput' => $beforeActionInput
        ]);

        $annotations = ['skip' => ['issue']];
        $beforeHook = new TestHookObject('before', 'sampleTest', ['beforeAction' => $beforeActionObject]);
        $testObject = new TestObject(
            'sampleTest',
            ['fakeAction' => $actionObject],
            $annotations,
            ['before' => $beforeHook],
            'filename'
        );

        $testGeneratorObject = TestGenerator::getInstance('', ['sampleTest' => $testObject]);
        $output = $testGeneratorObject->assembleTestPhp($testObject);

        $this->assertStringNotContainsString('This test is skipped', $output);
        $this->assertStringContainsString($actionInput, $output);
        $this->assertStringContainsString($beforeActionInput, $output);
    }

    /**
     * Tests that TestGenerator createAllTestFiles correctly filters based on severity.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testFilter(): void
    {
        $mockConfig = $this->createMock(MftfApplicationConfig::class);
        $fileList = new FilterList(['severity' => ['CRITICAL']]);
        $mockConfig
            ->method('getFilterList')
            ->willReturn($fileList);

        $property = new ReflectionProperty(MftfApplicationConfig::class, 'MFTF_APPLICATION_CONTEXT');
        $property->setAccessible(true);
        $property->setValue($mockConfig);

        $actionInput = 'fakeInput';
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => $actionInput
        ]);

        $annotation1 = ['severity' => ['CRITICAL']];
        $annotation2 = ['severity' => ['MINOR']];
        $test1 = new TestObject(
            'test1',
            ['fakeAction' => $actionObject],
            $annotation1,
            [],
            'filename'
        );
        $test2 = new TestObject(
            'test2',
            ['fakeAction' => $actionObject],
            $annotation2,
            [],
            'filename'
        );

        // Mock createCestFile to return name of tests that testGenerator tried to create
        $generatedTests = [];
        $cestFileCreatorUtil = $this->createMock(CestFileCreatorUtil::class);
        $cestFileCreatorUtil
            ->method('create')
            ->will(
                $this->returnCallback(
                    function ($filename) use (&$generatedTests) {
                        $generatedTests[$filename] = true;
                    }
                )
            );

        $property = new ReflectionProperty(CestFileCreatorUtil::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue($cestFileCreatorUtil);

        $testGeneratorObject = TestGenerator::getInstance('', ['sampleTest' => $test1, 'test2' => $test2]);
        $testGeneratorObject->createAllTestFiles();

        // Ensure Test1 was Generated but not Test 2
        $this->assertArrayHasKey('test1Cest', $generatedTests);
        $this->assertArrayNotHasKey('test2Cest', $generatedTests);
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $cestFileCreatorUtilInstance = new ReflectionProperty(CestFileCreatorUtil::class, 'INSTANCE');
        $cestFileCreatorUtilInstance->setAccessible(true);
        $cestFileCreatorUtilInstance->setValue(null);

        $mftfAppConfigInstance = new ReflectionProperty(MftfApplicationConfig::class, 'MFTF_APPLICATION_CONTEXT');
        $mftfAppConfigInstance->setAccessible(true);
        $mftfAppConfigInstance->setValue(null);
    }
}
