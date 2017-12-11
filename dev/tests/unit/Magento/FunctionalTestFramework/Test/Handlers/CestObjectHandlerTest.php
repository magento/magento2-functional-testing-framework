<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tests\unit\Magento\FunctionalTestFramework\Test\Handlers;

use AspectMock\Test as AspectMock;

use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Test\Handlers\CestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\CestHookObject;
use Magento\FunctionalTestingFramework\Test\Objects\CestObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Parsers\TestDataParser;
use Magento\FunctionalTestingFramework\Test\Util\ActionObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\CestObjectExtractor;
use PHPUnit\Framework\TestCase;

class CestObjectHandlerTest extends TestCase
{
    /**
     * Basic test to validate array => test object conversion
     */
    public function testGetCestObject()
    {
        // set up mock data
        $testCestName = 'testCest';
        $testTestName = 'testTest';
        $testActionBeforeName = 'testActionBefore';
        $testActionAfterName = 'testActionAfter';
        $testTestActionName = 'testActionInTest';
        $testActionType = 'testAction';

        $mockData = [CestObjectExtractor::CEST_ROOT => [
                $testCestName => [
                    CestObjectExtractor::NAME => $testCestName,
                    CestObjectExtractor::CEST_BEFORE_HOOK => [
                        $testActionBeforeName => [
                            ActionObjectExtractor::NODE_NAME => $testActionType,
                            ActionObjectExtractor::TEST_STEP_MERGE_KEY => $testActionBeforeName
                        ]
                    ],
                    CestObjectExtractor::CEST_AFTER_HOOK => [
                        $testActionAfterName => [
                            ActionObjectExtractor::NODE_NAME => $testActionType,
                            ActionObjectExtractor::TEST_STEP_MERGE_KEY => $testActionAfterName

                        ]
                    ],
                    CestObjectExtractor::CEST_ANNOTATIONS => [
                        'group' => [['value' => 'test']]
                    ],
                    $testTestName => [
                        $testTestActionName => [
                            ActionObjectExtractor::NODE_NAME => $testActionType,
                            ActionObjectExtractor::TEST_STEP_MERGE_KEY => $testTestActionName
                        ],
                    ]
                ]
            ]
        ];
        $mockDataParser = AspectMock::double(TestDataParser::class, ['readTestData' => $mockData])->make();
        $instance = AspectMock::double(ObjectManager::class, ['create' => $mockDataParser])
            ->make(); // bypass the private constructor
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);

        // run object handler method
        $coh = CestObjectHandler::getInstance();
        $actualCestObject = $coh->getObject($testCestName);

        // perform asserts
        $expectedBeforeActionObject = new ActionObject($testActionBeforeName, $testActionType, []);
        $expectedAfterActionObject = new ActionObject($testActionAfterName, $testActionType, []);
        $expectedBeforeHookObject = new CestHookObject(
            CestObjectExtractor::CEST_BEFORE_HOOK,
            $testCestName,
            [$expectedBeforeActionObject],
            []
        );
        $expectedAfterHookObject = new CestHookObject(
            CestObjectExtractor::CEST_AFTER_HOOK,
            $testCestName,
            [$expectedAfterActionObject],
            []
        );

        $expectedTestActionObject = new ActionObject($testTestActionName, $testActionType, []);
        $expectedTestObject = new TestObject($testTestName, [$expectedTestActionObject], [], []);

        $expectedCestObject = new CestObject(
            $testCestName,
            [
               'group' => ['test']
            ],
            [
                $testTestName => $expectedTestObject
            ],
            [
                CestObjectExtractor::CEST_BEFORE_HOOK => $expectedBeforeHookObject,
                CestObjectExtractor::CEST_AFTER_HOOK => $expectedAfterHookObject
            ]
        );

        $this->assertEquals($expectedCestObject, $actualCestObject);
    }
}
