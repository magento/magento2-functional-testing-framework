<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tests\unit\Magento\FunctionalTestFramework\Test\Handlers;

use AspectMock\Test as AspectMock;

use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;

class TestGeneratorTest extends MagentoTestCase
{
    /**
     * Basic test to check exceptions for incorrect entities.
     *
     * @throws \Exception
     */
    public function testEntityException()
    {
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => '{{someEntity.entity}}'
        ]);

        $testObject = new TestObject("sampleTest", ["merge123" => $actionObject], [], [], "filename");

        $testGeneratorObject = TestGenerator::getInstance("", ["sampleTest" => $testObject]);

        AspectMock::double(TestGenerator::class, ['loadAllTestObjects' => ["sampleTest" => $testObject]]);

        $this->expectExceptionMessage("Could not resolve entity reference \"{{someEntity.entity}}\" " .
            "in Action with stepKey \"fakeAction\".\n" .
            "Exception occurred parsing action at StepKey \"fakeAction\" in Test \"sampleTest\"");

        $testGeneratorObject->createAllTestFiles(null, []);
    }

    /**
     * Tests that skipped tests do not have a fully generated body
     *
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testSkippedNoGeneration()
    {
        $actionInput = 'fakeInput';
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => $actionInput
        ]);

        $annotations = ['skip' => ['issue']];
        $testObject = new TestObject("sampleTest", ["merge123" => $actionObject], $annotations, [], "filename");

        $testGeneratorObject = TestGenerator::getInstance("", ["sampleTest" => $testObject]);
        $output = $testGeneratorObject->assembleTestPhp($testObject);

        $this->assertContains('This test is skipped', $output);
        $this->assertNotContains($actionInput, $output);
    }

    /**
     * Tests that skipped tests have a fully generated body when --allowSkipped is passed in
     *
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testAllowSkipped()
    {
        // Mock allowSkipped for TestGenerator
        AspectMock::double(MftfApplicationConfig::class, ['allowSkipped' => true]);

        $actionInput = 'fakeInput';
        $actionObject = new ActionObject('fakeAction', 'comment', [
            'userInput' => $actionInput
        ]);
        $beforeActionInput = 'beforeInput';
        $beforeActionObject = new ActionObject('beforeAction', 'comment', [
            'userInput' => $beforeActionInput
        ]);

        $annotations = ['skip' => ['issue']];
        $beforeHook = new TestHookObject("before", "sampleTest", ['beforeAction' => $beforeActionObject]);
        $testObject = new TestObject(
            "sampleTest",
            ["fakeAction" => $actionObject],
            $annotations,
            ["before" => $beforeHook],
            "filename"
        );

        $testGeneratorObject = TestGenerator::getInstance("", ["sampleTest" => $testObject]);
        $output = $testGeneratorObject->assembleTestPhp($testObject);

        $this->assertNotContains('This test is skipped', $output);
        $this->assertContains($actionInput, $output);
        $this->assertContains($beforeActionInput, $output);
    }
}
