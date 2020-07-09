<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use Magento\FunctionalTestingFramework\Test\Util\ActionGroupObjectExtractor;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\TestLoggingUtil;

class ActionGroupObjectExtractorTest extends MagentoTestCase
{
    /** @var  ActionGroupObjectExtractor */
    private $testActionGroupObjectExtractor;

    /**
     * Setup method
     */
    public function setUp(): void
    {
        $this->testActionGroupObjectExtractor = new ActionGroupObjectExtractor();
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Tests basic action object extraction with an empty stepKey
     */
    public function testEmptyStepKey()
    {
        $this->expectExceptionMessage(
            "StepKeys cannot be empty.	Action='sampleAction' in Action Group filename.xml"
        );
        $this->testActionGroupObjectExtractor->extractActionGroup($this->createBasicActionObjectArray(""));
    }

    /**
     * Utility function to return mock parser output for testing extraction into ActionObjects.
     *
     * @param string $stepKey
     * @param string $actionGroup
     * @param string $filename
     * @return array
     */
    private function createBasicActionObjectArray(
        $stepKey = 'testAction1',
        $actionGroup = "actionGroup",
        $filename = "filename.xml"
    ) {
        $baseArray = [
            'nodeName' => 'actionGroup',
            'name' => $actionGroup,
            'filename' => $filename,
            $stepKey => [
                "nodeName" => "sampleAction",
                "stepKey" => $stepKey,
                "someAttribute" => "someAttributeValue"
            ]
        ];
        return $baseArray;
    }

    /**
     * clean up function runs after all tests
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
