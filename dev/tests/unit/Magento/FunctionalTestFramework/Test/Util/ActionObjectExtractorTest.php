<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Util\ActionObjectExtractor;
use PHPUnit\Framework\TestCase;

class ActionObjectExtractorTest extends TestCase
{
    /** @var  ActionObjectExtractor */
    private $testActionObjectExtractor;

    /**
     * Setup method
     */
    public function setUp()
    {
        $this->testActionObjectExtractor = new ActionObjectExtractor();
    }

    /**
     * Tests basic action object extraction with a valid parser array.
     */
    public function testBasicActionObjectExtration()
    {
        $actionObjects = $this->testActionObjectExtractor->extractActions($this->createBasicActionObjectArray());
        $this->assertCount(1, $actionObjects);

        /** @var ActionObject $firstElement */
        $firstElement = array_values($actionObjects)[0];
        $this->assertEquals('testAction1', $firstElement->getStepKey());
        $this->assertCount(1, $firstElement->getCustomActionAttributes());
    }

    /**
     * Tests an invalid merge order reference (i.e. a step referencing itself).
     */
    public function testInvalidMergeOrderReference()
    {
        $invalidArray = $this->createBasicActionObjectArray('invalidTestAction1', 'invalidTestAction1');

        $this->expectException('\Magento\FunctionalTestingFramework\Exceptions\TestReferenceException');
        $expectedExceptionMessage = "Invalid ordering configuration in test TestWithSelfReferencingStepKey with step" .
            " key(s):\n\tinvalidTestAction1\n";
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->testActionObjectExtractor->extractActions($invalidArray, 'TestWithSelfReferencingStepKey');
    }

    /**
     * Validates a warning is printed to the console when multiple actions reference the same actions for merging.
     */
    public function testAmbiguousMergeOrderRefernece()
    {
        $ambiguousArray = $this->createBasicActionObjectArray('testAction1');
        $ambiguousArray = array_merge(
            $ambiguousArray,
            $this->createBasicActionObjectArray('testAction2', 'testAction1')
        );

        $ambiguousArray = array_merge(
            $ambiguousArray,
            $this->createBasicActionObjectArray('testAction3', null, 'testAction1')
        );

        $outputString = "multiple actions referencing step key testAction1 in test AmbiguousRefTest:\n" .
            "\ttestAction2\n" .
            "\ttestAction3\n";

        $this->expectOutputString($outputString);
        $this->testActionObjectExtractor->extractActions($ambiguousArray, 'AmbiguousRefTest');
    }

    /**
     * Utility function to return mock parser output for testing extraction into ActionObjects.
     *
     * @param string $stepKey
     * @param string $before
     * @param string $after
     * @return array
     */
    private function createBasicActionObjectArray($stepKey = 'testAction1', $before = null, $after = null)
    {
        $baseArray = [
            $stepKey => [
                "nodeName" => "sampleAction",
                "stepKey" => $stepKey,
                "someAttribute" => "someAttributeValue"
            ]
        ];

        if ($before) {
            $baseArray[$stepKey] = array_merge($baseArray[$stepKey], ['before' => $before]);
        }

        if ($after) {
            $baseArray[$stepKey] = array_merge($baseArray[$stepKey], ['after' => $after]);
        }

        return $baseArray;
    }
}
