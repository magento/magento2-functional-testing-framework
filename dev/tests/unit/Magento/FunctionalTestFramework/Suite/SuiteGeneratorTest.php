<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Magento\FunctionalTestFramework\Suite;

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
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;
use Magento\FunctionalTestingFramework\Util\Manifest\DefaultTestManifest;
use tests\unit\Util\MagentoTestCase;
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
    public static function setUpBeforeClass(): void
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
    public function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
        $resolverMock = new MockModuleResolverBuilder();
        $resolverMock->setup();
    }

    /**
     * Tests generating a single suite given a set of parsed test data
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

        $mockTestData = array_merge($mockSimpleTest);
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

        $mockTestData = array_merge($mockSimpleTest);
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
     */
    public function testGenerateEmptySuite()
    {
        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockTestData = $testDataArrayBuilder
            ->withName('test')
            ->withAnnotations()
            ->withTestActions()
            ->build();

        $suiteDataArrayBuilder = new SuiteDataArrayBuilder();
        $mockData = $suiteDataArrayBuilder
            ->withName('basicTestSuite')
            ->build();
        unset($mockData['suites']['basicTestSuite'][TestObjectExtractor::TEST_BEFORE_HOOK]);
        unset($mockData['suites']['basicTestSuite'][TestObjectExtractor::TEST_AFTER_HOOK]);

        $this->setMockTestAndSuiteParserOutput($mockTestData, $mockData);

        // set expected error message
        $this->expectExceptionMessage("Suite basicTestSuite is not defined in xml or is invalid");

        // parse and generate suite object with mocked data
        $mockSuiteGenerator = SuiteGenerator::getInstance();
        $mockSuiteGenerator->generateSuite("basicTestSuite");
    }

    /**
     * Tests generating all suites with a suite containing invalid test reference
     */
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
        $mockTestData = array_merge($mockSimpleTest, $mockSimpleTest2);
        $this->setMockTestAndSuiteParserOutput($mockTestData, $mockSuiteData);

        // Make invalid manifest
        $suiteConfig = ['Suite2' => ['Test1']];
        $manifest = TestManifestFactory::makeManifest('default', $suiteConfig);

        // parse and generate suite object with mocked data and manifest
        $mockSuiteGenerator = SuiteGenerator::getInstance();
        $mockSuiteGenerator->generateAllSuites($manifest);

        // assert that no exception for generateAllSuites and suite generation error is stored in GenerationErrorHandler
        $errMessage = 'Cannot reference tests which are not declared as part of suite (Suite: "Suite2" Tests: "Test1")';
        TestLoggingUtil::getInstance()->validateMockLogStatement('error', $errMessage, []);
        $suiteErrors = GenerationErrorHandler::getInstance()->getErrorsByType('suite');
        $this->assertArrayHasKey('Suite2', $suiteErrors);
    }

    /**
     * Tests generating all suites with a non-existing suite
     */
    public function testNonExistentSuiteTestPair()
    {
        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockSimpleTest = $testDataArrayBuilder
            ->withName('Test1')
            ->withAnnotations(['group' => [['value' => 'group1']]])
            ->withTestActions()
            ->build();
        $mockTestData = array_merge($mockSimpleTest);
        $this->setMockTestAndSuiteParserOutput($mockTestData, []);

        // Make invalid manifest
        $suiteConfig = ['Suite3' => ['Test1']];
        $manifest = TestManifestFactory::makeManifest('default', $suiteConfig);

        // parse and generate suite object with mocked data and manifest
        $mockSuiteGenerator = SuiteGenerator::getInstance();
        $mockSuiteGenerator->generateAllSuites($manifest);

        // assert that no exception for generateAllSuites and suite generation error is stored in GenerationErrorHandler
        $errMessage = 'Suite Suite3 is not defined in xml or is invalid.';
        TestLoggingUtil::getInstance()->validateMockLogStatement('error', $errMessage, []);
        $suiteErrors = GenerationErrorHandler::getInstance()->getErrorsByType('suite');
        $this->assertArrayHasKey('Suite3', $suiteErrors);
    }

    /**
     * Tests generating split suites for parallel test generation
     */
    public function testGenerateSplitSuiteFromTest()
    {
        $suiteDataArrayBuilder = new SuiteDataArrayBuilder();
        $mockSuiteData = $suiteDataArrayBuilder
            ->withName('mockSuite')
            ->includeGroups(['group1'])
            ->build();
        $testDataArrayBuilder = new TestDataArrayBuilder();
        $mockSimpleTest1 = $testDataArrayBuilder
            ->withName('simpleTest1')
            ->withAnnotations(['group' => [['value' => 'group1']]])
            ->withTestReference("NonExistantTest")
            ->withTestActions()
            ->build();
        $mockSimpleTest2 = $testDataArrayBuilder
            ->withName('simpleTest2')
            ->withAnnotations(['group' => [['value' => 'group1']]])
            ->withTestActions()
            ->build();
        $mockSimpleTest3 = $testDataArrayBuilder
            ->withName('simpleTest3')
            ->withAnnotations(['group' => [['value' => 'group1']]])
            ->withTestActions()
            ->build();
        $mockTestData = array_merge($mockSimpleTest1, $mockSimpleTest2, $mockSimpleTest3);
        $this->setMockTestAndSuiteParserOutput($mockTestData, $mockSuiteData);

        // Make manifest for split suites
        $suiteConfig = [
            'mockSuite' => [
                'mockSuite_0_G' => ['simpleTest1', 'simpleTest2'],
                'mockSuite_1_G' => ['simpleTest3'],
            ],
        ];
        $manifest = TestManifestFactory::makeManifest('default', $suiteConfig);

        // parse and generate suite object with mocked data and manifest
        $mockSuiteGenerator = SuiteGenerator::getInstance();
        $mockSuiteGenerator->generateAllSuites($manifest);

        // assert last split suite group generated
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            "suite generated",
            ['suite' => 'mockSuite_1_G', 'relative_path' => "_generated" . DIRECTORY_SEPARATOR . "mockSuite_1_G"]
        );
    }

    /**
     * Function used to set mock for parser return and force init method to run between tests.
     *
     * @param array $testData
     * @param array $suiteData
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
     * clean up function runs after each test
     */
    public function tearDown(): void
    {
        GenerationErrorHandler::getInstance()->reset();
    }

    /**
     * clean up function runs after all tests
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
        parent::tearDownAfterClass();
    }
}
