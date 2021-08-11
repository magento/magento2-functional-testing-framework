<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Suite\Service;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Suite\SuiteGenerator;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Symfony\Component\Yaml\Yaml;

/**
 * Class SuiteGeneratorService
 */
class SuiteGeneratorService
{
    /**
     * Singleton SuiteGeneratorService Instance.
     *
     * @var SuiteGeneratorService
     */
    private static $INSTANCE;

    /**
     * SuiteGeneratorService constructor.
     */
    private function __construct()
    {
    }

    /**
     * Get CestFileCreatorUtil instance.
     *
     * @return SuiteGeneratorService
     */
    public static function getInstance(): SuiteGeneratorService
    {
        if (!self::$INSTANCE) {
            self::$INSTANCE = new SuiteGeneratorService();
        }

        return self::$INSTANCE;
    }

    /**
     * Function which takes the current config.yml array and clears any previous configuration for suite group object
     * files.
     *
     * @return void
     * @throws TestFrameworkException
     */
    public function clearPreviousSessionConfigEntries(): void
    {
        $ymlArray = self::getYamlFileContents();
        $newYmlArray = $ymlArray;
        // if the yaml entries haven't already been cleared
        if (array_key_exists(SuiteGenerator::YAML_EXTENSIONS_TAG, $ymlArray)) {
            $ymlEntries = $ymlArray[SuiteGenerator::YAML_EXTENSIONS_TAG][SuiteGenerator::YAML_ENABLED_TAG];

            foreach ($ymlEntries as $key => $entry) {
                if (preg_match('/(Group\\\\.*)/', $entry)) {
                    unset($newYmlArray[SuiteGenerator::YAML_EXTENSIONS_TAG][SuiteGenerator::YAML_ENABLED_TAG][$key]);
                }
            }
            // needed for proper yml file generation based on indices
            $newYmlArray[SuiteGenerator::YAML_EXTENSIONS_TAG][SuiteGenerator::YAML_ENABLED_TAG] =
                array_values($newYmlArray[SuiteGenerator::YAML_EXTENSIONS_TAG][SuiteGenerator::YAML_ENABLED_TAG]);
        }

        if (array_key_exists(SuiteGenerator::YAML_GROUPS_TAG, $newYmlArray)) {
            unset($newYmlArray[SuiteGenerator::YAML_GROUPS_TAG]);
        }
        $ymlText = SuiteGenerator::YAML_COPYRIGHT_TEXT . Yaml::dump($newYmlArray, 10);
        file_put_contents(self::getYamlConfigFilePath() . SuiteGenerator::YAML_CODECEPTION_CONFIG_FILENAME, $ymlText);
    }

    /**
     * Function which accepts a suite name and suite path and appends a new group entry to the codeception.yml.dist
     * file in order to register the set of tests as a new group. Also appends group object location if required
     * by suite.
     *
     * @param string      $suiteName
     * @param string      $suitePath
     * @param string|null $groupNamespace
     *
     * @return void
     * @throws TestFrameworkException
     */
    public function appendEntriesToConfig(string $suiteName, string $suitePath, ?string $groupNamespace): void
    {
        $relativeSuitePath = substr($suitePath, strlen(TESTS_BP));
        $relativeSuitePath = ltrim($relativeSuitePath, DIRECTORY_SEPARATOR);
        $ymlArray = self::getYamlFileContents();

        if (!array_key_exists(SuiteGenerator::YAML_GROUPS_TAG, $ymlArray)) {
            $ymlArray[SuiteGenerator::YAML_GROUPS_TAG] = [];
        }

        if ($groupNamespace) {
            $ymlArray[SuiteGenerator::YAML_EXTENSIONS_TAG][SuiteGenerator::YAML_ENABLED_TAG][] = $groupNamespace;
        }

        $ymlArray[SuiteGenerator::YAML_GROUPS_TAG][$suiteName] = [$relativeSuitePath];
        $ymlText = SuiteGenerator::YAML_COPYRIGHT_TEXT . Yaml::dump($ymlArray, 10);
        file_put_contents(self::getYamlConfigFilePath() . SuiteGenerator::YAML_CODECEPTION_CONFIG_FILENAME, $ymlText);
    }

    /**
     * Function which takes a string which is the desired output directory (under _generated) and an array of tests
     * relevant to the suite to be generated. The function takes this information and creates a new instance of the
     * test generator which is then called to create all the test files for the suite.
     *
     * @param string $path
     * @param array  $tests
     *
     * @return void
     * @throws TestReferenceException
     */
    public function generateRelevantGroupTests(string $path, array $tests): void
    {
        $testGenerator = TestGenerator::getInstance($path, $tests);
        $testGenerator->createAllTestFiles(null, []);
    }

    /**
     * Function to return contents of codeception.yml file for config changes.
     *
     * @return array
     * @throws TestFrameworkException
     */
    private static function getYamlFileContents(): array
    {
        $configYmlFile = self::getYamlConfigFilePath() . SuiteGenerator::YAML_CODECEPTION_CONFIG_FILENAME;
        $defaultConfigYmlFile = self::getYamlConfigFilePath() . SuiteGenerator::YAML_CODECEPTION_DIST_FILENAME;

        if (file_exists($configYmlFile)) {
            $ymlContents = file_get_contents($configYmlFile);
        } else {
            $ymlContents = file_get_contents($defaultConfigYmlFile);
        }

        return Yaml::parse($ymlContents) ?? [];
    }

    /**
     * Static getter for the Config yml filepath (as path cannot be stored in a const).
     *
     * @return string
     * @throws TestFrameworkException
     */
    private static function getYamlConfigFilePath(): string
    {
        return FilePathFormatter::format(TESTS_BP);
    }
}
