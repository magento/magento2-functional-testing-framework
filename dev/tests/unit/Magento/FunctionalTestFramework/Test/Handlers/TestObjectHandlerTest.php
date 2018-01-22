<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tests\unit\Magento\FunctionalTestFramework\Test\Handlers;

use AspectMock\Test as AspectMock;

use Go\Aop\Aspect;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Parsers\TestDataParser;
use Magento\FunctionalTestingFramework\Test\Util\ActionObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;
use PHPUnit\Framework\TestCase;
use tests\unit\Util\TestDataArrayBuilder;

class TestObjectHandlerTest extends TestCase
{
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
            ->withAfterHook()
            ->withBeforeHook()
            ->withTestActions()
            ->build();

        $this->setMockParserOutput(['tests' => $mockData]);

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
        $expectedBeforeHookObject = new TestHookObject(
            TestObjectExtractor::TEST_BEFORE_HOOK,
            $testDataArrayBuilder->testName,
            [$expectedBeforeActionObject]
        );
        $expectedAfterHookObject = new TestHookObject(
            TestObjectExtractor::TEST_AFTER_HOOK,
            $testDataArrayBuilder->testName,
            [$expectedAfterActionObject]
        );

        $expectedTestActionObject = new ActionObject(
            $testDataArrayBuilder->testTestActionName,
            $testDataArrayBuilder->testActionType,
            []
        );
        $expectedTestObject = new TestObject(
            $testDataArrayBuilder->testName,
            [$expectedTestActionObject],
            [
                'group' => ['test']
            ],
            [
                TestObjectExtractor::TEST_BEFORE_HOOK => $expectedBeforeHookObject,
                TestObjectExtractor::TEST_AFTER_HOOK => $expectedAfterHookObject
            ],
            []
        );

        $this->assertEquals($expectedTestObject, $actualTestObject);
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
            ->withAnnotations()
            ->withTestActions()
            ->build();
        $excludeTest = (new TestDataArrayBuilder())
            ->withName('excludeTest')
            ->withTestActions()
            ->build();

        $this->setMockParserOutput(['tests' => array_merge($includeTest, $excludeTest)]);

        // execute test method
        $toh = TestObjectHandler::getInstance();
        $tests = $toh->getTestsByGroup('test');

        // perform asserts
        $this->assertCount(1, $tests);
        $this->assertArrayHasKey('includeTest', $tests);
        $this->assertArrayNotHasKey('excludeTest', $tests);
    }

    /**
     * Function used to set mock for parser return and force init method to run between tests.
     *
     * @param array $data
     * @throws \Exception
     */
    private function setMockParserOutput($data)
    {
        // clear test object handler value to inject parsed content
        $property = new \ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataParser = AspectMock::double(TestDataParser::class, ['readTestData' => $data])->make();
        $instance = AspectMock::double(ObjectManager::class, ['create' => $mockDataParser])
            ->make(); // bypass the private constructor
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);
    }
}
