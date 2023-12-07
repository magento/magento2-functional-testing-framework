<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Suite\SuiteGenerator;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;
use Magento\FunctionalTestingFramework\Util\Manifest\DefaultTestManifest;
use Magento\FunctionalTestingFramework\Util\Manifest\ParallelByTimeTestManifest;
use Magento\FunctionalTestingFramework\Util\Manifest\ParallelByGroupTestManifest;
use Magento\FunctionalTestingFramework\Util\Manifest\TestManifestFactory;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;
use Symfony\Component\Yaml\Yaml;
use tests\unit\Util\TestLoggingUtil;
use tests\util\MftfTestCase;

class SuiteGenerationTest extends MftfTestCase
{
    const RESOURCES_DIR = TESTS_BP . DIRECTORY_SEPARATOR . 'verification' . DIRECTORY_SEPARATOR . 'Resources';
    const CONFIG_YML_FILE = TESTS_BP . DIRECTORY_SEPARATOR . SuiteGenerator::YAML_CODECEPTION_CONFIG_FILENAME;
    const GENERATE_RESULT_DIR = TESTS_BP .
        DIRECTORY_SEPARATOR .
        "verification" .
        DIRECTORY_SEPARATOR .
        "_generated" .
        DIRECTORY_SEPARATOR;

    /**
     * Array which stores state of any existing config.yml groups
     *
     * @var array
     */
    private static $TEST_GROUPS = [];

    /**
     * Set up config.yml for testing
     */
    public static function setUpBeforeClass(): void
    {
        // destroy _generated if it exists
        if (file_exists(self::GENERATE_RESULT_DIR)) {
            DirSetupUtil::rmdirRecursive(self::GENERATE_RESULT_DIR);
        }
    }

    public function setUp(): void
    {
        // copy config yml file to test dir
        $fileSystem = new \Symfony\Component\Filesystem\Filesystem();
        $fileSystem->copy(
            realpath(
                FW_BP
                . DIRECTORY_SEPARATOR
                . 'etc'
                . DIRECTORY_SEPARATOR
                . 'config'
                . DIRECTORY_SEPARATOR
                . 'codeception.dist.yml'
            ),
            self::CONFIG_YML_FILE
        );

        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Test basic generation of a suite
     */
    public function testSuiteGeneration1()
    {
        $groupName = 'functionalSuite1';

        $expectedContents = SuiteTestReferences::$data[$groupName];

        // Generate the Suite
        SuiteGenerator::getInstance()->generateSuite($groupName);

        // Validate log message and add group name for later deletion
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            "suite generated",
            ['suite' => $groupName, 'relative_path' => "_generated" . DIRECTORY_SEPARATOR . $groupName]
        );

        self::$TEST_GROUPS[] = $groupName;

        // Validate Yaml file updated
        $yml = Yaml::parse(file_get_contents(self::CONFIG_YML_FILE));
        $this->assertArrayHasKey($groupName, $yml['groups']);

        $suiteResultBaseDir = self::GENERATE_RESULT_DIR .
            DIRECTORY_SEPARATOR .
            $groupName .
            DIRECTORY_SEPARATOR;

        // Validate tests have been generated
        $dirContents = array_diff(scandir($suiteResultBaseDir), ['..', '.']);

        foreach ($expectedContents as $expectedFile) {
            $this->assertTrue(in_array($expectedFile, $dirContents));
        }
    }

    /**
     * Test generation of parallel suite groups
     */
    public function testSuiteGenerationParallelByTime()
    {
        $groupName = 'functionalSuite1';

        $expectedGroups = [
            'functionalSuite1_0_G',
            'functionalSuite1_1_G',
            'functionalSuite1_2_G',
            'functionalSuite1_3_G'
        ];

        $expectedContents = SuiteTestReferences::$data[$groupName];

        //createParallelManifest
        /** @var ParallelByTimeTestManifest $parallelManifest */
        $parallelManifest = TestManifestFactory::makeManifest("parallelByTime", ["functionalSuite1" => []]);

        // Generate the Suite
        $parallelManifest->createTestGroups(1);
        SuiteGenerator::getInstance()->generateAllSuites($parallelManifest);

        // Validate log message (for final group) and add group name for later deletion
        $expectedGroup = $expectedGroups[count($expectedGroups)-1] ;
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            "suite generated",
            ['suite' => $expectedGroup, 'relative_path' => "_generated" . DIRECTORY_SEPARATOR . $expectedGroup]
        );

        self::$TEST_GROUPS[] = $groupName;

        // Validate Yaml file updated
        $yml = Yaml::parse(file_get_contents(self::CONFIG_YML_FILE));
        $this->assertEquals(array_intersect($expectedGroups, array_keys($yml['groups'])), $expectedGroups);

        foreach ($expectedGroups as $expectedFolder) {
            $suiteResultBaseDir = self::GENERATE_RESULT_DIR .
                DIRECTORY_SEPARATOR .
                $expectedFolder .
                DIRECTORY_SEPARATOR;

            // Validate tests have been generated
            $dirContents = array_diff(scandir($suiteResultBaseDir), ['..', '.']);

            //Validate only one test has been added to each group since lines are set to 1
            $this->assertEquals(1, count($dirContents));
            $this->assertContains(array_values($dirContents)[0], $expectedContents);
        }
    }

    /**
     * Test generation of parallel suite groups
     */
    public function testSuiteGenerationParallelByGroup()
    {
        $groupName = 'functionalSuite1';

        $expectedGroups = [
            'functionalSuite1_0_G',
            'functionalSuite1_1_G',
        ];

        $expectedContents = SuiteTestReferences::$data[$groupName];

        //createParallelManifest
        /** @var ParallelByGroupTestManifest $parallelManifest */
        $parallelManifest = TestManifestFactory::makeManifest("parallelByGroup", ["functionalSuite1" => []]);

        // Generate the Suite
        $parallelManifest->createTestGroups(2);
        SuiteGenerator::getInstance()->generateAllSuites($parallelManifest);

        // Validate log message (for final group) and add group name for later deletion
        $expectedGroup = $expectedGroups[count($expectedGroups)-1] ;
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            "suite generated",
            ['suite' => $expectedGroup, 'relative_path' => "_generated" . DIRECTORY_SEPARATOR . $expectedGroup]
        );

        self::$TEST_GROUPS[] = $groupName;

        // Validate Yaml file updated
        $yml = Yaml::parse(file_get_contents(self::CONFIG_YML_FILE));
        $this->assertEquals(array_intersect($expectedGroups, array_keys($yml['groups'])), $expectedGroups);

        foreach ($expectedGroups as $expectedFolder) {
            $suiteResultBaseDir = self::GENERATE_RESULT_DIR .
                DIRECTORY_SEPARATOR .
                $expectedFolder .
                DIRECTORY_SEPARATOR;

            // Validate tests have been generated
            $dirContents = array_diff(scandir($suiteResultBaseDir), ['..', '.']);

            //Validate two test has been added to each group since lines are set to 1
            $this->assertEquals(2, count($dirContents));
            $this->assertContains(array_values($dirContents)[0], $expectedContents);
        }
    }

    /**
     * Test hook groups generated during suite generation
     */
    public function testSuiteGenerationHooks()
    {
        $groupName = 'functionalSuiteHooks';

        $expectedContents = SuiteTestReferences::$data[$groupName];

        // Generate the Suite
        SuiteGenerator::getInstance()->generateSuite($groupName);

        // Validate log message and add group name for later deletion
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            "suite generated",
            ['suite' => $groupName, 'relative_path' => "_generated" . DIRECTORY_SEPARATOR . $groupName]
        );
        self::$TEST_GROUPS[] = $groupName;

        // Validate Yaml file updated
        $yml = Yaml::parse(file_get_contents(self::CONFIG_YML_FILE));
        $this->assertArrayHasKey($groupName, $yml['groups']);

        $suiteResultBaseDir = self::GENERATE_RESULT_DIR .
            DIRECTORY_SEPARATOR .
            $groupName .
            DIRECTORY_SEPARATOR;

        // Validate tests have been generated
        $dirContents = array_diff(scandir($suiteResultBaseDir), ['..', '.']);

        foreach ($expectedContents as $expectedFile) {
            $this->assertTrue(in_array($expectedFile, $dirContents));
        }

        //assert group file created and contains correct contents
        $groupFile = PROJECT_ROOT .
            DIRECTORY_SEPARATOR .
            "src" .
            DIRECTORY_SEPARATOR .
            "Magento" .
            DIRECTORY_SEPARATOR .
            "FunctionalTestingFramework" .
            DIRECTORY_SEPARATOR .
            "Group" .
            DIRECTORY_SEPARATOR .
            $groupName .
            ".php";

        $this->assertTrue(file_exists($groupFile));
        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . $groupName . ".txt",
            $groupFile
        );
    }

    /**
     * Test generation of parallel suite groups
     */
    public function testSuiteGenerationSingleRun()
    {
        //using functionalSuite2 to avoid directory caching
        $groupName = 'functionalSuite2';

        $expectedContents = SuiteTestReferences::$data[$groupName];

        //createParallelManifest
        /** @var DefaultTestManifest $parallelManifest */
        $singleRunManifest = TestManifestFactory::makeManifest("singleRun", ["functionalSuite2" => []]);

        // Generate the Suite
        SuiteGenerator::getInstance()->generateAllSuites($singleRunManifest);
        $singleRunManifest->generate();

        // Validate log message and add group name for later deletion
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            "suite generated",
            ['suite' => $groupName, 'relative_path' => "_generated" . DIRECTORY_SEPARATOR . $groupName]
        );
        self::$TEST_GROUPS[] = $groupName;

        // Validate Yaml file updated
        $yml = Yaml::parse(file_get_contents(self::CONFIG_YML_FILE));
        $this->assertArrayHasKey($groupName, $yml['groups']);

        $suiteResultBaseDir = self::GENERATE_RESULT_DIR .
            DIRECTORY_SEPARATOR .
            $groupName .
            DIRECTORY_SEPARATOR;

        // Validate tests have been generated
        $dirContents = array_diff(scandir($suiteResultBaseDir), ['..', '.']);

        foreach ($expectedContents as $expectedFile) {
            $this->assertTrue(in_array($expectedFile, $dirContents));
        }

        $expectedManifest = "verification"
            . DIRECTORY_SEPARATOR
            . "_generated"
            . DIRECTORY_SEPARATOR
            . "default"
            . DIRECTORY_SEPARATOR
            . PHP_EOL
            . "-g functionalSuite2"
            . PHP_EOL;

        $this->assertEquals($expectedManifest, file_get_contents(self::getManifestFilePath()));
    }

    /**
     * Test extends tests generation in a suite
     */
    public function testSuiteGenerationWithExtends()
    {
        $groupName = 'suiteExtends';

        $expectedFileNames = SuiteTestReferences::$data[$groupName];

        // Generate the Suite
        SuiteGenerator::getInstance()->generateSuite($groupName);

        // Validate log message and add group name for later deletion
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            "suite generated",
            ['suite' => $groupName, 'relative_path' => "_generated" . DIRECTORY_SEPARATOR . $groupName]
        );
        self::$TEST_GROUPS[] = $groupName;

        // Validate Yaml file updated
        $yml = Yaml::parse(file_get_contents(self::CONFIG_YML_FILE));
        $this->assertArrayHasKey($groupName, $yml['groups']);

        $suiteResultBaseDir = self::GENERATE_RESULT_DIR .
            $groupName .
            DIRECTORY_SEPARATOR;

        // Validate tests have been generated
        $dirContents = array_diff(scandir($suiteResultBaseDir), ['..', '.']);

        foreach ($expectedFileNames as $expectedFileName) {
            $this->assertTrue(in_array($expectedFileName, $dirContents));
            $this->assertFileEquals(
                self::RESOURCES_PATH . DIRECTORY_SEPARATOR
                    . substr($expectedFileName, 0, strlen($expectedFileName)-4)
                    . ".txt",
                $suiteResultBaseDir . $expectedFileName
            );
        }
    }

    /**
     * Test comments generated during suite generation
     */
    public function testSuiteCommentsGeneration()
    {
        $groupName = 'functionalSuiteWithComments';

        $expectedContents = SuiteTestReferences::$data[$groupName];

        // Generate the Suite
        SuiteGenerator::getInstance()->generateSuite($groupName);

        // Validate log message and add group name for later deletion
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            "suite generated",
            ['suite' => $groupName, 'relative_path' => "_generated" . DIRECTORY_SEPARATOR . $groupName]
        );
        self::$TEST_GROUPS[] = $groupName;

        // Validate Yaml file updated
        $yml = Yaml::parse(file_get_contents(self::CONFIG_YML_FILE));
        $this->assertArrayHasKey($groupName, $yml['groups']);

        $suiteResultBaseDir = self::GENERATE_RESULT_DIR .
            DIRECTORY_SEPARATOR .
            $groupName .
            DIRECTORY_SEPARATOR;

        // Validate tests have been generated
        $dirContents = array_diff(scandir($suiteResultBaseDir), ['..', '.']);

        foreach ($expectedContents as $expectedFile) {
            $this->assertTrue(in_array($expectedFile, $dirContents));
        }

        //assert group file created and contains correct contents
        $groupFile = PROJECT_ROOT .
            DIRECTORY_SEPARATOR .
            "src" .
            DIRECTORY_SEPARATOR .
            "Magento" .
            DIRECTORY_SEPARATOR .
            "FunctionalTestingFramework" .
            DIRECTORY_SEPARATOR .
            "Group" .
            DIRECTORY_SEPARATOR .
            $groupName .
            ".php";

        $this->assertTrue(file_exists($groupFile));
        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . $groupName . ".txt",
            $groupFile
        );
    }

    /**
     * Test suite generation with actions from different modules
     */
    public function testSuiteGenerationActionsInDifferentModules()
    {
        $groupName = 'ActionsInDifferentModulesSuite';

        $expectedContents = SuiteTestReferences::$data[$groupName];

        // Generate the Suite
        SuiteGenerator::getInstance()->generateSuite($groupName);

        // Validate log message and add group name for later deletion
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            "suite generated",
            ['suite' => $groupName, 'relative_path' => "_generated" . DIRECTORY_SEPARATOR . $groupName]
        );
        self::$TEST_GROUPS[] = $groupName;

        // Validate Yaml file updated
        $yml = Yaml::parse(file_get_contents(self::CONFIG_YML_FILE));
        $this->assertArrayHasKey($groupName, $yml['groups']);

        $suiteResultBaseDir = self::GENERATE_RESULT_DIR .
            DIRECTORY_SEPARATOR .
            $groupName .
            DIRECTORY_SEPARATOR;

        // Validate tests have been generated
        $dirContents = array_diff(scandir($suiteResultBaseDir), ['..', '.']);

        foreach ($expectedContents as $expectedFile) {
            $this->assertTrue(in_array($expectedFile, $dirContents));
        }

        //assert group file created and contains correct contents
        $groupFile = PROJECT_ROOT .
            DIRECTORY_SEPARATOR .
            "src" .
            DIRECTORY_SEPARATOR .
            "Magento" .
            DIRECTORY_SEPARATOR .
            "FunctionalTestingFramework" .
            DIRECTORY_SEPARATOR .
            "Group" .
            DIRECTORY_SEPARATOR .
            $groupName .
            ".php";

        $this->assertTrue(file_exists($groupFile));
        $this->assertFileEquals(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . $groupName . ".txt",
            $groupFile
        );
    }

    /**
     * revert any changes made to config.yml
     * remove _generated directory
     */
    public function tearDown(): void
    {
        DirSetupUtil::rmdirRecursive(self::GENERATE_RESULT_DIR);

        // delete config yml file from test dir
        $fileSystem = new \Symfony\Component\Filesystem\Filesystem();
        $fileSystem->remove(
            self::CONFIG_YML_FILE
        );

        $property = new \ReflectionProperty(DirSetupUtil::class, "DIR_CONTEXT");
        $property->setAccessible(true);
        $property->setValue(null, []);
    }

    /**
     * Remove yml if created during tests and did not exist before
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }

    /**
     * Getter for manifest file path
     *
     * @return string
     * @throws TestFrameworkException
     */
    private static function getManifestFilePath()
    {
        return FilePathFormatter::format(TESTS_BP) .
            "verification" .
            DIRECTORY_SEPARATOR .
            "_generated" .
            DIRECTORY_SEPARATOR .
            'testManifest.txt';
    }
}
