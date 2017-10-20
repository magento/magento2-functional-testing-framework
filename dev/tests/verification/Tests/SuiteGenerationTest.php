<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Suite\SuiteGenerator;
use Magento\FunctionalTestingFramework\Util\TestManifest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use tests\verification\Util\FileDiffUtil;

class SuiteGenerationTest extends TestCase
{
    const RESOURCES_DIR = TESTS_BP . DIRECTORY_SEPARATOR . 'verification' . DIRECTORY_SEPARATOR . 'Resources';
    const CONFIG_YML_FILE = FW_BP . DIRECTORY_SEPARATOR . SuiteGenerator::YAML_CODECEPTION_CONFIG_FILENAME;

    private static $YML_EXISTS_FLAG = false;
    private static $TEST_GROUPS = [];


    public static function setUpBeforeClass()
    {
        if (file_exists(self::CONFIG_YML_FILE)) {
            self::$YML_EXISTS_FLAG = true;
            return;
        }

        $configYml = fopen(self::CONFIG_YML_FILE, "w");
        fclose($configYml);
    }

    public function testSuiteGeneration1()
    {
         $groupName = 'functionalSuite1';

        // Generate the Suite
        SuiteGenerator::getInstance()->generateSuite($groupName);

        // Validate console message and add group name for later deletion
        $this->expectOutputRegex('/Suite .* generated to .*/');
        self::$TEST_GROUPS[] = $groupName;

        // Validate Yaml file updated
        $yml = Yaml::parse(file_get_contents(self::CONFIG_YML_FILE));
        $this->assertArrayHasKey($groupName, $yml['groups']);

        // Validate test manifest contents
        $actualManifest = TESTS_BP .
            DIRECTORY_SEPARATOR .
            "verification" .
            DIRECTORY_SEPARATOR .
            "_generated" .
            DIRECTORY_SEPARATOR .
            $groupName .
            DIRECTORY_SEPARATOR .
            TestManifest::TEST_MANIFEST_FILENAME;
        $expectedManifest = self::RESOURCES_DIR .  DIRECTORY_SEPARATOR . __FUNCTION__ . ".txt";
        $fileDiffUtil = new FileDiffUtil($expectedManifest, $actualManifest);
        $this->assertNull($fileDiffUtil->diffContents());
    }

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
}
