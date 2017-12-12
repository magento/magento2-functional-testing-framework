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
use Magento\FunctionalTestingFramework\Test\Handlers\CestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\CestHookObject;
use Magento\FunctionalTestingFramework\Test\Objects\CestObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Parsers\TestDataParser;
use Magento\FunctionalTestingFramework\Test\Util\ActionObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\CestObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;
use PHPUnit\Framework\TestCase;

class CestObjectHandlerTest extends TestCase
{
    /**
     * Mock cest name
     *
     * @var string
     */
    private $testCestName = 'testCest';

    /**
     * Mock test name
     *
     * @var string
     */
    private $testTestName = 'testTest';

    /**
     * Mock before action name
     *
     * @var string
     */
    private $testActionBeforeName = 'testActionBefore';

    /**
     * Mock after action name
     *
     * @var string
     */
    private $testActionAfterName = 'testActionAfter';

    /**
     * Mock test action in test name
     *
     * @var string
     */
    private $testTestActionName = 'testActionInTest';

    /**
     * Mock test action type
     *
     * @var string
     */
    private $testActionType = 'testAction';

    /**
     * Basic test to validate array => test object conversion
     */
    public function testGetCestObject()
    {
        // set up mock data
        $mockData = [CestObjectExtractor::CEST_ROOT => [
            $this->testCestName => [
                CestObjectExtractor::NAME => $this->testCestName,
                CestObjectExtractor::CEST_BEFORE_HOOK => [
                    $this->testActionBeforeName => [
                        ActionObjectExtractor::NODE_NAME => $this->testActionType,
                        ActionObjectExtractor::TEST_STEP_MERGE_KEY => $this->testActionBeforeName
                    ]
                ],
                CestObjectExtractor::CEST_AFTER_HOOK => [
                    $this->testActionAfterName => [
                        ActionObjectExtractor::NODE_NAME => $this->testActionType,
                        ActionObjectExtractor::TEST_STEP_MERGE_KEY => $this->testActionAfterName

                    ]
                ],
                CestObjectExtractor::CEST_ANNOTATIONS => [
                    'group' => [['value' => 'test']]
                ],
                $this->testTestName => [
                    $this->testTestActionName => [
                        ActionObjectExtractor::NODE_NAME => $this->testActionType,
                        ActionObjectExtractor::TEST_STEP_MERGE_KEY => $this->testTestActionName
                    ],
                ]
            ]
        ]
        ];

        $this->setMockParserOutput($mockData);

        // run object handler method
        $coh = CestObjectHandler::getInstance();
        $actualCestObject = $coh->getObject($this->testCestName);

        // perform asserts
        $expectedBeforeActionObject = new ActionObject($this->testActionBeforeName, $this->testActionType, []);
        $expectedAfterActionObject = new ActionObject($this->testActionAfterName, $this->testActionType, []);
        $expectedBeforeHookObject = new CestHookObject(
            CestObjectExtractor::CEST_BEFORE_HOOK,
            $this->testCestName,
            [$expectedBeforeActionObject],
            []
        );
        $expectedAfterHookObject = new CestHookObject(
            CestObjectExtractor::CEST_AFTER_HOOK,
            $this->testCestName,
            [$expectedAfterActionObject],
            []
        );

        $expectedTestActionObject = new ActionObject($this->testTestActionName, $this->testActionType, []);
        $expectedTestObject = new TestObject($this->testTestName, [$expectedTestActionObject], [], []);

        $expectedCestObject = new CestObject(
            $this->testCestName,
            [
               'group' => ['test']
            ],
            [
                $this->testTestName => $expectedTestObject
            ],
            [
                CestObjectExtractor::CEST_BEFORE_HOOK => $expectedBeforeHookObject,
                CestObjectExtractor::CEST_AFTER_HOOK => $expectedAfterHookObject
            ]
        );

        $this->assertEquals($expectedCestObject, $actualCestObject);
    }

    /**
     * Tests the function used to get a series of relevant tests/cests by group
     */
    public function testGetCestsByGroup()
    {
        // set up mock data
        $mockData = [CestObjectExtractor::CEST_ROOT => [
            $this->testCestName => [
                CestObjectExtractor::NAME => $this->testCestName,
                CestObjectExtractor::CEST_ANNOTATIONS => [
                    'group' => [['value' => 'test']]
                ],
                $this->testTestName => [
                    $this->testTestActionName => [
                        ActionObjectExtractor::NODE_NAME => $this->testActionType,
                        ActionObjectExtractor::TEST_STEP_MERGE_KEY => $this->testTestActionName
                    ],
                ]
            ],
            $this->testCestName . '2' => [
                CestObjectExtractor::NAME => $this->testCestName . '2',
                $this->testTestName . 'Include' => [
                    TestObjectExtractor::TEST_ANNOTATIONS => [
                        'group' => [['value' => 'test']]
                    ],
                    $this->testTestActionName => [
                        ActionObjectExtractor::NODE_NAME => $this->testActionType,
                        ActionObjectExtractor::TEST_STEP_MERGE_KEY => $this->testTestActionName
                    ],
                ],
                $this->testTestName . 'Exclude' => [
                    $this->testTestActionName => [
                        ActionObjectExtractor::NODE_NAME => $this->testActionType,
                        ActionObjectExtractor::TEST_STEP_MERGE_KEY => $this->testTestActionName
                    ],
                ]
            ]
        ]];

        $this->setMockParserOutput($mockData);

        // execute test method
        $coh = CestObjectHandler::getInstance();
        $cests = $coh->getCestsByGroup('test');

        // perform asserts
        $this->assertCount(2, $cests);
        $this->assertArrayHasKey($this->testCestName . '2', $cests);
        $actualTests = $cests[$this->testCestName . '2']->getTests();
        $this->assertArrayHasKey($this->testTestName . 'Include', $actualTests);
        $this->assertArrayNotHasKey($this->testTestName . 'Exclude', $actualTests);
    }

    /**
     * Function used to set mock for parser return and force init method to run between tests.
     *
     * @param array $data
     */
    private function setMockParserOutput($data)
    {
        // clear cest object handler value to inject parsed content
        $property = new \ReflectionProperty(CestObjectHandler::class, 'cestObjectHandler');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataParser = AspectMock::double(TestDataParser::class, ['readTestData' => $data])->make();
        $instance = AspectMock::double(ObjectManager::class, ['create' => $mockDataParser])
            ->make(); // bypass the private constructor
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);
    }
}
