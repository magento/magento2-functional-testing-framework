<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use Exception;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Parsers\ActionGroupDataParser;
use Magento\FunctionalTestingFramework\Test\Parsers\TestDataParser;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use tests\unit\Util\TestDataArrayBuilder;
use tests\unit\Util\TestLoggingUtil;

class ObjectExtensionUtilTest extends TestCase
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
     * After class functionality.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }

    /**
     * Tests generating a test that extends another test.
     *
     * @return void
     * @throws Exception
     */
    public function testGenerateExtendedTest(): void
    {
        $mockActions = [
            'mockStep' => ['nodeName' => 'mockNode', 'stepKey' => 'mockStep']
        ];

        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockSimpleTest = $testDataArrayBuilder
            ->withName('simpleTest')
            ->withAnnotations(['title' => [['value' => 'simpleTest']]])
            ->withTestActions($mockActions)
            ->build();

        $mockExtendedTest = $testDataArrayBuilder
            ->withName('extendedTest')
            ->withAnnotations(['title' => [['value' => 'extendedTest']]])
            ->withTestReference('simpleTest')
            ->build();

        $mockTestData = array_merge($mockSimpleTest, $mockExtendedTest);
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
        $this->assertEquals($testObject->getParentName(), 'simpleTest');
        $this->assertArrayHasKey('mockStep', $testObject->getOrderedActions());
    }

    /**
     * Tests generating a test that extends another test.
     *
     * @return void
     * @throws Exception
     */
    public function testGenerateExtendedWithHooks(): void
    {
        $mockBeforeHooks = [
            'beforeHookAction' => ['nodeName' => 'mockNodeBefore', 'stepKey' => 'mockStepBefore']
        ];
        $mockAfterHooks = [
            'afterHookAction' => ['nodeName' => 'mockNodeAfter', 'stepKey' => 'mockStepAfter']
        ];

        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockSimpleTest = $testDataArrayBuilder
            ->withName('simpleTest')
            ->withAnnotations(['title' => [['value' => 'simpleTest']]])
            ->withBeforeHook($mockBeforeHooks)
            ->withAfterHook($mockAfterHooks)
            ->build();

        $mockExtendedTest = $testDataArrayBuilder
            ->withName('extendedTest')
            ->withAnnotations(['title' => [['value' => 'extendedTest']]])
            ->withTestReference('simpleTest')
            ->build();

        $mockTestData = array_merge($mockSimpleTest, $mockExtendedTest);
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
        $this->assertEquals($testObject->getParentName(), 'simpleTest');
        $this->assertArrayHasKey('mockStepBefore', $testObject->getHooks()['before']->getActions());
        $this->assertArrayHasKey('mockStepAfter', $testObject->getHooks()['after']->getActions());
    }

    /**
     * Tests generating a test that extends another test.
     *
     * @return void
     * @throws Exception
     */
    public function testExtendedTestNoParent(): void
    {
        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockExtendedTest = $testDataArrayBuilder
            ->withName('extendedTest')
            ->withTestReference('simpleTest')
            ->build();

        $mockTestData = array_merge($mockExtendedTest);
        $this->setMockTestOutput($mockTestData);

        // parse and generate test object with mocked data
        TestObjectHandler::getInstance()->getObject('extendedTest');

        // validate log statement
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'debug',
            'parent test not defined. test will be skipped',
            ['parent' => 'simpleTest', 'test' => 'extendedTest']
        );
    }

    /**
     * Tests generating a test that extends another test.
     *
     * @return void
     * @throws Exception
     */
    public function testExtendingExtendedTest(): void
    {
        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockParentTest = $testDataArrayBuilder
            ->withName('anotherTest')
            ->withTestActions()
            ->build();

        $mockSimpleTest = $testDataArrayBuilder
            ->withName('simpleTest')
            ->withAnnotations(['title' => [['value' => 'simpleTest']]])
            ->withTestActions()
            ->withTestReference('anotherTest')
            ->build();

        $mockExtendedTest = $testDataArrayBuilder
            ->withName('extendedTest')
            ->withAnnotations(['title' => [['value' => 'extendedTest']]])
            ->withTestReference('simpleTest')
            ->build();

        $mockTestData = array_merge($mockParentTest, $mockSimpleTest, $mockExtendedTest);
        $this->setMockTestOutput($mockTestData);

        $this->expectExceptionMessage('Cannot extend a test that already extends another test. Test: simpleTest');

        // parse and generate test object with mocked data
        TestObjectHandler::getInstance()->getObject('extendedTest');

        // validate log statement
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'debug',
            'parent test not defined. test will be skipped',
            ['parent' => 'simpleTest', 'test' => 'extendedTest']
        );
        $this->expectOutputString('Extending Test: anotherTest => simpleTest' . PHP_EOL);
    }

    /**
     * Tests generating an action group that extends another action group.
     *
     * @return void
     * @throws Exception
     */
    public function testGenerateExtendedActionGroup(): void
    {
        $mockSimpleActionGroup = [
            'nodeName' => 'actionGroup',
            'name' => 'mockSimpleActionGroup',
            'filename' => 'someFile',
            'commentHere' => [
                'nodeName' => 'comment',
                'selector' => 'selector',
                'stepKey' => 'commentHere'
            ],
            'parentComment' => [
                'nodeName' => 'comment',
                'selector' => 'parentSelector',
                'stepKey' => 'parentComment'
            ],
        ];

        $mockExtendedActionGroup = [
            'nodeName' => 'actionGroup',
            'name' => 'mockExtendedActionGroup',
            'filename' => 'someFile',
            'extends' => 'mockSimpleActionGroup',
            'commentHere' => [
                'nodeName' => 'comment',
                'selector' => 'otherSelector',
                'stepKey' => 'commentHere'
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
        $this->assertEquals('mockSimpleActionGroup', $actionGroupObject->getParentName());
        $actions = $actionGroupObject->getActions();
        $this->assertEquals('otherSelector', $actions['commentHere']->getCustomActionAttributes()['selector']);
        $this->assertEquals('parentSelector', $actions['parentComment']->getCustomActionAttributes()['selector']);
    }

    /**
     * Tests generating an action group that extends an action group that does not exist.
     *
     * @return void
     * @throws Exception
     */
    public function testGenerateExtendedActionGroupNoParent(): void
    {
        $mockExtendedActionGroup = [
            'nodeName' => 'actionGroup',
            'name' => 'mockExtendedActionGroup',
            'filename' => 'someFile',
            'extends' => 'mockSimpleActionGroup',
            'commentHere' => [
                'nodeName' => 'comment',
                'selector' => 'otherSelector',
                'stepKey' => 'commentHere'
            ],
        ];

        $mockActionGroupData = [
            'actionGroups' => [
                'mockExtendedActionGroup' => $mockExtendedActionGroup
            ]
        ];
        $this->setMockTestOutput(null, $mockActionGroupData);

        $this->expectExceptionMessage(
            'Parent Action Group mockSimpleActionGroup not defined for Test ' . $mockExtendedActionGroup['name']
        );

        // parse and generate test object with mocked data
        ActionGroupObjectHandler::getInstance()->getObject('mockExtendedActionGroup');
    }

    /**
     * Tests generating an action group that extends another action group that is already extended.
     *
     * @return void
     * @throws Exception
     */
    public function testExtendingExtendedActionGroup(): void
    {
        $mockParentActionGroup = [
            'nodeName' => 'actionGroup',
            'name' => 'mockParentActionGroup',
            'filename' => 'someFile'
        ];

        $mockSimpleActionGroup = [
            'nodeName' => 'actionGroup',
            'name' => 'mockSimpleActionGroup',
            'filename' => 'someFile',
            'extends' => 'mockParentActionGroup'
        ];

        $mockExtendedActionGroup = [
            'nodeName' => 'actionGroup',
            'name' => 'mockExtendedActionGroup',
            'filename' => 'someFile',
            'extends' => 'mockSimpleActionGroup'
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
            'Cannot extend an action group that already extends another action group. ' . $mockSimpleActionGroup['name']
        );

        // parse and generate test object with mocked data
        try {
            ActionGroupObjectHandler::getInstance()->getObject('mockExtendedActionGroup');
        } catch (Exception $exception) {
            // validate log statement
            TestLoggingUtil::getInstance()->validateMockLogStatement(
                'error',
                'Cannot extend an action group that already extends another action group. ' .
                $mockSimpleActionGroup['name'],
                ['parent' => $mockSimpleActionGroup['name'], 'actionGroup' => $mockExtendedActionGroup['name']]
            );

            throw $exception;
        }
    }

    /**
     * Tests generating a test that extends a skipped parent test.
     *
     * @return void
     * @throws Exception
     */
    public function testExtendedTestSkippedParent(): void
    {
        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockParentTest = $testDataArrayBuilder
            ->withName('baseTest')
            ->withAnnotations([
                'skip' => ['nodeName' => 'skip', 'issueId' => [['nodeName' => 'issueId', 'value' => 'someIssue']]]
            ])
            ->build();

        $testDataArrayBuilder->reset();
        $mockExtendedTest = $testDataArrayBuilder
            ->withName('extendTest')
            ->withTestReference('baseTest')
            ->build();

        $mockTestData = array_merge($mockParentTest, $mockExtendedTest);
        $this->setMockTestOutput($mockTestData);

        // parse and generate test object with mocked data
        TestObjectHandler::getInstance()->getObject('extendTest');

        // validate log statement
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'debug',
            'extendTest is skipped due to ParentTestIsSkipped',
            []
        );
    }

    /**
     * Function used to set mock for parser return and force init method to run between tests.
     *
     * @param array|null $testData
     * @param array|null $actionGroupData
     *
     * @return void
     * @throws Exception
     */
    private function setMockTestOutput(array $testData = null, array $actionGroupData = null): void
    {
        // clear test object handler value to inject parsed content
        $property = new ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $property->setAccessible(true);
        $property->setValue(null);

        // clear test object handler value to inject parsed content
        $property = new ReflectionProperty(ActionGroupObjectHandler::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataParser = $this->createMock(TestDataParser::class);
        $mockDataParser
            ->method('readTestData')
            ->willReturn($testData);

        $mockActionGroupParser = $this->createMock(ActionGroupDataParser::class);
        $mockActionGroupParser
            ->method('readActionGroupData')
            ->willReturn($actionGroupData);

        $instance = $this->createMock(ObjectManager::class);
        $instance
            ->method('create')
            ->will(
                $this->returnCallback(
                    function ($className) use ($mockDataParser, $mockActionGroupParser) {
                        if ($className === TestDataParser::class) {
                            return $mockDataParser;
                        }

                        if ($className === ActionGroupDataParser::class) {
                            return $mockActionGroupParser;
                        }

                        return null;
                    }
                )
            );
        // clear object manager value to inject expected instance
        $property = new ReflectionProperty(ObjectManager::class, 'instance');
        $property->setAccessible(true);
        $property->setValue($instance);
    }
}
