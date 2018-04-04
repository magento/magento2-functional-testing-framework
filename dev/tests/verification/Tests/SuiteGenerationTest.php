<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\verification\Tests;

use Magento\Framework\Module\Dir;
use Magento\FunctionalTestingFramework\Suite\SuiteGenerator;
use Magento\FunctionalTestingFramework\Util\TestManifest;
use Symfony\Component\Yaml\Yaml;
use tests\util\MftfTestCase;

class SuiteGenerationTest extends MftfTestCase
{
    const RESOURCES_DIR = TESTS_BP . DIRECTORY_SEPARATOR . 'verification' . DIRECTORY_SEPARATOR . 'Resources';
    const CONFIG_YML_FILE = FW_BP . DIRECTORY_SEPARATOR . SuiteGenerator::YAML_CODECEPTION_CONFIG_FILENAME;

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

        $suiteResultBaseDir = TESTS_BP .
            DIRECTORY_SEPARATOR .
            "verification" .
            DIRECTORY_SEPARATOR .
            "_generated" .
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
     * revert any changes made to config.yml
     */
    public static function tearDownAfterClass()
    {
        // restore config if we see there was an original codeception.yml file
        if (self::$YML_EXISTS_FLAG) {
            $yml = Yaml::parse(file_get_contents(self::CONFIG_YML_FILE));
            foreach (self::$TEST_GROUPS as $testGroup) {
                unset($yml['group'][$testGroup]);
            }

            file_put_contents(self::CONFIG_YML_FILE, Yaml::dump($yml, 10));
            return;
        }

        unlink(self::CONFIG_YML_FILE);
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
