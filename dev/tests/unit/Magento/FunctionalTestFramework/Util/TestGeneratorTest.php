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
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\Filesystem\CestFileCreatorUtil;
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use ReflectionClass;
use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\TestLoggingUtil;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

class TestGeneratorTest extends MagentoTestCase
{
    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $property = new ReflectionProperty(ObjectManager::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null, null);

        $property = new ReflectionProperty(ModuleResolver::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null, null);
    }

    /**
     * Before method functionality.
     *
     * @return void
     */
    protected function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * After method functionality.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        GenerationErrorHandler::getInstance()->reset();
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
        $this->mockTestObjectHandler();

        $testGeneratorObject->createAllTestFiles(null, []);

        // assert that no exception for createAllTestFiles and generation error is stored in GenerationErrorHandler
        $errorMessage = '/' . preg_quote('Removed invalid test object sampleTest') . '/';
        TestLoggingUtil::getInstance()->validateMockLogStatmentRegex('error', $errorMessage, []);
        $testErrors = GenerationErrorHandler::getInstance()->getErrorsByType('test');
        $this->assertArrayHasKey('sampleTest', $testErrors);
    }

    /**
     * Basic test to check unique id is appended to input as prefix
     *
     * @return void
     * @throws Exception
     */
    public function testUniqueIdAppendedToInputStringAsPrefix()
    {
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => '{{someEntity.entity}}'
        ]);

        $testObject = new TestObject('sampleTest', ['merge123' => $actionObject], [], [], 'filename');
        $testGeneratorObject = TestGenerator::getInstance('', ['sampleTest' => $testObject]);

        $result = $testGeneratorObject->getUniqueIdForInput('prefix', "foo");
        
        $this->assertMatchesRegularExpression('/[A-Za-z0-9]+foo/', $result);
    }

    /**
     * Basic test to check if exception is thrown when invalid entity is found in xml file
     *
     * @return void
     * @throws Exception
     */
    public function testInvalidEntity()
    {
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => '{{someEntity.entity}}'
        ]);

        $testObject = new TestObject('sampleTest', ['merge123' => $actionObject], [], [], 'filename');
        $testGeneratorObject = TestGenerator::getInstance('', ['sampleTest' => $testObject]);
        $this->expectException(TestReferenceException::class);
        $result = $testGeneratorObject->entityExistsCheck('testintity', "teststepkey");
    }

    /**
     * Basic test to check unique id is appended to input as suffix
     *
     * @return void
     * @throws Exception
     */
    public function testUniqueIdAppendedToInputStringAsSuffix()
    {
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => '{{someEntity.entity}}'
        ]);

        $testObject = new TestObject('sampleTest', ['merge123' => $actionObject], [], [], 'filename');
        $testGeneratorObject = TestGenerator::getInstance('', ['sampleTest' => $testObject]);

        $result = $testGeneratorObject->getUniqueIdForInput('suffix', "foo");
        
        $this->assertMatchesRegularExpression('/foo[A-Za-z0-9]+/', $result);
    }

     /**
      * Basic test for wrong output for input
      *
      * @return void
      * @throws Exception
      */
    public function testFailedRegexForUniqueAttribute()
    {
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => '{{someEntity.entity}}'
        ]);

        $testObject = new TestObject('sampleTest', ['merge123' => $actionObject], [], [], 'filename');
        $testGeneratorObject = TestGenerator::getInstance('', ['sampleTest' => $testObject]);

        $result = $testGeneratorObject->getUniqueIdForInput('suffix', "foo");
        
        $this->assertDoesNotMatchRegularExpression('/bar[A-Za-z0-9]+/', $result);
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
        $property->setValue(null, $mockConfig);

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
    public function testSeverityFilter(): void
    {
        $mockConfig = $this->createMock(MftfApplicationConfig::class);
        $fileList = new FilterList(['severity' => ['CRITICAL']]);
        $mockConfig
            ->method('getFilterList')
            ->willReturn($fileList);

        $property = new ReflectionProperty(MftfApplicationConfig::class, 'MFTF_APPLICATION_CONTEXT');
        $property->setAccessible(true);
        $property->setValue(null, $mockConfig);

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
        $property->setValue(null, $cestFileCreatorUtil);

        $testGeneratorObject = TestGenerator::getInstance('', ['sampleTest' => $test1, 'test2' => $test2]);
        $testGeneratorObject->createAllTestFiles();

        // Ensure Test1 was Generated but not Test 2
        $this->assertArrayHasKey('test1Cest', $generatedTests);
        $this->assertArrayNotHasKey('test2Cest', $generatedTests);
    }

    /**
     * Test for exception thrown when duplicate arguments found
     *
     * @return void
     * @throws TestFrameworkException
     */
    public function testIfExceptionThrownWhenDuplicateArgumentsFound()
    {
        $fileContents = '<actionGroups xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="urn:magento:mftf:Test/etc/actionGroupSchema.xsd">
              <actionGroup name="ActionGroupReturningValueActionGroup">
                  <arguments>
                      <argument name="count" type="string"/>
                      <argument name="count" type="string"/>
                  </arguments>
                  <grabMultiple selector="selector" stepKey="grabProducts1"/>
                  <assertCount stepKey="assertCount">
                      <expectedResult type="int">{{count}}</expectedResult>
                      <actualResult type="variable">grabProducts1</actualResult>
                  </assertCount>
                  <return value="{$grabProducts1}" stepKey="returnProducts1"/>
              </actionGroup>
          </actionGroups>';
        $actionInput = 'fakeInput';
        $actionObject = new ActionObject('fakeAction', 'comment', [
          'userInput' => $actionInput
        ]);
        $annotation1 = ['group' => ['someGroupValue']];

        $test1 = new TestObject(
            'test1',
            ['fakeAction' => $actionObject],
            $annotation1,
            [],
            'filename'
        );
        $annotation2 = ['group' => ['someOtherGroupValue']];

        $test2 = new TestObject(
            'test2',
            ['fakeAction' => $actionObject],
            $annotation2,
            [],
            'filename'
        );
        $testGeneratorObject = TestGenerator::getInstance('', ['sampleTest' => $test1, 'test2' => $test2]);
        $result = $testGeneratorObject->throwExceptionIfDuplicateArgumentsFound($testGeneratorObject);
        $this->assertEquals($result, "");
    }

    /**
     * Test for exception not thrown when duplicate arguments not found
     *
     * @return void
     */
    public function testIfExceptionNotThrownWhenDuplicateArgumentsNotFound()
    {
        $fileContents = '<actionGroups xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:noNamespaceSchemaLocation="urn:magento:mftf:Test/etc/actionGroupSchema.xsd">
                <actionGroup name="ActionGroupReturningValueActionGroup">
                    <arguments>
                        <argument name="count" type="string"/>
                    </arguments>
                    <grabMultiple selector="selector" stepKey="grabProducts1"/>
                    <assertCount stepKey="assertCount">
                        <expectedResult type="int">{{count}}</expectedResult>
                        <actualResult type="variable">grabProducts1</actualResult>
                    </assertCount>
                    <return value="{$grabProducts1}" stepKey="returnProducts1"/>
                </actionGroup>
            </actionGroups>';
        $actionInput = 'fakeInput';
        $actionObject = new ActionObject('fakeAction', 'comment', [
          'userInput' => $actionInput
        ]);
        $annotation1 = ['group' => ['someGroupValue']];

        $test1 = new TestObject(
            'test1',
            ['fakeAction' => $actionObject],
            $annotation1,
            [],
            'filename'
        );
        $annotation2 = ['group' => ['someOtherGroupValue']];

        $test2 = new TestObject(
            'test2',
            ['fakeAction' => $actionObject],
            $annotation2,
            [],
            'filename'
        );
        $testGeneratorObject = TestGenerator::getInstance('', ['sampleTest' => $test1, 'test2' => $test2]);
        $result = $testGeneratorObject->throwExceptionIfDuplicateArgumentsFound($testGeneratorObject);
        $this->assertEquals($result, "");
    }

    /**
     * Tests that TestGenerator createAllTestFiles correctly filters based on group.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testIncludeGroupFilter(): void
    {
        $mockConfig = $this->createMock(MftfApplicationConfig::class);
        $fileList = new FilterList(['includeGroup' => ['someGroupValue']]);
        $mockConfig
            ->method('getFilterList')
            ->willReturn($fileList);

        $property = new ReflectionProperty(MftfApplicationConfig::class, 'MFTF_APPLICATION_CONTEXT');
        $property->setAccessible(true);
        $property->setValue(null, $mockConfig);

        $actionInput = 'fakeInput';
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => $actionInput
        ]);

        $annotation1 = ['group' => ['someGroupValue']];
        $annotation2 = ['group' => ['someOtherGroupValue']];
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
        $property->setValue(null, $cestFileCreatorUtil);

        $testGeneratorObject = TestGenerator::getInstance('', ['sampleTest' => $test1, 'test2' => $test2]);
        $testGeneratorObject->createAllTestFiles();

        // Ensure Test1 was Generated but not Test 2
        $this->assertArrayHasKey('test1Cest', $generatedTests);
        $this->assertArrayNotHasKey('test2Cest', $generatedTests);
    }

    /**
     * Tests that TestGenerator createAllTestFiles correctly filters based on group not included.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testExcludeGroupFilter(): void
    {
        $mockConfig = $this->createMock(MftfApplicationConfig::class);
        $fileList = new FilterList(['excludeGroup' => ['someGroupValue']]);
        $mockConfig
            ->method('getFilterList')
            ->willReturn($fileList);

        $property = new ReflectionProperty(MftfApplicationConfig::class, 'MFTF_APPLICATION_CONTEXT');
        $property->setAccessible(true);
        $property->setValue(null, $mockConfig);

        $actionInput = 'fakeInput';
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => $actionInput
        ]);

        $annotation1 = ['group' => ['someGroupValue']];
        $annotation2 = ['group' => ['someOtherGroupValue']];
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
        $property->setValue(null, $cestFileCreatorUtil);

        $testGeneratorObject = TestGenerator::getInstance('', ['sampleTest' => $test1, 'test2' => $test2]);
        $testGeneratorObject->createAllTestFiles();

        // Ensure Test2 was Generated but not Test 1
        $this->assertArrayNotHasKey('test1Cest', $generatedTests);
        $this->assertArrayHasKey('test2Cest', $generatedTests);
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $cestFileCreatorUtilInstance = new ReflectionProperty(CestFileCreatorUtil::class, 'INSTANCE');
        $cestFileCreatorUtilInstance->setAccessible(true);
        $cestFileCreatorUtilInstance->setValue(null, null);

        $mftfAppConfigInstance = new ReflectionProperty(MftfApplicationConfig::class, 'MFTF_APPLICATION_CONTEXT');
        $mftfAppConfigInstance->setAccessible(true);
        $mftfAppConfigInstance->setValue(null, null);

        $property = new ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $property->setAccessible(true);
        $property->setValue(null, null);
    }

    /**
     * Mock test object handler for test.
     */
    private function mockTestObjectHandler(): void
    {
        $testObjectHandlerClass = new ReflectionClass(TestObjectHandler::class);
        $testObjectHandlerConstructor = $testObjectHandlerClass->getConstructor();
        $testObjectHandlerConstructor->setAccessible(true);
        $testObjectHandler = $testObjectHandlerClass->newInstanceWithoutConstructor();
        $testObjectHandlerConstructor->invoke($testObjectHandler);

        $property = new ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $property->setAccessible(true);
        $property->setValue(null, $testObjectHandler);
    }
}
