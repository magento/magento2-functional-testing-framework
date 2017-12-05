<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Util\ActionMergeUtil;
use Magento\FunctionalTestingFramework\Test\Util\ActionObjectExtractor;
use PHPUnit\Framework\TestCase;
use tests\unit\Util\DataObjectHandlerReflectionUtil;

class ActionMergeUtilTest extends TestCase
{
    /**
     * Static ActionMergeUtil for all tests.
     *
     * @var ActionMergeUtil
     */
    private static $MERGE_UTIL;

    /**
     * Set up method for ActionMergeUtil tests.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$MERGE_UTIL = new ActionMergeUtil("actionMergeUtilTest", "TestCase");
    }

    /**
     * Test to validate actions are properly ordered during a merge.
     *
     * @return void
     */
    public function testResolveActionStepOrdering()
    {
        $actions = [];
        $actionsLength = 11;

        $testObjNamePosBeforeFirst = 'testBeforeBeforeMerge';
        $testObjNamePosFirst = 'testBeforeMerge0';
        $testObjNamePosEnd = 'testAfterMerge10';
        $testObjNamePosAfterEnd = 'testAfterAfterMerge10';

        for ($i = 1; $i < $actionsLength; $i++) {
            $stepKey = 'stepKey'. $i;
            $type = 'testType';
            $actionAttributes = [];

            $actions[] = new ActionObject($stepKey, $type, $actionAttributes);
        }

        $actions[] = new ActionObject(
            $testObjNamePosAfterEnd,
            'stepType',
            [],
            $testObjNamePosEnd,
            ActionObject::MERGE_ACTION_ORDER_AFTER
        );
        $actions[] = new ActionObject(
            $testObjNamePosBeforeFirst,
            'stepType',
            [],
            $testObjNamePosFirst,
            ActionObjectExtractor::TEST_ACTION_BEFORE
        );
        $actions[] = new ActionObject(
            $testObjNamePosFirst,
            'stepType',
            [],
            'stepKey1',
            ActionObjectExtractor::TEST_ACTION_BEFORE
        );
        $actions[] = new ActionObject(
            $testObjNamePosEnd,
            'stepType',
            [],
            'stepKey' . (string)($actionsLength - 1),
            ActionObject::MERGE_ACTION_ORDER_AFTER
        );

        $orderedActions = self::$MERGE_UTIL->resolveActionSteps($actions);
        $orderedActionKeys = array_keys($orderedActions);

        $this->assertEquals($testObjNamePosBeforeFirst, $orderedActionKeys[0]);
        $this->assertEquals($testObjNamePosFirst, $orderedActionKeys[1]);
        $this->assertEquals($testObjNamePosEnd, $orderedActionKeys[$actionsLength + 1]);
        $this->assertEquals($testObjNamePosAfterEnd, $orderedActionKeys[$actionsLength + 2]);
    }

    /**
     * Test to validate action steps properly resolve section element references.
     *
     * @return void
     */
    public function testResolveActionStepSectionData()
    {
        $this->markTestIncomplete('TODO');
    }

    /**
     * Test to validate action steps properly resolve page references.
     *
     * @return void
     */
    public function testResolveActionStepPageData()
    {
        $this->markTestIncomplete('TODO');
    }

    /**
     * Test to validate action steps properly resolve entity data references.
     *
     * @return void
     */
    public function testResolveActionStepEntityData()
    {
        $dataObjectName = 'myObject';
        $dataObjectType = 'testObject';
        $dataFieldName = 'myfield';
        $dataFieldValue = 'myValue';
        $userInputKey = "userInput";
        $userinputValue = "{{" . "${dataObjectName}.${dataFieldName}}}";
        $actionName = "myAction";
        $actionType = "myCustomType";

        // Set up mock data object
        $mockData = [$dataFieldName => $dataFieldValue];
        $mockDataObject = new EntityDataObject($dataObjectName, $dataObjectType, $mockData, null, null, null);

        // Set up mock DataObject Handler
        $mockDOHInstance = AspectMock::double(DataObjectHandler::class, ['getObject' => $mockDataObject])->make();
        AspectMock::double(DataObjectHandler::class, ['getInstance' => $mockDOHInstance]);

        // Create test object and action object
        $actionAttributes = [$userInputKey => $userinputValue];
        $actions[$actionName] = new ActionObject($actionName, $actionType, $actionAttributes);

        $this->assertEquals($userinputValue, $actions[$actionName]->getCustomActionAttributes()[$userInputKey]);

        $mergeUtil = new ActionMergeUtil("test", "TestCase");
        $resolvedActions = $mergeUtil->resolveActionSteps($actions);

        $this->assertEquals($dataFieldValue, $resolvedActions[$actionName]->getCustomActionAttributes()[$userInputKey]);
    }
}
