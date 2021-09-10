<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Test\Handlers;

use Exception;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Parsers\TestDataParser;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;
use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\TestDataArrayBuilder;
use tests\unit\Util\TestLoggingUtil;

class TestObjectHandlerTest extends MagentoTestCase
{
    /**
     * Before test functionality.
     *
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Basic test to validate array => test object conversion.
     *
     * @return void
     * @throws Exception
     */
    public function testGetTestObject(): void
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

        $this->mockTestObjectHandler($mockData);

        // run object handler method
        $toh = TestObjectHandler::getInstance();
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
     * Tests basic getting of a test that has a fileName.
     *
     * @return void
     */
    public function testGetTestWithFileName(): void
    {
        $this->markTestIncomplete('TODO');
    }

    /**
     * Tests the function used to get a series of relevant tests by group.
     *
     * @return void
     * @throws Exception
     */
    public function testGetTestsByGroup(): void
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

        $this->mockTestObjectHandler(array_merge($includeTest, $excludeTest));

        // execute test method
        $toh = TestObjectHandler::getInstance();
        $tests = $toh->getTestsByGroup('test');

        // perform asserts
        $this->assertCount(1, $tests);
        $this->assertArrayHasKey('includeTest', $tests);
        $this->assertArrayNotHasKey('excludeTest', $tests);
    }

    /**
     * Tests the function used to parse and determine a test's Module (used in allure Features annotation).
     *
     * @return void
     * @throws Exception
     */
    public function testGetTestWithModuleName(): void
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

        $this->mockTestObjectHandler($mockData, ['Vendor_' . $moduleExpected => $filepath]);

        // Execute Test Method
        $toh = TestObjectHandler::getInstance();
        $actualTestObject = $toh->getObject($testDataArrayBuilder->testName);
        $moduleName = $actualTestObject->getAnnotations()["features"][0];
        //performAsserts
        $this->assertEquals($moduleExpected, $moduleName);
    }

    /**
     * getObject should throw exception if test extends from itself.
     *
     * @return void
     * @throws Exception
     */
    public function testGetTestObjectWithInvalidExtends(): void
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

        $this->mockTestObjectHandler($testOne);

        $toh = TestObjectHandler::getInstance();
        $this->expectException(TestFrameworkException::class);
        $this->expectExceptionMessage("Mftf Test can not extend from itself: " . "testOne");

        $toh->getObject('testOne');
    }

    /**
     * getAllObjects should throw exception if test extends from itself.
     *
     * @return void
     * @throws Exception
     */
    public function testGetAllTestObjectsWithInvalidExtends(): void
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

        $this->mockTestObjectHandler(array_merge($testOne, $testTwo));

        $toh = TestObjectHandler::getInstance();
        $toh->getAllObjects();

        // assert that no exception for getAllObjects and test generation error is stored in GenerationErrorHandler
        $errorMessage = '/' . preg_quote("Mftf Test can not extend from itself: " . "testOne") . '/';
        TestLoggingUtil::getInstance()->validateMockLogStatmentRegex('error', $errorMessage, []);
        $testErrors = GenerationErrorHandler::getInstance()->getErrorsByType('test');
        $this->assertArrayHasKey('testOne', $testErrors);
    }

    /**
     * Validate test object when ENABLE_PAUSE is set to true.
     *
     * @return void
     * @throws Exception
     */
    public function testGetTestObjectWhenEnablePause(): void
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

        $this->mockTestObjectHandler($mockData);

        // run object handler method
        $toh = TestObjectHandler::getInstance();
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
     * After method functionality.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
        parent::tearDownAfterClass();
    }

    /**
     * Mock test object handler.
     *
     * @param array $data
     * @param array|null $paths
     *
     * @return void
     */
    private function mockTestObjectHandler(array $data, ?array $paths = null): void
    {
        if (!$paths) {
            $paths = ['Magento_Module' => '/base/path/some/other/path/Magento/Module'];
        }
        // clear test object handler value to inject parsed content
        $property = new ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataParser = $this->createMock(TestDataParser::class);
        $mockDataParser
            ->method('readTestData')
            ->willReturn($data);

        $mockConfig = $this->createMock(MftfApplicationConfig::class);
        $mockConfig
            ->method('forceGenerateEnabled')
            ->willReturn(false);

        $mockResolver = $this->createMock(ModuleResolver::class);
        $mockResolver
            ->method('getEnabledModules')
            ->willReturn([]);

        $objectManager = ObjectManagerFactory::getObjectManager();
        $objectManagerMockInstance = $this->createMock(ObjectManager::class);
        $objectManagerMockInstance
            ->method('create')
            ->will(
                $this->returnCallback(
                    function (
                        $class,
                        $arguments = []
                    ) use (
                        $objectManager,
                        $mockDataParser,
                        $mockConfig,
                        $mockResolver
                    ) {
                        if ($class === TestDataParser::class) {
                            return $mockDataParser;
                        }
                        if ($class === MftfApplicationConfig::class) {
                            return $mockConfig;
                        }
                        if ($class === ModuleResolver::class) {
                            return $mockResolver;
                        }

                        return $objectManager->create($class, $arguments);
                    }
                )
            );

        $objectManagerProperty = new ReflectionProperty(ObjectManager::class, 'instance');
        $objectManagerProperty->setAccessible(true);
        $objectManagerProperty->setValue($objectManagerMockInstance);

        $resolver = ModuleResolver::getInstance();
        $property = new ReflectionProperty(ModuleResolver::class, 'enabledModuleNameAndPaths');
        $property->setAccessible(true);
        $property->setValue($resolver, $paths);
    }
}
