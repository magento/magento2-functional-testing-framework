<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Test\Objects;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Page\Handlers\SectionObjectHandler;
use Magento\FunctionalTestingFramework\Page\Objects\ElementObject;
use Magento\FunctionalTestingFramework\Page\Objects\SectionObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\ArgumentObject;
use ReflectionProperty;
use tests\unit\Util\ActionGroupObjectBuilder;
use tests\unit\Util\EntityDataObjectBuilder;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\TestLoggingUtil;

class ActionGroupObjectTest extends MagentoTestCase
{
    const ACTION_GROUP_MERGE_KEY = 'TestKey';

    /**
     * Before test functionality.
     *
     * @return void
     */
    protected function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Tests a string literal in an action group.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testGetStepsWithDefaultCase(): void
    {
        $entity = (new EntityDataObjectBuilder())
            ->withDataFields(['field1' => 'testValue'])
            ->build();
        $this->setEntityObjectHandlerReturn($entity);
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())->build();
        $steps = $actionGroupUnderTest->getSteps(null, self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => 'literal', 'requiredCredentials' => '']);
    }

    /**
     * Tests a data reference in an action group, replaced by the user.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testGetStepsWithCustomArgs(): void
    {
        $this->setEntityObjectHandlerReturn(function ($entityName) {
            if ($entityName === 'data2') {
                return (new EntityDataObjectBuilder())->withDataFields(['field2' => 'testValue2'])->build();
            }
        });

        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject(
                'action1',
                'testAction',
                [
                    'userInput' => '{{arg1.field2}}','requiredCredentials' => ''
                ]
            )])
        ->withArguments([new ArgumentObject('arg1', null, 'entity')])
        ->build();

        $steps = $actionGroupUnderTest->getSteps(['arg1' => 'data2'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => 'testValue2','requiredCredentials' => '']);

        // entity.field as argument
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject(
                'action1',
                'testAction',
                ['userInput' => '{{arg1}}',
                'requiredCredentials' => ''
                ]
            )])
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(['arg1' => 'data2.field2'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => 'testValue2', 'requiredCredentials' => '']);

        // String Data
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject(
                'action1',
                'testAction',
                ['userInput' => '{{simple}}',
                'requiredCredentials' => ''
                ]
            )])
            ->withArguments([new ArgumentObject('simple', null, 'string')])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(['simple' => 'override'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => 'override', 'requiredCredentials' => '']);
    }

    /**
     * Tests a data reference in an action group replaced with a persisted reference.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testGetStepsWithPersistedArgs(): void
    {
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject(
                'action1',
                'testAction',
                ['userInput' => '{{arg1.field2}}',
                'requiredCredentials' => '']
            )])
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(['arg1' => '$data3$'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => '$data3.field2$','requiredCredentials' => '']);

        // Simple Data
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject(
                'action1',
                'testAction',
                ['userInput' => '{{simple}}',
                'requiredCredentials' => ''
                ]
            )])
            ->withArguments([new ArgumentObject('simple', null, 'string')])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(['simple' => '$data3.field2$'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => '$data3.field2$','requiredCredentials' => '']);
    }

    /**
     * Tests a data reference in an action group replaced with a data.field reference.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testGetStepsWithNoFieldArg(): void
    {
        $this->setEntityObjectHandlerReturn(function ($entityName) {
            if ($entityName === 'data2') {
                return (new EntityDataObjectBuilder())->withDataFields(['field2' => 'testValue2'])->build();
            }
        });

        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject(
                'action1',
                'testAction',
                ['userInput' => '{{arg1}}',
                'requiredCredentials' => ''
                ]
            )])
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(['arg1' => 'data2.field2'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => 'testValue2','requiredCredentials' => '']);
    }

    /**
     * Tests a data reference in an action group resolved with its default state.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testGetStepsWithNoArgs(): void
    {
        $this->setEntityObjectHandlerReturn(function ($entityName) {
            if ($entityName === 'data1') {
                return (new EntityDataObjectBuilder())->withDataFields(['field1' => 'testValue'])->build();
            }
        });

        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject(
                'action1',
                'testAction',
                ['userInput' => '{{data1.field1}}',
                 'requiredCredentials' => ''
                ]
            )])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(null, self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => 'testValue','requiredCredentials' => '']);
    }

    /**
     * Tests a parameterized section reference in an action group resolved with user args.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testGetStepsWithParameterizedArg(): void
    {
        // Mock Entity Object Handler
        $this->setEntityObjectHandlerReturn(function ($entityName) {
            if ($entityName === 'data2') {
                return (new EntityDataObjectBuilder())->withDataFields(['field2' => 'testValue2'])->build();
            }
        });
        // mock the section object handler response
        $element = new ElementObject("element1", "textArea", ".selector {{var1}}", null, null, true);
        $section = new SectionObject("testSection", ["element1" => $element]);
        $sectionInstance = $this->createMock(SectionObjectHandler::class);
        $sectionInstance
            ->method('getObject')
            ->willReturn($section);
        // bypass the private constructor
        $property = new ReflectionProperty(SectionObjectHandler::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue(null, $sectionInstance);

        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects(
                [new ActionObject('action1', 'testAction', ['selector' => '{{section1.element1(arg1.field2)}}'])]
            )
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        // XML Data
        $steps = $actionGroupUnderTest->getSteps(['arg1' => 'data2'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, [
            'selector' => '.selector testValue2',
            'requiredCredentials' => ''
        ]);

        // Persisted Data
        $steps = $actionGroupUnderTest->getSteps(
            ['arg1' => '$data2$'],
            self::ACTION_GROUP_MERGE_KEY
        );
        $this->assertOnMergeKeyAndActionValue(
            $steps,
            ['selector' => '.selector $data2.field2$',
            'requiredCredentials' => ''
            ]
        );
    }

    /**
     * Tests a parameterized section reference in an action group resolved with user simpleArgs.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testGetStepsWithParameterizedSimpleArg(): void
    {
        // Mock Entity Object Handler
        $this->setEntityObjectHandlerReturn(function ($entityName) {
            if ($entityName === 'data2') {
                return (new EntityDataObjectBuilder())->withDataFields(['field2' => 'testValue2'])->build();
            }
        });
        // mock the section object handler response
        $element = new ElementObject("element1", "textArea", ".selector {{var1}}", null, null, true);
        $section = new SectionObject("testSection", ["element1" => $element]);

        $sectionInstance = $this->createMock(SectionObjectHandler::class);
        $sectionInstance
            ->method('getObject')
            ->willReturn($section);
        // bypass the private constructor
        $property = new ReflectionProperty(SectionObjectHandler::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue(null, $sectionInstance);

        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects(
                [new ActionObject('action1', 'testAction', ['selector' => '{{section1.element1(simple)}}'])]
            )
            ->withArguments([new ArgumentObject('simple', null, 'string')])
            ->build();

        // String Literal
        $steps = $actionGroupUnderTest->getSteps(['simple' => 'stringLiteral'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, [
            'selector' => '.selector stringLiteral',
            'requiredCredentials' => ''
        ]);

        // String Literal w/ data-like structure
        $steps = $actionGroupUnderTest->getSteps(['simple' => 'data2.field2'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue(
            $steps,
            ['selector' => '.selector data2.field2',
            'requiredCredentials' => ''
            ]
        );

        // Persisted Data
        $steps = $actionGroupUnderTest->getSteps(['simple' => '$someData.field1$'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue(
            $steps,
            ['selector' => '.selector $someData.field1$',
            'requiredCredentials' => ''
            ]
        );
    }

    /**
     * Tests a data reference in an action group resolved with a persisted reference used in another function.
     *
     * @return void
     * @throws TestReferenceException
     */
    public function testGetStepsWithOuterScopePersistence(): void
    {
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject('action1', 'testAction', ['userInput' => '{{arg1.field1}}'])])
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(['arg1' => '$$someData$$'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue(
            $steps,
            ['userInput' => '$$someData.field1$$',
            'requiredCredentials' => ''
            ]
        );
    }

    /**
     * Tests an action group with mismatching args.
     *
     * @return void
     */
    public function testExceptionOnMissingActions(): void
    {
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        $this->expectException(TestReferenceException::class);
        $this->expectExceptionMessageMatches('/Arguments missed .* for actionGroup/');
        $actionGroupUnderTest->getSteps(['arg2' => 'data1'], self::ACTION_GROUP_MERGE_KEY);
    }

    /**
     * Tests an action group with missing args.
     *
     * @return void
     */
    public function testExceptionOnMissingArguments(): void
    {
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        $this->expectException(TestReferenceException::class);
        $this->expectExceptionMessageMatches('/Arguments missed .* for actionGroup/');
        $actionGroupUnderTest->getSteps(null, self::ACTION_GROUP_MERGE_KEY);
    }

    /**
     * Tests the stepKey replacement with "stepKey + invocationKey" process filter.
     * Specific to actions that make it past a "require stepKey replacement" filter.
     *
     * @return void
     */
    public function testStepKeyReplacementFilteredIn(): void
    {
        $createStepKey = "createDataStepKey";
        $updateStepKey = "updateDataStepKey";

        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([
                new ActionObject(
                    $updateStepKey,
                    ActionGroupObject::STEPKEY_REPLACEMENT_ENABLED_TYPES[6],
                    ['selector' => 'value']
                ),
                new ActionObject(
                    $createStepKey,
                    ActionGroupObject::STEPKEY_REPLACEMENT_ENABLED_TYPES[7],
                    ['selector' => 'value']
                )
            ])
            ->build();

        $result = $actionGroupUnderTest->extractStepKeys();

        $this->assertContains($updateStepKey, $result);
        $this->assertContains($createStepKey, $result);
        $this->assertCount(2, $result);
    }

    /**
     * Tests the stepKey replacement with "stepKey + invocationKey" process filter.
     * Specific to actions that make are removed by a "require stepKey replacement" filter.
     *
     * @return void
     */
    public function testStepKeyReplacementFilteredOut(): void
    {
        $clickStepKey = "clickStepKey";
        $fillFieldStepKey = "fillFieldStepKey";
        $clickAction = "click";
        $fillFieldAction ="fillField";

        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([
                new ActionObject($clickStepKey, $clickAction, ['selector' => 'value']),
                new ActionObject($fillFieldStepKey, $fillFieldAction, ['selector' => 'value'])
            ])
            ->build();

        $result = $actionGroupUnderTest->extractStepKeys();

        $this->assertNotContains($clickStepKey, $result);
        $this->assertNotContains($fillFieldStepKey, $result);
        $this->assertCount(0, $result);
    }

    /**
     * This function takes a desired return for the EntityObjectHandler mock and performs set up of the mock for the
     * duration of a single test case.
     *
     * @param mixed $return
     *
     * @return void
     */
    private function setEntityObjectHandlerReturn($return): void
    {
        $instance = $this->createMock(DataObjectHandler::class);

        if (is_callable($return)) {
            $instance
                ->method('getObject')
                ->will($this->returnCallback($return));
        } else {
            $instance
                ->method('getObject')
                ->willReturn($return);
        }
        // bypass the private constructor
        $property = new ReflectionProperty(DataObjectHandler::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue(null, $instance);
    }

    /**
     * Given a set of steps and an expected custom attribute value, this function performs a set of asserts to validate
     * information such as step key and step attribute value.
     *
     * @param array $actions
     * @param array $expectedValue
     * @param string|null $expectedMergeKey
     *
     * @return void
     */
    private function assertOnMergeKeyAndActionValue(
        array $actions,
        array $expectedValue,
        ?string $expectedMergeKey = null
    ): void {
        $expectedMergeKey = $expectedMergeKey ??
            ActionGroupObjectBuilder::DEFAULT_ACTION_OBJECT_NAME . self::ACTION_GROUP_MERGE_KEY;
        $this->assertArrayHasKey($expectedMergeKey, $actions);

        $action = $actions[$expectedMergeKey];
        $this->assertEquals($expectedMergeKey, $action->getStepKey());
        $this->assertEquals($expectedValue, $action->getCustomActionAttributes());
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
}
