<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Test\Handlers;

use AspectMock\Test as AspectMock;

use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Parsers\TestDataParser;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\ObjectHandlerUtil;
use tests\unit\Util\TestDataArrayBuilder;
use tests\unit\Util\MockModuleResolverBuilder;
use tests\unit\Util\TestLoggingUtil;
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;

class TestObjectHandlerTest extends MagentoTestCase
{
    public function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Basic test to validate array => test object conversion.
     *
     * @throws \Exception
     */
    public function testGetTestObject()
    {
        // set up mock data
        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockData = $testDataArrayBuilder
            ->withAnnotations()
            ->withFailedHook()
            ->withAfterHook()
            ->withBeforeHook()
            ->withTestActions()
            ->build();

        $resolverMock = new MockModuleResolverBuilder();
        $resolverMock->setup();
        ObjectHandlerUtil::mockTestObjectHandlerWitData($mockData);

        // run object handler method
        $toh = TestObjectHandler::getInstance();
        $mockConfig = AspectMock::double(TestObjectHandler::class, ['initTestData' => false]);
        $actualTestObject = $toh->getObject($testDataArrayBuilder->testName);

        // perform asserts
        $expectedBeforeActionObject = new ActionObject(
            $testDataArrayBuilder->testActionBeforeName,
            $testDataArrayBuilder->testActionType,
            []
        );
        $expectedAfterActionObject = new ActionObject(
            $testDataArrayBuilder->testActionAfterName,
            $testDataArrayBuilder->testActionType,
            []
        );
        $expectedFailedActionObject = new ActionObject(
            'saveScreenshot',
            'saveScreenshot',
            []
        );

        $expectedBeforeHookObject = new TestHookObject(
            TestObjectExtractor::TEST_BEFORE_HOOK,
            $testDataArrayBuilder->testName,
            ["testActionBefore" => $expectedBeforeActionObject]
        );
        $expectedAfterHookObject = new TestHookObject(
            TestObjectExtractor::TEST_AFTER_HOOK,
            $testDataArrayBuilder->testName,
            ["testActionAfter" => $expectedAfterActionObject]
        );
        $expectedFailedHookObject = new TestHookObject(
            TestObjectExtractor::TEST_FAILED_HOOK,
            $testDataArrayBuilder->testName,
            ["saveScreenshot" => $expectedFailedActionObject]
        );

        $expectedTestActionObject = new ActionObject(
            $testDataArrayBuilder->testTestActionName,
            $testDataArrayBuilder->testActionType,
            []
        );
        $expectedTestObject = new TestObject(
            $testDataArrayBuilder->testName,
            ["testActionInTest" => $expectedTestActionObject],
            [
                'features' => ['NO MODULE DETECTED'],
                'group' => ['test'],
                'description' => ['test_files' => '<h3>Test files</h3>', 'deprecated' => []]
            ],
            [
                TestObjectExtractor::TEST_BEFORE_HOOK => $expectedBeforeHookObject,
                TestObjectExtractor::TEST_AFTER_HOOK => $expectedAfterHookObject,
                TestObjectExtractor::TEST_FAILED_HOOK => $expectedFailedHookObject
            ],
            null
        );

        $this->assertEquals($expectedTestObject, $actualTestObject);
    }

    /**
     * Tests basic getting of a test that has a fileName
     */
    public function testGetTestWithFileName()
    {
        $this->markTestIncomplete('TODO');
    }

    /**
     * Tests the function used to get a series of relevant tests by group.
     *
     * @throws \Exception
     */
    public function testGetTestsByGroup()
    {
        // set up mock data with Exclude Test
        $includeTest = (new TestDataArrayBuilder())
            ->withName('includeTest')
            ->withAnnotations(['group' => [['value' => 'test']], 'title'=>[['value' => 'includeTest']]])
            ->withTestActions()
            ->build();
        $excludeTest = (new TestDataArrayBuilder())
            ->withName('excludeTest')
            ->withAnnotations(['title'=>[['value' => 'excludeTest']]])
            ->withTestActions()
            ->build();

        $resolverMock = new MockModuleResolverBuilder();
        $resolverMock->setup();
        ObjectHandlerUtil::mockTestObjectHandlerWitData(array_merge($includeTest, $excludeTest));

        // execute test method
        $toh = TestObjectHandler::getInstance();
        $tests = $toh->getTestsByGroup('test');

        // perform asserts
        $this->assertCount(1, $tests);
        $this->assertArrayHasKey('includeTest', $tests);
        $this->assertArrayNotHasKey('excludeTest', $tests);
    }

    /**
     * Tests the function used to parse and determine a test's Module (used in allure Features annotation)
     *
     * @throws \Exception
     */
    public function testGetTestWithModuleName()
    {
        // set up Test Data
        $moduleExpected = "SomeModuleName";
        $moduleExpectedTest = $moduleExpected . "Test";
        $filepath = DIRECTORY_SEPARATOR .
            "user" . DIRECTORY_SEPARATOR .
            "magento2ce" . DIRECTORY_SEPARATOR .
            "dev" . DIRECTORY_SEPARATOR .
            "tests" . DIRECTORY_SEPARATOR .
            "acceptance" . DIRECTORY_SEPARATOR .
            "tests" . DIRECTORY_SEPARATOR .
            "functional" . DIRECTORY_SEPARATOR .
            "Vendor" . DIRECTORY_SEPARATOR .
            $moduleExpectedTest;
        $file = $filepath . DIRECTORY_SEPARATOR .
            "Test" . DIRECTORY_SEPARATOR .
            "text.xml";
        // set up mock data
        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockData = $testDataArrayBuilder
            ->withAnnotations()
            ->withFailedHook()
            ->withAfterHook()
            ->withBeforeHook()
            ->withTestActions()
            ->withFileName($file)
            ->build();

        $resolverMock = new MockModuleResolverBuilder();
        $resolverMock->setup(['Vendor_' . $moduleExpected => $filepath]);

        ObjectHandlerUtil::mockTestObjectHandlerWitData($mockData);
        // Execute Test Method
        $toh = TestObjectHandler::getInstance();
        $actualTestObject = $toh->getObject($testDataArrayBuilder->testName);
        $moduleName = $actualTestObject->getAnnotations()["features"][0];
        //performAsserts
        $this->assertEquals($moduleExpected, $moduleName);
    }

    /**
     * getObject should throw exception if test extends from itself
     *
     * @throws \Exception
     */
    public function testGetTestObjectWithInvalidExtends()
    {
        // set up Test Data
        $testOne = (new TestDataArrayBuilder())
            ->withName('testOne')
            ->withTestReference('testOne')
            ->withAnnotations()
            ->withFailedHook()
            ->withAfterHook()
            ->withBeforeHook()
            ->withTestActions()
            ->build();
        $resolverMock = new MockModuleResolverBuilder();
        $resolverMock->setup();
        ObjectHandlerUtil::mockTestObjectHandlerWitData($testOne);

        $toh = TestObjectHandler::getInstance();

        $this->expectException(\Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException::class);
        $this->expectExceptionMessage("Mftf Test can not extend from itself: " . "testOne");

        $toh->getObject('testOne');
    }

    /**
     * getAllObjects should throw exception if test extends from itself
     *
     * @throws \Exception
     */
    public function testGetAllTestObjectsWithInvalidExtends()
    {
        // set up Test Data
        $testOne = (new TestDataArrayBuilder())
            ->withName('testOne')
            ->withTestReference('testOne')
            ->withAnnotations()
            ->withFailedHook()
            ->withAfterHook()
            ->withBeforeHook()
            ->withTestActions()
            ->build();
        $testTwo = (new TestDataArrayBuilder())
            ->withName('testTwo')
            ->withAnnotations()
            ->withFailedHook()
            ->withAfterHook()
            ->withBeforeHook()
            ->withTestActions()
            ->build();

        $resolverMock = new MockModuleResolverBuilder();
        $resolverMock->setup();
        ObjectHandlerUtil::mockTestObjectHandlerWitData(array_merge($testOne, $testTwo));

        $toh = TestObjectHandler::getInstance();
        $toh->getAllObjects();

        // assert that no exception for getAllObjects and test generation error is stored in GenerationErrorHandler
        $errorMessage = '/' . preg_quote("Mftf Test can not extend from itself: " . "testOne") . '/';
        TestLoggingUtil::getInstance()->validateMockLogStatmentRegex('error', $errorMessage, []);
        $testErrors = GenerationErrorHandler::getInstance()->getErrorsByType('test');
        $this->assertArrayHasKey('testOne', $testErrors);
    }

    /**
     * Validate test object when ENABLE_PAUSE is set to true
     *
     * @throws \Exception
     */
    public function testGetTestObjectWhenEnablePause()
    {
        // set up mock data
        putenv('ENABLE_PAUSE=true');
        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockData = $testDataArrayBuilder
            ->withAnnotations()
            ->withFailedHook()
            ->withAfterHook()
            ->withBeforeHook()
            ->withTestActions()
            ->build();

        $resolverMock = new MockModuleResolverBuilder();
        $resolverMock->setup();
        ObjectHandlerUtil::mockTestObjectHandlerWitData($mockData);

        // run object handler method
        $toh = TestObjectHandler::getInstance();
        $mockConfig = AspectMock::double(TestObjectHandler::class, ['initTestData' => false]);
        $actualTestObject = $toh->getObject($testDataArrayBuilder->testName);

        // perform asserts
        $expectedBeforeActionObject = new ActionObject(
            $testDataArrayBuilder->testActionBeforeName,
            $testDataArrayBuilder->testActionType,
            []
        );
        $expectedAfterActionObject = new ActionObject(
            $testDataArrayBuilder->testActionAfterName,
            $testDataArrayBuilder->testActionType,
            []
        );
        $expectedFailedActionObject1 = new ActionObject(
            'saveScreenshot',
            'saveScreenshot',
            []
        );
        $expectedFailedActionObject2 = new ActionObject(
            'pauseWhenFailed',
            'pause',
            [ActionObject::PAUSE_ACTION_INTERNAL_ATTRIBUTE => true]
        );

        $expectedBeforeHookObject = new TestHookObject(
            TestObjectExtractor::TEST_BEFORE_HOOK,
            $testDataArrayBuilder->testName,
            ["testActionBefore" => $expectedBeforeActionObject]
        );
        $expectedAfterHookObject = new TestHookObject(
            TestObjectExtractor::TEST_AFTER_HOOK,
            $testDataArrayBuilder->testName,
            ["testActionAfter" => $expectedAfterActionObject]
        );
        $expectedFailedHookObject = new TestHookObject(
            TestObjectExtractor::TEST_FAILED_HOOK,
            $testDataArrayBuilder->testName,
            [
                "saveScreenshot" => $expectedFailedActionObject1,
                "pauseWhenFailed" => $expectedFailedActionObject2,
            ]
        );

        $expectedTestActionObject = new ActionObject(
            $testDataArrayBuilder->testTestActionName,
            $testDataArrayBuilder->testActionType,
            []
        );
        $expectedTestObject = new TestObject(
            $testDataArrayBuilder->testName,
            ["testActionInTest" => $expectedTestActionObject],
            [
                'features' => ['NO MODULE DETECTED'],
                'group' => ['test'],
                'description' => ['test_files' => '<h3>Test files</h3>', 'deprecated' => []]
            ],
            [
                TestObjectExtractor::TEST_BEFORE_HOOK => $expectedBeforeHookObject,
                TestObjectExtractor::TEST_AFTER_HOOK => $expectedAfterHookObject,
                TestObjectExtractor::TEST_FAILED_HOOK => $expectedFailedHookObject
            ],
            null
        );

        $this->assertEquals($expectedTestObject, $actualTestObject);
        putenv('ENABLE_PAUSE');
    }

    /**
     * After method functionality
     *
     * @return void
     */
    public function tearDown(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
        AspectMock::clean();
        parent::tearDownAfterClass();
    }
}
