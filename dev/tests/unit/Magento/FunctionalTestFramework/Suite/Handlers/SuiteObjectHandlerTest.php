<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tests\unit\Magento\FunctionalTestFramework\Suite\Handlers;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Suite\Parsers\SuiteDataParser;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Parsers\TestDataParser;
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;
use tests\unit\Util\SuiteDataArrayBuilder;
use tests\unit\Util\TestDataArrayBuilder;

class SuiteObjectHandlerTest extends MagentoTestCase
{
    /**
     * Tests basic parsing and accesors of suite object and suite object supporting classes
     */
    public function testGetSuiteObject()
    {
        $suiteDataArrayBuilder = new SuiteDataArrayBuilder();
        $mockData = $suiteDataArrayBuilder
            ->withName('basicTestSuite')
            ->withAfterHook()
            ->withBeforeHook()
            ->includeTests(['simpleTest'])
            ->includeGroups(['group1'])
            ->excludeTests(['group1Test2'])
            ->excludeGroups(['group2'])
            ->build();

        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockSimpleTest = $testDataArrayBuilder
            ->withName('simpleTest')
            ->withTestActions()
            ->build();

        $mockGroup1Test1 = $testDataArrayBuilder
            ->withName('group1Test1')
            ->withAnnotations(['group' => [['value' => 'group1']], 'title'=>[['value' => 'group1Test1']]])
            ->withTestActions()
            ->build();

        $mockGroup1Test2 = $testDataArrayBuilder
            ->withName('group1Test2')
            ->withAnnotations(['group' => [['value' => 'group1']], 'title'=>[['value' => 'group1Test2']]])
            ->withTestActions()
            ->build();

        $mockGroup2Test1 = $testDataArrayBuilder
            ->withName('group2Test1')
            ->withAnnotations(['group' => [['value' => 'group2']], 'title'=>[['value' => 'group2Test1']]])
            ->withTestActions()
            ->build();

        $mockTestData = ['tests' => array_merge($mockSimpleTest, $mockGroup1Test1, $mockGroup1Test2, $mockGroup2Test1)];
        $this->setMockTestAndSuiteParserOutput($mockTestData, $mockData);

        // parse and retrieve suite object with mocked data
        $basicTestSuiteObj = SuiteObjectHandler::getInstance()->getObject('basicTestSuite');

        // assert on created suite object
        $this->assertEquals($basicTestSuiteObj->getName(), 'basicTestSuite');
        $this->assertCount(2, $basicTestSuiteObj->getTests());
        $this->assertNotEmpty($basicTestSuiteObj->getBeforeHook());
        $this->assertNotEmpty($basicTestSuiteObj->getAfterHook());
    }

    /**
     * Function used to set mock for parser return and force init method to run between tests.
     *
     * @param array $testData
     * @throws \Exception
     */
    private function setMockTestAndSuiteParserOutput($testData, $suiteData)
    {
        // clear test object handler value to inject parsed content
        $property = new \ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $property->setAccessible(true);
        $property->setValue(null);

        // clear suite object handler value to inject parsed content
        $property = new \ReflectionProperty(SuiteObjectHandler::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataParser = AspectMock::double(TestDataParser::class, ['readTestData' => $testData])->make();
        $mockSuiteDataParser = AspectMock::double(SuiteDataParser::class, ['readSuiteData' => $suiteData])->make();
        $instance = AspectMock::double(
            ObjectManager::class,
            ['create' => function ($clazz) use ($mockDataParser, $mockSuiteDataParser) {
                if ($clazz == TestDataParser::class) {
                    return $mockDataParser;
                }

                if ($clazz == SuiteDataParser::class) {
                    return $mockSuiteDataParser;
                }
            }]
        )->make();
        // bypass the private constructor
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);
    }
}
