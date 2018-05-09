<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Suite\SuiteGenerator;
use Magento\FunctionalTestingFramework\Suite\Generators\GroupClassGenerator;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Suite\Parsers\SuiteDataParser;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Parsers\TestDataParser;
use Magento\FunctionalTestingFramework\Util\Manifest\DefaultTestManifest;
use PHPUnit\Framework\TestCase;
use tests\unit\Util\SuiteDataArrayBuilder;
use tests\unit\Util\TestDataArrayBuilder;

class ObjectExtensionTest extends TestCase
{
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
        ->withTestActions($mockActions)
        ->build();

        $mockExtendedTest = $testDataArrayBuilder
            ->withName('extendedTest')
            ->withTestReference("simpleTest")
            ->build();

        $mockTestData = ['tests' => array_merge($mockSimpleTest, $mockExtendedTest)];
        $this->setMockTestOutput($mockTestData);

        $this->expectOutputString("Extending Test: simpleTest => extendedTest" . PHP_EOL);

        // parse and generate test object with mocked data
        $testObject = TestObjectHandler::getInstance()->getObject('extendedTest');

        // assert that expected test is generated
        $this->assertEquals($testObject->getParentName(), "simpleTest");
        $this->assertArrayHasKey("mockStep", $testObject->getOrderedActions());
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

        $this->expectExceptionMessage("Parent Test simpleTest not defined for Test extendedTest.");

        // parse and generate test object with mocked data
        TestObjectHandler::getInstance()->getObject('extendedTest');
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
            ->withTestActions()
            ->withTestReference("anotherTest")
            ->build();

        $mockExtendedTest = $testDataArrayBuilder
            ->withName('extendedTest')
            ->withTestReference("simpleTest")
            ->build();

        $mockTestData = ['tests' => array_merge($mockParentTest, $mockSimpleTest, $mockExtendedTest)];
        $this->setMockTestOutput($mockTestData);

        $this->expectOutputString("Extending Test: anotherTest => simpleTest" . PHP_EOL);
        $this->expectExceptionMessage("Cannot extend a test that already extends another test. Test: simpleTest");

        // parse and generate test object with mocked data
        TestObjectHandler::getInstance()->getObject('extendedTest');

    }

    /**
     * Function used to set mock for parser return and force init method to run between tests.
     *
     * @param array $testData
     * @throws \Exception
     */
    private function setMockTestOutput($testData)
    {
        // clear test object handler value to inject parsed content
        $property = new \ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataParser = AspectMock::double(TestDataParser::class, ['readTestData' => $testData])->make();
        $instance = AspectMock::double(
            ObjectManager::class,
            ['create' => function ($clazz) use (
                $mockDataParser
            ) {
                if ($clazz == TestDataParser::class) {
                    return $mockDataParser;
                }
            }]
        )->make();
        // bypass the private constructor
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);
    }
}
