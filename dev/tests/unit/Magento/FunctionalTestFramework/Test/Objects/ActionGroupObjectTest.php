<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Test\Objects;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Page\Handlers\SectionObjectHandler;
use Magento\FunctionalTestingFramework\Page\Objects\ElementObject;
use Magento\FunctionalTestingFramework\Page\Objects\SectionObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\ArgumentObject;
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;
use tests\unit\Util\ActionGroupObjectBuilder;
use tests\unit\Util\EntityDataObjectBuilder;
use tests\unit\Util\TestLoggingUtil;

class ActionGroupObjectTest extends MagentoTestCase
{
    const ACTION_GROUP_MERGE_KEY = 'TestKey';

    /**
     * Before test functionality
     * @return void
     */
    public function setUp()
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Tests a string literal in an action group
     */
    public function testGetStepsWithDefaultCase()
    {
        $entity = (new EntityDataObjectBuilder())
            ->withDataFields(['field1' => 'testValue'])
            ->build();
        $this->setEntityObjectHandlerReturn($entity);
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())->build();
        $steps = $actionGroupUnderTest->getSteps(null, self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => 'literal']);
    }

    /**
     * Tests a data reference in an action group, replaced by the user
     */
    public function testGetStepsWithCustomArgs()
    {
        $this->setEntityObjectHandlerReturn(function ($entityName) {
            if ($entityName == "data2") {
                return (new EntityDataObjectBuilder())->withDataFields(['field2' => 'testValue2'])->build();
            }
        });

        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject('action1', 'testAction', ['userInput' => '{{arg1.field2}}'])])
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(['arg1' => 'data2'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => 'testValue2']);

        // entity.field as argument
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject('action1', 'testAction', ['userInput' => '{{arg1}}'])])
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(['arg1' => 'data2.field2'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => 'testValue2']);

        // String Data
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject('action1', 'testAction', ['userInput' => '{{simple}}'])])
            ->withArguments([new ArgumentObject('simple', null, 'string')])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(['simple' => 'override'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => 'override']);
    }

    /**
     * Tests a data reference in an action group replaced with a persisted reference.
     */
    public function testGetStepsWithPersistedArgs()
    {
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject('action1', 'testAction', ['userInput' => '{{arg1.field2}}'])])
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(['arg1' => '$data3$'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => '$data3.field2$']);

        // Simple Data
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject('action1', 'testAction', ['userInput' => '{{simple}}'])])
            ->withArguments([new ArgumentObject('simple', null, 'string')])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(['simple' => '$data3.field2$'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => '$data3.field2$']);
    }

    /**
     * Tests a data reference in an action group replaced with a data.field reference.
     */
    public function testGetStepsWithNoFieldArg()
    {
        $this->setEntityObjectHandlerReturn(function ($entityName) {
            if ($entityName == "data2") {
                return (new EntityDataObjectBuilder())->withDataFields(['field2' => 'testValue2'])->build();
            }
        });

        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject('action1', 'testAction', ['userInput' => '{{arg1}}'])])
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(['arg1' => 'data2.field2'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => 'testValue2']);
    }

    /**
     * Tests a data reference in an action group resolved with its default state.
     */
    public function testGetStepsWithNoArgs()
    {
        $this->setEntityObjectHandlerReturn(function ($entityName) {
            if ($entityName == "data1") {
                return (new EntityDataObjectBuilder())->withDataFields(['field1' => 'testValue'])->build();
            }
        });

        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject('action1', 'testAction', ['userInput' => '{{data1.field1}}'])])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(null, self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => 'testValue']);
    }

    /**
     * Tests a parameterized section reference in an action group resolved with user args.
     */
    public function testGetStepsWithParameterizedArg()
    {
        // Mock Entity Object Handler
        $this->setEntityObjectHandlerReturn(function ($entityName) {
            if ($entityName == "data2") {
                return (new EntityDataObjectBuilder())->withDataFields(['field2' => 'testValue2'])->build();
            }
        });
        // mock the section object handler response
        $element = new ElementObject("element1", "textArea", ".selector {{var1}}", null, null, true);
        $section = new SectionObject("testSection", ["element1" => $element]);
        // bypass the private constructor
        $sectionInstance = AspectMock::double(SectionObjectHandler::class, ['getObject' => $section])->make();
        AspectMock::double(SectionObjectHandler::class, ['getInstance' => $sectionInstance]);

        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects(
                [new ActionObject('action1', 'testAction', ['selector' => '{{section1.element1(arg1.field2)}}'])]
            )
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        // XML Data
        $steps = $actionGroupUnderTest->getSteps(['arg1' => 'data2'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['selector' => '.selector testValue2']);

        // Persisted Data
        $steps = $actionGroupUnderTest->getSteps(['arg1' => '$data2$'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['selector' => '.selector $data2.field2$']);
    }

    /**
     * Tests a parameterized section reference in an action group resolved with user simpleArgs.
     */
    public function testGetStepsWithParameterizedSimpleArg()
    {
        // Mock Entity Object Handler
        $this->setEntityObjectHandlerReturn(function ($entityName) {
            if ($entityName == "data2") {
                return (new EntityDataObjectBuilder())->withDataFields(['field2' => 'testValue2'])->build();
            }
        });
        // mock the section object handler response
        $element = new ElementObject("element1", "textArea", ".selector {{var1}}", null, null, true);
        $section = new SectionObject("testSection", ["element1" => $element]);
        // bypass the private constructor
        $sectionInstance = AspectMock::double(SectionObjectHandler::class, ['getObject' => $section])->make();
        AspectMock::double(SectionObjectHandler::class, ['getInstance' => $sectionInstance]);

        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects(
                [new ActionObject('action1', 'testAction', ['selector' => '{{section1.element1(simple)}}'])]
            )
            ->withArguments([new ArgumentObject('simple', null, 'string')])
            ->build();

        // String Literal
        $steps = $actionGroupUnderTest->getSteps(['simple' => 'stringLiteral'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['selector' => '.selector stringLiteral']);

        // String Literal w/ data-like structure
        $steps = $actionGroupUnderTest->getSteps(['simple' => 'data2.field2'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['selector' => '.selector data2.field2']);

        // Persisted Data
        $steps = $actionGroupUnderTest->getSteps(['simple' => '$someData.field1$'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['selector' => '.selector $someData.field1$']);
    }

    /**
     * Tests a data reference in an action group resolved with a persisted reference used in another function.
     */
    public function testGetStepsWithOuterScopePersistence()
    {
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects([new ActionObject('action1', 'testAction', ['userInput' => '{{arg1.field1}}'])])
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        $steps = $actionGroupUnderTest->getSteps(['arg1' => '$$someData$$'], self::ACTION_GROUP_MERGE_KEY);
        $this->assertOnMergeKeyAndActionValue($steps, ['userInput' => '$$someData.field1$$']);
    }

    /**
     * Tests an action group with mismatching args.
     */
    public function testExceptionOnMissingActions()
    {
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        $this->expectException(TestReferenceException::class);
        $this->expectExceptionMessageRegExp('/Arguments missed .* for actionGroup/');
        $actionGroupUnderTest->getSteps(['arg2' => 'data1'], self::ACTION_GROUP_MERGE_KEY);
    }

    /**
     * Tests an action group with missing args.
     */
    public function testExceptionOnMissingArguments()
    {
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withArguments([new ArgumentObject('arg1', null, 'entity')])
            ->build();

        $this->expectException(TestReferenceException::class);
        $this->expectExceptionMessageRegExp('/Arguments missed .* for actionGroup/');
        $actionGroupUnderTest->getSteps(null, self::ACTION_GROUP_MERGE_KEY);
    }

    public function testStepKeyReplacementFilter() {

//        "executeJS",
//        "magentoCLI",
//        "generateDate",
//        "formatMoney",
//        "deleteData",
//        "getData",
//        "updateData",
//        "createData",
//        "grabAttributeFrom",
//        "grabCookie",
//        "grabFromCurrentUrl",
//        "grabMultiple",
//        "grabPageSource",
//        "grabTextFrom",
//        "grabValueFrom"



//        $expectedArray = ["stepKey", "stepKey"];
//        $relevantStepKeys= ["stepKey"];


        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withActionObjects(
                [new ActionObject('executeJSStepKey' ,'executeJS', ['selector' => 'value'])]
                [new ActionObject('magentoCLIStepKey' ,'magentoCLI', ['selector' => 'value'])]
                [new ActionObject('generateDateStepKey' ,'generateDate', ['selector' => 'value'])]
                [new ActionObject('formatMoneyStepKey' ,'formatMoney', ['selector' => 'value'])]
                [new ActionObject('deleteDataStepKey' ,'deleteData', ['selector' => 'value'])]
                [new ActionObject('getDataStepKey' ,'getData', ['selector' => 'value'])]
                [new ActionObject('updateDataStepKey' ,'updateData', ['selector' => 'value'])]
                [new ActionObject('createDataStepKey' ,'createData', ['selector' => 'value'])]
                [new ActionObject('grabAttributeFromStepKey' ,'grabAttributeFrom', ['selector' => 'value'])]
                [new ActionObject('grabCookieStepKey' ,'grabCookie', ['selector' => 'value'])]
                [new ActionObject('grabFromCurrentUrlStepKey' ,'grabFromCurrentUrl', ['selector' => 'value'])]
                [new ActionObject('grabMultipleStepKey' ,'grabMultiple', ['selector' => 'value'])]
                [new ActionObject('grabPageSourceStepKey' ,'grabPageSource', ['selector' => 'value'])]
                [new ActionObject('grabTextFromStepKey' ,'grabTextFrom', ['selector' => 'value'])]
                [new ActionObject('grabValueFromStepKey' ,'grabValueFrom', ['selector' => 'value'])]
            )
            ->build();

        //operate
        $result = $actionGroupUnderTest->extractStepKeys();

        //assert
        $this->assertEquals('executeJSStepKey', $result[0]);
        $this->assertEquals('magentoCLIStepKey', $result[1]);
        $this->assertEquals('generateDateStepKey', $result[2]);
        $this->assertEquals('formatMoneyStepKey', $result[3]);
        $this->assertEquals('deleteDataStepKey', $result[4]);
        $this->assertEquals('getDataStepKey', $result[5]);
        $this->assertEquals('updateDataStepKey', $result[6]);
        $this->assertEquals('createDataStepKey', $result[7]);
        $this->assertEquals('grabAttributeFromStepKey', $result[8]);
        $this->assertEquals('grabCookieStepKey', $result[9]);
        $this->assertEquals('grabFromCurrentUrlStepKey', $result[10]);
        $this->assertEquals('grabMultipleStepKey', $result[11]);
        $this->assertEquals('grabPageSourceStepKey', $result[12]);
        $this->assertEquals('grabTextFromStepKey', $result[13]);
        $this->assertEquals('grabValueFromStepKey', $result[14]);
    }

//    public function testStepKeyReplacementFilterIgnore() {
////        $expectedArray = ["stepKey", "stepKey"];
//        $relevantStepKeys= ["stepKey"];
//
//        //Object
//        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
//            ->withActionObjects(
//                [new ActionObject('clickStepKey' ,'click', ['selector' => 'value'])]
//                [new ActionObject('conditionalClickStepKey' ,'conditionalClick', ['selector' => 'value'])]
//                [new ActionObject('fillFieldStepKey' ,'fillField', ['selector' => 'value'])]
//            )
//            ->build();
//
//
//        //operate
//        $result = $actionGroupUnderTest->extractStepKeys();
//
//
//        //assert
//        $this->assertEquals('click', $result[0]);
//        $this->assertEquals('conditionalClick', $result[1]);
//        $this->assertEquals
//
//    }



    /**
     * This function takes a desired return for the EntityObjectHandler mock and performs set up of the mock for the
     * duration of a single test case.
     *
     * @param mixed $return
     * @return void
     */
    private function setEntityObjectHandlerReturn($return)
    {
        $instance = AspectMock::double(DataObjectHandler::class, ['getObject' => $return])
            ->make(); // bypass the private constructor
        AspectMock::double(DataObjectHandler::class, ['getInstance' => $instance]);
    }

    /**
     * Given a set of steps and an expected custom attribute value, this function performs a set of asserts to validate
     * information such as step key and step attribute value.
     *
     * @param array $actions
     * @param array $expectedValue
     * @param string $expectedMergeKey
     * @return void
     */
    private function assertOnMergeKeyAndActionValue($actions, $expectedValue, $expectedMergeKey = null)
    {
        $expectedMergeKey = $expectedMergeKey ??
            ActionGroupObjectBuilder::DEFAULT_ACTION_OBJECT_NAME . self::ACTION_GROUP_MERGE_KEY;
        $this->assertArrayHasKey($expectedMergeKey, $actions);

        $action = $actions[$expectedMergeKey];
        $this->assertEquals($expectedMergeKey, $action->getStepKey());
        $this->assertEquals($expectedValue, $action->getCustomActionAttributes());
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
