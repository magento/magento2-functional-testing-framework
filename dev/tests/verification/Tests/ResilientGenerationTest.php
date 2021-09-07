<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Suite\SuiteGenerator;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;
use Magento\FunctionalTestingFramework\Util\Manifest\TestManifestFactory;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Symfony\Component\Yaml\Yaml;
use tests\unit\Util\TestLoggingUtil;
use tests\util\MftfTestCase;

class ResilientGenerationTest extends MftfTestCase
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
     * Exception group names and log messages
     *
     * @var string[][]
     */
    private static $exceptionGrpLogs = [
        'NotGenerateHookBeforeSuite' => [
            '/Suite NotGenerateHookBeforeSuite is not defined in xml or is invalid./'
        ],
        'NotGenerateHookAfterSuite' => [
            '/Suite NotGenerateHookAfterSuite is not defined in xml or is invalid./'
        ],
        'NotGenerateEmptySuite' => [
            '/Suite NotGenerateEmptySuite is not defined in xml or is invalid./'
        ],
    ];

    /**
     * Set up for testing
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

        $property = new \ReflectionProperty(SuiteGenerator::class, "instance");
        $property->setAccessible(true);
        $property->setValue(null);

        $property = new \ReflectionProperty(DirSetupUtil::class, "DIR_CONTEXT");
        $property->setAccessible(true);
        $property->setValue([]);

        $property = new \ReflectionProperty(SuiteObjectHandler::class, "instance");
        $property->setAccessible(true);
        $property->setValue(null);

        $property = new \ReflectionProperty(TestObjectHandler::class, "testObjectHandler");
        $property->setAccessible(true);
        $property->setValue(null);
    }

    /**
     * Test resilient generate all tests
     */
    public function testGenerateAllTests()
    {
        $testManifest = TestManifestFactory::makeManifest('default', []);
        TestGenerator::getInstance(null, [])->createAllTestFiles($testManifest);

        // Validate tests have been generated
        $dirContents = array_diff(
            scandir(self::GENERATE_RESULT_DIR . DIRECTORY_SEPARATOR . 'default'),
            ['..', '.']
        );

        foreach ($dirContents as $dirContent) {
            $this->assertStringStartsNotWith(
                'NotGenerate',
                $dirContent,
                "string {$dirContent} should not contains \"NotGenerate\""
            );
        }
    }

    /**
     * Test resilient generate all suites
     */
    public function testGenerateAllSuites()
    {
        $testManifest = TestManifestFactory::makeManifest('', []);
        SuiteGenerator::getInstance()->generateAllSuites($testManifest);

        foreach (SuiteTestReferences::$data as $groupName => $expectedContents) {
            if (substr($groupName, 0, 11) !== 'NotGenerate') {
                // Validate Yaml file updated
                $yml = Yaml::parse(file_get_contents(self::CONFIG_YML_FILE));
                $this->assertArrayHasKey($groupName, $yml['groups']);

                $suiteResultBaseDir = self::GENERATE_RESULT_DIR .
                    DIRECTORY_SEPARATOR .
                    $groupName .
                    DIRECTORY_SEPARATOR;

                // Validate suite and tests have been generated
                $dirContents = array_diff(scandir($suiteResultBaseDir), ['..', '.']);

                foreach ($expectedContents as $expectedFile) {
                    $this->assertTrue(in_array($expectedFile, $dirContents));
                }
            } else {
                $dirContents = array_diff(scandir(self::GENERATE_RESULT_DIR), ['..', '.']);

                // Validate suite is not generated
                $this->assertFalse(in_array($groupName, $dirContents));
            }
        }
    }

    /**
     * Test resilient generate some suites
     */
    public function testGenerateSomeSuites()
    {
        $suites = [
            'PartialGenerateForIncludeSuite',
            'PartialGenerateNoExcludeSuite',
            'NotGenerateHookBeforeSuite',
            'NotGenerateHookAfterSuite',
        ];

        foreach ($suites as $groupName) {
            $expectedContents = SuiteTestReferences::$data[$groupName];
            if (!in_array($groupName, array_keys(self::$exceptionGrpLogs))) {
                SuiteGenerator::getInstance()->generateSuite($groupName);
            } else {
                // Validate exception is thrown
                $this->assertExceptionRegex(
                    \Exception::class,
                    self::$exceptionGrpLogs[$groupName],
                    function () use ($groupName) {
                        SuiteGenerator::getInstance()->generateSuite($groupName);
                    }
                );
            }

            if (substr($groupName, 0, 11) !== 'NotGenerate') {
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
            } else {
                $dirContents = array_diff(scandir(self::GENERATE_RESULT_DIR), ['..', '.']);

                // Validate suite is not generated
                $this->assertFalse(in_array($groupName, $dirContents));
            }

            // Validate log message
            if (substr($groupName, 0, 11) !== 'NotGenerate'
                && !in_array($groupName, array_keys(self::$exceptionGrpLogs))) {
                $type = 'info';
                $message = '/suite generated/';
                $context = [
                    'suite' => $groupName,
                    'relative_path' => "_generated" . DIRECTORY_SEPARATOR . $groupName
                ];
            } else {
                $type = 'error';
                $message = self::$exceptionGrpLogs[$groupName][0];
                $context = [];
            }
            TestLoggingUtil::getInstance()->validateMockLogStatmentRegex($type, $message, $context);
        }
    }

    /**
     * Test generate an empty suite
     */
    public function testGenerateEmptySuites()
    {
        $groupName = 'NotGenerateEmptySuite';

        // Validate exception is thrown
        $this->assertExceptionRegex(
            \Exception::class,
            self::$exceptionGrpLogs[$groupName],
            function () use ($groupName) {
                SuiteGenerator::getInstance()->generateSuite($groupName);
            }
        );

        // Validate suite is not generated
        $dirContents = array_diff(scandir(self::GENERATE_RESULT_DIR), ['..', '.']);
        $this->assertFalse(in_array($groupName, $dirContents));

        // Validate log message
        $type = 'error';
        $message = self::$exceptionGrpLogs[$groupName][0];
        $context = [];

        TestLoggingUtil::getInstance()->validateMockLogStatmentRegex($type, $message, $context);
    }

    /**
     *
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

        $property = new \ReflectionProperty(SuiteGenerator::class, "instance");
        $property->setAccessible(true);
        $property->setValue(null);

        $property = new \ReflectionProperty(DirSetupUtil::class, "DIR_CONTEXT");
        $property->setAccessible(true);
        $property->setValue([]);

        $property = new \ReflectionProperty(SuiteObjectHandler::class, "instance");
        $property->setAccessible(true);
        $property->setValue(null);

        $property = new \ReflectionProperty(TestObjectHandler::class, "testObjectHandler");
        $property->setAccessible(true);
        $property->setValue(null);
    }

    /**
     * Remove yml if created during tests and did not exist before
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
