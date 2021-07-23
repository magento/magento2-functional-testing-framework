<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Suite\Service;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;
use Symfony\Component\Yaml\Yaml;

/**
 * Class SuiteGeneratorService
 */
class SuiteGeneratorService
{
    const YAML_CODECEPTION_DIST_FILENAME = 'codeception.dist.yml';
    const YAML_CODECEPTION_CONFIG_FILENAME = 'codeception.yml';
    const YAML_GROUPS_TAG = 'groups';
    const YAML_EXTENSIONS_TAG = 'extensions';
    const YAML_ENABLED_TAG = 'enabled';
    const YAML_COPYRIGHT_TEXT =
        "# Copyright © Magento, Inc. All rights reserved.\n# See COPYING.txt for license details.\n";


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
    public function clearPreviousSessionConfigEntries()
    {
        $ymlArray = self::getYamlFileContents();
        $newYmlArray = $ymlArray;
        // if the yaml entries haven't already been cleared
        if (array_key_exists(self::YAML_EXTENSIONS_TAG, $ymlArray)) {
            foreach ($ymlArray[self::YAML_EXTENSIONS_TAG][self::YAML_ENABLED_TAG] as $key => $entry) {
                if (preg_match('/(Group\\\\.*)/', $entry)) {
                    unset($newYmlArray[self::YAML_EXTENSIONS_TAG][self::YAML_ENABLED_TAG][$key]);
                }
            }

            // needed for proper yml file generation based on indices
            $newYmlArray[self::YAML_EXTENSIONS_TAG][self::YAML_ENABLED_TAG] =
                array_values($newYmlArray[self::YAML_EXTENSIONS_TAG][self::YAML_ENABLED_TAG]);
        }

        if (array_key_exists(self::YAML_GROUPS_TAG, $newYmlArray)) {
            unset($newYmlArray[self::YAML_GROUPS_TAG]);
        }

        $ymlText = self::YAML_COPYRIGHT_TEXT . Yaml::dump($newYmlArray, 10);
        file_put_contents(self::getYamlConfigFilePath() . self::YAML_CODECEPTION_CONFIG_FILENAME, $ymlText);
    }


    /**
     * Function which accepts a suite name and suite path and appends a new group entry to the codeception.yml.dist
     * file in order to register the set of tests as a new group. Also appends group object location if required
     * by suite.
     *
     * @param string $suiteName
     * @param string $suitePath
     * @param string $groupNamespace
     *
     * @return void
     * @throws TestFrameworkException
     */
    public function appendEntriesToConfig(string $suiteName, string $suitePath, string $groupNamespace)
    {
        $relativeSuitePath = substr($suitePath, strlen(TESTS_BP));
        $relativeSuitePath = ltrim($relativeSuitePath, DIRECTORY_SEPARATOR);
        $ymlArray = self::getYamlFileContents();

        if (!array_key_exists(self::YAML_GROUPS_TAG, $ymlArray)) {
            $ymlArray[self::YAML_GROUPS_TAG]= [];
        }

        if ($groupNamespace) {
            $ymlArray[self::YAML_EXTENSIONS_TAG][self::YAML_ENABLED_TAG][] = $groupNamespace;
        }

        $ymlArray[self::YAML_GROUPS_TAG][$suiteName] = [$relativeSuitePath];
        $ymlText = self::YAML_COPYRIGHT_TEXT . Yaml::dump($ymlArray, 10);
        file_put_contents(self::getYamlConfigFilePath() . self::YAML_CODECEPTION_CONFIG_FILENAME, $ymlText);
    }

    /**
     * Function to return contents of codeception.yml file for config changes.
     *
     * @return array
     * @throws TestFrameworkException
     */
    private static function getYamlFileContents(): array
    {
        $configYmlFile = self::getYamlConfigFilePath() . self::YAML_CODECEPTION_CONFIG_FILENAME;
        $defaultConfigYmlFile = self::getYamlConfigFilePath() . self::YAML_CODECEPTION_DIST_FILENAME;
        $ymlContents = null;

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
    private static function getYamlConfigFilePath()
    {
        return FilePathFormatter::format(TESTS_BP);
    }
}