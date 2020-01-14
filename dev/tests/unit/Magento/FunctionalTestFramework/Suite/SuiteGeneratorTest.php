<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tests\unit\Magento\FunctionalTestFramework\Suite;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
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
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;
use Magento\FunctionalTestingFramework\Util\Manifest\TestManifestFactory;
use tests\unit\Util\SuiteDataArrayBuilder;
use tests\unit\Util\TestDataArrayBuilder;
use tests\unit\Util\TestLoggingUtil;
use tests\unit\Util\MockModuleResolverBuilder;

class SuiteGeneratorTest extends MagentoTestCase
{
    /**
     * Setup entry append and clear for Suite Generator
     */
    public static function setUpBeforeClass()
    {
        AspectMock::double(SuiteGenerator::class, [
            'clearPreviousSessionConfigEntries' => null,
            'appendEntriesToConfig' => null
        ]);
    }

    /**
     * Before test functionality
     * @return void
     */
    public function setUp()
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
        $resolverMock = new MockModuleResolverBuilder();
        $resolverMock->setup();
    }

    /**
     * Tests generating a single suite given a set of parsed test data
     * @throws \Exception
     */
    public function testGenerateSuite()
    {
        $suiteDataArrayBuilder = new SuiteDataArrayBuilder();
        $mockData = $suiteDataArrayBuilder
            ->withName('basicTestSuite')
            ->withAfterHook()
            ->withBeforeHook()
            ->includeTests(['simpleTest'])
            ->includeGroups(['group1'])
            ->build();

        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockSimpleTest = $testDataArrayBuilder
            ->withName('simpleTest')
            ->withTestActions()
            ->build();

        $mockTestData = ['tests' => array_merge($mockSimpleTest)];
        $this->setMockTestAndSuiteParserOutput($mockTestData, $mockData);

        // parse and generate suite object with mocked data
        $mockSuiteGenerator = SuiteGenerator::getInstance();
        $mockSuiteGenerator->generateSuite("basicTestSuite");

        // assert that expected suite is generated
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            "suite generated",
            ['suite' => 'basicTestSuite', 'relative_path' => "_generated" . DIRECTORY_SEPARATOR . "basicTestSuite"]
        );
    }

    /**
     * Tests generating all suites given a set of parsed test data
     * @throws \Exception
     */
    public function testGenerateAllSuites()
    {
        $suiteDataArrayBuilder = new SuiteDataArrayBuilder();
        $mockData = $suiteDataArrayBuilder
            ->withName('basicTestSuite')
            ->withAfterHook()
            ->withBeforeHook()
            ->includeTests(['simpleTest'])
            ->includeGroups(['group1'])
            ->build();

        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockSimpleTest = $testDataArrayBuilder
            ->withName('simpleTest')
            ->withTestActions()
            ->build();

        $mockTestData = ['tests' => array_merge($mockSimpleTest)];
        $this->setMockTestAndSuiteParserOutput($mockTestData, $mockData);

        // parse and retrieve suite object with mocked data
        $exampleTestManifest = new DefaultTestManifest([], "sample" . DIRECTORY_SEPARATOR . "path");
        $mockSuiteGenerator = SuiteGenerator::getInstance();
        $mockSuiteGenerator->generateAllSuites($exampleTestManifest);

        // assert that expected suites are generated
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            "suite generated",
            ['suite' => 'basicTestSuite', 'relative_path' => "_generated" . DIRECTORY_SEPARATOR . "basicTestSuite"]
        );
    }

    /**
     * Tests attempting to generate a suite with no included/excluded tests and no hooks
     * @throws \Exception
     */
    public function testGenerateEmptySuite()
    {
        $suiteDataArrayBuilder = new SuiteDataArrayBuilder();
        $mockData = $suiteDataArrayBuilder
            ->withName('basicTestSuite')
            ->build();
        unset($mockData['suites']['basicTestSuite'][TestObjectExtractor::TEST_BEFORE_HOOK]);
        unset($mockData['suites']['basicTestSuite'][TestObjectExtractor::TEST_AFTER_HOOK]);

        $mockTestData = null;
        $this->setMockTestAndSuiteParserOutput($mockTestData, $mockData);

        // set expected error message
        $this->expectExceptionMessage("Suites must not be empty. Suite: \"basicTestSuite\"");

        // parse and generate suite object with mocked data
        $mockSuiteGenerator = SuiteGenerator::getInstance();
        $mockSuiteGenerator->generateSuite("basicTestSuite");
    }

    public function testInvalidSuiteTestPair()
    {
        // Mock Suite1 => Test1 and Suite2 => Test2
        $suiteDataArrayBuilder = new SuiteDataArrayBuilder();
        $mockData = $suiteDataArrayBuilder
            ->withName('Suite1')
            ->includeGroups(['group1'])
            ->build();
        $suiteDataArrayBuilder = new SuiteDataArrayBuilder();
        $mockData2 = $suiteDataArrayBuilder
            ->withName('Suite2')
            ->includeGroups(['group2'])
            ->build();
        $mockSuiteData = array_merge_recursive($mockData, $mockData2);

        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockSimpleTest = $testDataArrayBuilder
            ->withName('Test1')
            ->withAnnotations(['group' => [['value' => 'group1']]])
            ->withTestActions()
            ->build();
        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockSimpleTest2 = $testDataArrayBuilder
            ->withName('Test2')
            ->withAnnotations(['group' => [['value' => 'group2']]])
            ->withTestActions()
            ->build();
        $mockTestData = ['tests' => array_merge($mockSimpleTest, $mockSimpleTest2)];
        $this->setMockTestAndSuiteParserOutput($mockTestData, $mockSuiteData);

        // Make invalid manifest
        $suiteConfig = ['Suite2' => ['Test1']];
        $manifest = TestManifestFactory::makeManifest('default', $suiteConfig);

        // Set up Expected Exception
        $this->expectException(TestReferenceException::class);
        $this->expectExceptionMessageRegExp('(Suite: "Suite2" Tests: "Test1")');

        // parse and generate suite object with mocked data and manifest
        $mockSuiteGenerator = SuiteGenerator::getInstance();
        $mockSuiteGenerator->generateAllSuites($manifest);
    }

    public function testNonExistentSuiteTestPair()
    {
        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockSimpleTest = $testDataArrayBuilder
            ->withName('Test1')
            ->withAnnotations(['group' => [['value' => 'group1']]])
            ->withTestActions()
            ->build();
        $mockTestData = ['tests' => array_merge($mockSimpleTest)];
        $this->setMockTestAndSuiteParserOutput($mockTestData, []);

        // Make invalid manifest
        $suiteConfig = ['Suite3' => ['Test1']];
        $manifest = TestManifestFactory::makeManifest('default', $suiteConfig);

        // Set up Expected Exception
        $this->expectException(TestReferenceException::class);
        $this->expectExceptionMessageRegExp('#Suite3 is not defined#');

        // parse and generate suite object with mocked data and manifest
        $mockSuiteGenerator = SuiteGenerator::getInstance();
        $mockSuiteGenerator->generateAllSuites($manifest);
    }

    /**
     * Function used to set mock for parser return and force init method to run between tests.
     *
     * @param array $testData
     * @throws \Exception
     */
    private function setMockTestAndSuiteParserOutput($testData, $suiteData)
    {
        $property = new \ReflectionProperty(SuiteGenerator::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null);

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
        $mockGroupClass = AspectMock::double(
            GroupClassGenerator::class,
            ['generateGroupClass' => 'namespace']
        )->make();
        $mockSuiteClass = AspectMock::double(SuiteGenerator::class, ['generateRelevantGroupTests' => null])->make();
        $instance = AspectMock::double(
            ObjectManager::class,
            ['create' => function ($clazz) use (
                $mockDataParser,
                $mockSuiteDataParser,
                $mockGroupClass,
                $mockSuiteClass
            ) {
                if ($clazz == TestDataParser::class) {
                    return $mockDataParser;
                }
                if ($clazz == SuiteDataParser::class) {
                    return $mockSuiteDataParser;
                }
                if ($clazz == GroupClassGenerator::class) {
                    return $mockGroupClass;
                }
                if ($clazz == SuiteGenerator::class) {
                    return $mockSuiteClass;
                }
            }]
        )->make();
        // bypass the private constructor
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);

        $property = new \ReflectionProperty(SuiteGenerator::class, 'groupClassGenerator');
        $property->setAccessible(true);
        $property->setValue($instance, $instance);
    }

    /**
     * clean up function runs after all tests
     */
    public static function tearDownAfterClass()
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
        parent::tearDownAfterClass();
    }
}
