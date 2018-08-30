<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use AspectMock\Proxy\Verifier;
use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Parsers\ActionGroupDataParser;
use Magento\FunctionalTestingFramework\Test\Parsers\TestDataParser;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use tests\unit\Util\TestDataArrayBuilder;
use tests\unit\Util\TestLoggingUtil;

class ObjectExtensionUtilTest extends TestCase
{
    /**
     * Before test functionality
     * @return void
     */
    public function setUp()
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Tests generating a test that extends another test
     * @throws \Exception
     */
    public function testGenerateExtendedTest()
    {
        $mockActions = [
          "mockStep" => ["nodeName" => "mockNode", "stepKey" => "mockStep"]
        ];

        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockSimpleTest = $testDataArrayBuilder
            ->withName('simpleTest')
            ->withAnnotations(['title'=>[['value' => 'simpleTest']]])
            ->withTestActions($mockActions)
            ->build();

        $mockExtendedTest = $testDataArrayBuilder
            ->withName('extendedTest')
            ->withAnnotations(['title'=>[['value' => 'extendedTest']]])
            ->withTestReference("simpleTest")
            ->build();

        $mockTestData = ['tests' => array_merge($mockSimpleTest, $mockExtendedTest)];
        $this->setMockTestOutput($mockTestData);

        // parse and generate test object with mocked data
        $testObject = TestObjectHandler::getInstance()->getObject('extendedTest');

        // assert log statement is correct
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'debug',
            'extending test',
            ['parent' => 'simpleTest', 'test' => 'extendedTest']
        );

        // assert that expected test is generated
        $this->assertEquals($testObject->getParentName(), "simpleTest");
        $this->assertArrayHasKey("mockStep", $testObject->getOrderedActions());
    }

    /**
     * Tests generating a test that extends another test
     * @throws \Exception
     */
    public function testGenerateExtendedWithHooks()
    {
        $mockBeforeHooks = [
            "beforeHookAction" => ["nodeName" => "mockNodeBefore", "stepKey" => "mockStepBefore"]
        ];
        $mockAfterHooks = [
            "afterHookAction" => ["nodeName" => "mockNodeAfter", "stepKey" => "mockStepAfter"]
        ];

        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockSimpleTest = $testDataArrayBuilder
            ->withName('simpleTest')
            ->withAnnotations(['title'=>[['value' => 'simpleTest']]])
            ->withBeforeHook($mockBeforeHooks)
            ->withAfterHook($mockAfterHooks)
            ->build();

        $mockExtendedTest = $testDataArrayBuilder
            ->withName('extendedTest')
            ->withAnnotations(['title'=>[['value' => 'extendedTest']]])
            ->withTestReference("simpleTest")
            ->build();

        $mockTestData = ['tests' => array_merge($mockSimpleTest, $mockExtendedTest)];
        $this->setMockTestOutput($mockTestData);

        // parse and generate test object with mocked data
        $testObject = TestObjectHandler::getInstance()->getObject('extendedTest');

        // assert log statement is correct
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'debug',
            'extending test',
            ['parent' => 'simpleTest', 'test' => 'extendedTest']
        );

        // assert that expected test is generated
        $this->assertEquals($testObject->getParentName(), "simpleTest");
        $this->assertArrayHasKey("mockStepBefore", $testObject->getHooks()['before']->getActions());
        $this->assertArrayHasKey("mockStepAfter", $testObject->getHooks()['after']->getActions());
    }
    
    /**
     * Tests generating a test that extends another test
     * @throws \Exception
     */
    public function testExtendedTestNoParent()
    {
        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockExtendedTest = $testDataArrayBuilder
            ->withName('extendedTest')
            ->withTestReference("simpleTest")
            ->build();

        $mockTestData = ['tests' => array_merge($mockExtendedTest)];
        $this->setMockTestOutput($mockTestData);

        // parse and generate test object with mocked data
        TestObjectHandler::getInstance()->getObject('extendedTest');

        // validate log statement
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'debug',
            "parent test not defined. test will be skipped",
            ['parent' => 'simpleTest', 'test' => 'extendedTest']
        );
    }

    /**
     * Tests generating a test that extends another test
     * @throws \Exception
     */
    public function testExtendingExtendedTest()
    {
        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockParentTest = $testDataArrayBuilder
            ->withName('anotherTest')
            ->withTestActions()
            ->build();

        $mockSimpleTest = $testDataArrayBuilder
            ->withName('simpleTest')
            ->withAnnotations(['title'=>[['value' => 'simpleTest']]])
            ->withTestActions()
            ->withTestReference("anotherTest")
            ->build();

        $mockExtendedTest = $testDataArrayBuilder
            ->withName('extendedTest')
            ->withAnnotations(['title'=>[['value' => 'extendedTest']]])
            ->withTestReference("simpleTest")
            ->build();

        $mockTestData = ['tests' => array_merge($mockParentTest, $mockSimpleTest, $mockExtendedTest)];
        $this->setMockTestOutput($mockTestData);

        $this->expectExceptionMessage("Cannot extend a test that already extends another test. Test: simpleTest");

        // parse and generate test object with mocked data
        TestObjectHandler::getInstance()->getObject('extendedTest');

        // validate log statement
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'debug',
            "parent test not defined. test will be skipped",
            ['parent' => 'simpleTest', 'test' => 'extendedTest']
        );
        $this->expectOutputString("Extending Test: anotherTest => simpleTest" . PHP_EOL);
    }

    /**
     * Tests generating an action group that extends another action group
     * @throws \Exception
     */
    public function testGenerateExtendedActionGroup()
    {
        $mockSimpleActionGroup = [
            "nodeName" => "actionGroup",
            "name" => "mockSimpleActionGroup",
            "filename" => "someFile",
            "commentHere" => [
                "nodeName" => "comment",
                "selector" => "selector",
                "stepKey" => "commentHere"
            ],
            "parentComment" => [
                "nodeName" => "comment",
                "selector" => "parentSelector",
                "stepKey" => "parentComment"
            ],
        ];

        $mockExtendedActionGroup = [
            "nodeName" => "actionGroup",
            "name" => "mockExtendedActionGroup",
            "filename" => "someFile",
            "extends" => "mockSimpleActionGroup",
            "commentHere" => [
                "nodeName" => "comment",
                "selector" => "otherSelector",
                "stepKey" => "commentHere"
            ],
        ];

        $mockActionGroupData = [
            'actionGroups' => [
                'mockSimpleActionGroup' => $mockSimpleActionGroup,
                'mockExtendedActionGroup' => $mockExtendedActionGroup
            ]
        ];
        $this->setMockTestOutput(null, $mockActionGroupData);

        // parse and generate test object with mocked data
        $actionGroupObject = ActionGroupObjectHandler::getInstance()->getObject('mockExtendedActionGroup');

        // validate log statement
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'debug',
            'extending action group:',
            ['parent' => $mockSimpleActionGroup['name'], 'actionGroup' => $mockExtendedActionGroup['name']]
        );

        // assert that expected test is generated
        $this->assertEquals("mockSimpleActionGroup", $actionGroupObject->getParentName());
        $actions = $actionGroupObject->getActions();
        $this->assertEquals("otherSelector", $actions["commentHere"]->getCustomActionAttributes()["selector"]);
        $this->assertEquals("parentSelector", $actions["parentComment"]->getCustomActionAttributes()["selector"]);
    }

    /**
     * Tests generating an action group that extends an action group that does not exist
     * @throws \Exception
     */
    public function testGenerateExtendedActionGroupNoParent()
    {
        $mockExtendedActionGroup = [
            "nodeName" => "actionGroup",
            "name" => "mockSimpleActionGroup",
            "filename" => "someFile",
            "extends" => "mockSimpleActionGroup",
            "commentHere" => [
                "nodeName" => "comment",
                "selector" => "otherSelector",
                "stepKey" => "commentHere"
            ],
        ];

        $mockActionGroupData = [
            'actionGroups' => [
                'mockExtendedActionGroup' => $mockExtendedActionGroup
            ]
        ];
        $this->setMockTestOutput(null, $mockActionGroupData);

        $this->expectExceptionMessage(
            "Parent Action Group mockSimpleActionGroup not defined for Test " . $mockExtendedActionGroup['extends']
        );

        // parse and generate test object with mocked data
        ActionGroupObjectHandler::getInstance()->getObject('mockExtendedActionGroup');
    }

    /**
     * Tests generating an action group that extends another action group that is already extended
     * @throws \Exception
     */
    public function testExtendingExtendedActionGroup()
    {
        $mockParentActionGroup = [
            "nodeName" => "actionGroup",
            "name" => "mockParentActionGroup",
            "filename" => "someFile"
        ];

        $mockSimpleActionGroup = [
            "nodeName" => "actionGroup",
            "name" => "mockSimpleActionGroup",
            "filename" => "someFile",
            "extends" => "mockParentActionGroup",
        ];

        $mockExtendedActionGroup = [
            "nodeName" => "actionGroup",
            "name" => "mockSimpleActionGroup",
            "filename" => "someFile",
            "extends" => "mockSimpleActionGroup",
        ];

        $mockActionGroupData = [
            'actionGroups' => [
                'mockParentActionGroup' => $mockParentActionGroup,
                'mockSimpleActionGroup' => $mockSimpleActionGroup,
                'mockExtendedActionGroup' => $mockExtendedActionGroup
            ]
        ];
        $this->setMockTestOutput(null, $mockActionGroupData);

        $this->expectExceptionMessage(
            "Cannot extend an action group that already extends another action group. " . $mockSimpleActionGroup['name']
        );

        // parse and generate test object with mocked data
        try {
            ActionGroupObjectHandler::getInstance()->getObject('mockExtendedActionGroup');
        } catch (\Exception $e) {
            // validate log statement
            TestLoggingUtil::getInstance()->validateMockLogStatement(
                'error',
                "Cannot extend an action group that already extends another action group. " .
                $mockSimpleActionGroup['name'],
                ['parent' => $mockSimpleActionGroup['name'], 'actionGroup' => $mockSimpleActionGroup['name']]
            );

            throw $e;
        }
    }

    /**
     * Function used to set mock for parser return and force init method to run between tests.
     *
     * @param array $testData
     * @throws \Exception
     */
    private function setMockTestOutput($testData = null, $actionGroupData = null)
    {
        // clear test object handler value to inject parsed content
        $property = new \ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $property->setAccessible(true);
        $property->setValue(null);

        // clear test object handler value to inject parsed content
        $property = new \ReflectionProperty(ActionGroupObjectHandler::class, 'ACTION_GROUP_OBJECT_HANDLER');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataParser = AspectMock::double(TestDataParser::class, ['readTestData' => $testData])->make();
        $mockActionGroupParser = AspectMock::double(
            ActionGroupDataParser::class,
            ['readActionGroupData' => $actionGroupData]
        )->make();
        $instance = AspectMock::double(
            ObjectManager::class,
            ['create' => function ($clazz) use (
                $mockDataParser,
                $mockActionGroupParser
            ) {
                if ($clazz == TestDataParser::class) {
                    return $mockDataParser;
                }
                if ($clazz == ActionGroupDataParser::class) {
                    return $mockActionGroupParser;
                }
            }]
        )->make();
        // bypass the private constructor
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);
    }

    /**
     * After class functionality
     * @return void
     */
    public static function tearDownAfterClass()
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
