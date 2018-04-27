<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\verification\Tests;

use Magento\Framework\Module\Dir;
use Magento\FunctionalTestingFramework\Suite\SuiteGenerator;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;
use Magento\FunctionalTestingFramework\Util\Manifest\ParallelTestManifest;
use Magento\FunctionalTestingFramework\Util\Manifest\TestManifestFactory;
use Symfony\Component\Yaml\Yaml;
use tests\util\MftfTestCase;

class SuiteGenerationTest extends MftfTestCase
{
    const RESOURCES_DIR = TESTS_BP . DIRECTORY_SEPARATOR . 'verification' . DIRECTORY_SEPARATOR . 'Resources';
    const CONFIG_YML_FILE = FW_BP . DIRECTORY_SEPARATOR . SuiteGenerator::YAML_CODECEPTION_CONFIG_FILENAME;
    const GENERATE_RESULT_DIR = TESTS_BP .
        DIRECTORY_SEPARATOR .
        "verification" .
        DIRECTORY_SEPARATOR .
        "_generated" .
        DIRECTORY_SEPARATOR;

    /**
     * Flag to track existence of config.yml file
     *
     * @var bool
     */
    private static $YML_EXISTS_FLAG = false;

    /**
     * Array which stores state of any existing config.yml groups
     *
     * @var array
     */
    private static $TEST_GROUPS = [];

    /**
     * Set up config.yml for testing
     */
    public static function setUpBeforeClass()
    {
        if (file_exists(self::CONFIG_YML_FILE)) {
            self::$YML_EXISTS_FLAG = true;
            return;
        }

        // destroy manifest file if it exists
        if (file_exists(self::getManifestFilePath())) {
            unlink(self::getManifestFilePath());
        }

        // destroy _generated if it exists
        if (file_exists(self::GENERATE_RESULT_DIR)) {
            DirSetupUtil::rmdirRecursive(self::GENERATE_RESULT_DIR);
        }

        $configYml = fopen(self::CONFIG_YML_FILE, "w");
        fclose($configYml);
    }

    /**
     * Test basic generation of a suite
     */
    public function testSuiteGeneration1()
    {
         $groupName = 'functionalSuite1';

        $expectedContents = [
           'additionalTestCest.php',
           'additionalIncludeTest2Cest.php',
           'IncludeTest2Cest.php',
           'IncludeTestCest.php'
        ];

        // Generate the Suite
        SuiteGenerator::getInstance()->generateSuite($groupName);

        // Validate console message and add group name for later deletion
        $this->expectOutputRegex('/Suite .* generated to .*/');
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
    public function testSuiteGenerationParallel()
    {
        $groupName = 'functionalSuite1';

        $expectedGroups = [
            'functionalSuite1_0',
            'functionalSuite1_1',
            'functionalSuite1_2',
            'functionalSuite1_3'
        ];

        $expectedContents = [
            'additionalTestCest.php',
            'additionalIncludeTest2Cest.php',
            'IncludeTest2Cest.php',
            'IncludeTestCest.php'
        ];

        //createParallelManifest
        /** @var ParallelTestManifest $parallelManifest */
        $parallelManifest = TestManifestFactory::makeManifest("parallel", ["functionalSuite1" => []]);

        // Generate the Suite
        $parallelManifest->createTestGroups(1);
        SuiteGenerator::getInstance()->generateAllSuites($parallelManifest);

        // Validate console message and add group name for later deletion
        $this->expectOutputRegex('/Suite .* generated to .*/');
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
     * Test hook groups generated during suite generation
     */
    public function testSuiteGenerationHooks()
    {
        $groupName = 'functionalSuiteHooks';

        $expectedContents = [
            'IncludeTestCest.php'
        ];

        // Generate the Suite
        SuiteGenerator::getInstance()->generateSuite($groupName);

        // Validate console message and add group name for later deletion
        $this->expectOutputRegex('/Suite .* generated to .*/');
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

        $expectedContents = [
            'additionalTestCest.php',
            'additionalIncludeTest2Cest.php',
            'IncludeTest2Cest.php',
            'IncludeTestCest.php'
        ];

        //createParallelManifest
        /** @var ParallelTestManifest $parallelManifest */
        $singleRunManifest = TestManifestFactory::makeManifest("singleRun", ["functionalSuite2" => []]);

        // Generate the Suite
        SuiteGenerator::getInstance()->generateAllSuites($singleRunManifest);
        $singleRunManifest->generate();

        // Validate console message and add group name for later deletion
        $this->expectOutputRegex('/Suite .* generated to .*/');
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

        $expectedManifest = "dev/tests/verification/_generated/default/" . PHP_EOL . "-g functionalSuite2" . PHP_EOL;

        $this->assertEquals($expectedManifest, file_get_contents(self::getManifestFilePath()));
    }

    /**
     * revert any changes made to config.yml
     * remove _generated directory
     */
    public function tearDown()
    {
        // restore config if we see there was an original codeception.yml file
        if (self::$YML_EXISTS_FLAG) {
            $yml = Yaml::parse(file_get_contents(self::CONFIG_YML_FILE));
            foreach (self::$TEST_GROUPS as $testGroup) {
                unset($yml['groups'][$testGroup]);
            }

            file_put_contents(self::CONFIG_YML_FILE, Yaml::dump($yml, 10));
        }
        DirSetupUtil::rmdirRecursive(self::GENERATE_RESULT_DIR);
    }

    /**
     * Remove yml if created during tests and did not exist before
     */
    public static function tearDownAfterClass()
    {
        if (!self::$YML_EXISTS_FLAG) {
            unlink(self::CONFIG_YML_FILE);
        }
    }

    /**
     * Getter for manifest file path
     *
     * @return string
     */
    private static function getManifestFilePath()
    {
        return TESTS_BP .
            DIRECTORY_SEPARATOR .
            "verification" .
            DIRECTORY_SEPARATOR .
            "_generated" .
            DIRECTORY_SEPARATOR .
            'testManifest.txt';
    }
}
