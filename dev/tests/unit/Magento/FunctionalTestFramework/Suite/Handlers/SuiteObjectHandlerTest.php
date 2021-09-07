<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Suite\Handlers;

use Exception;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Suite\Parsers\SuiteDataParser;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Parsers\TestDataParser;
use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\SuiteDataArrayBuilder;
use tests\unit\Util\TestDataArrayBuilder;

class SuiteObjectHandlerTest extends MagentoTestCase
{
    /**
     * Tests basic parsing and accessors of suite object and suite object supporting classes.
     *
     * @return void
     * @throws Exception
     */
    public function testGetSuiteObject(): void
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

        $mockTestData = array_merge($mockSimpleTest, $mockGroup1Test1, $mockGroup1Test2, $mockGroup2Test1);
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
     * @param array $suiteData
     *
     * @return void
     * @throws Exception
     */
    private function setMockTestAndSuiteParserOutput(array $testData, array $suiteData): void
    {
        // clear test object handler value to inject parsed content
        $property = new ReflectionProperty(TestObjectHandler::class, 'testObjectHandler');
        $property->setAccessible(true);
        $property->setValue(null);

        // clear suite object handler value to inject parsed content
        $property = new ReflectionProperty(SuiteObjectHandler::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockDataParser = $this->createMock(TestDataParser::class);
        $mockDataParser
            ->method('readTestData')
            ->willReturn($testData);

        $mockSuiteDataParser = $this->createMock(SuiteDataParser::class);
        $mockSuiteDataParser
            ->method('readSuiteData')
            ->willReturn($suiteData);

        $instance = $this->createMock(ObjectManager::class);
        $instance
            ->method('create')
            ->will(
                $this->returnCallback(
                    function ($clazz) use ($mockDataParser, $mockSuiteDataParser) {
                        if ($clazz === TestDataParser::class) {
                            return $mockDataParser;
                        }

                        if ($clazz === SuiteDataParser::class) {
                            return $mockSuiteDataParser;
                        }

                        return null;
                    }
                )
            );

        $property = new ReflectionProperty(ObjectManager::class, 'instance');
        $property->setAccessible(true);
        $property->setValue($instance);
    }
}
