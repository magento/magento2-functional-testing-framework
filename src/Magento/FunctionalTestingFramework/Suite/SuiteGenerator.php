<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Suite;

use Magento\Framework\Phrase;
use Magento\Framework\Validator\Exception;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Symfony\Component\Yaml\Yaml;

class SuiteGenerator
{
    const YAML_CODECEPTION_DIST_FILENAME = 'codeception.dist.yml';
    const YAML_CODECEPTION_CONFIG_FILENAME = 'codeception.yml';
    const YAML_COPYRIGHT_TEXT =
        "# Copyright © Magento, Inc. All rights reserved.\n# See COPYING.txt for license details.\n";

    /**
     * Singelton Variable Instance.
     *
     * @var SuiteGenerator
     */
    private static $SUITE_GENERATOR_INSTANCE;

    /**
     * SuiteGenerator constructor.
     */
    private function __construct()
    {
        //private constructor for singelton
    }

    /**
     * Singleton method which is used to retrieve the instance of the suite generator.
     *
     * @return SuiteGenerator
     */
    public static function getInstance()
    {
        if (!self::$SUITE_GENERATOR_INSTANCE) {
            self::$SUITE_GENERATOR_INSTANCE = new SuiteGenerator();
        }

        return self::$SUITE_GENERATOR_INSTANCE;
    }

    /**
     * Function which takes a suite name and generates corresponding dir, cest files, group class, and updates
     * yml configuration for group run.
     *
     * @param string $suiteName
     * @return void
     */
    public function generateSuite($suiteName)
    {
        $suite = SuiteObjectHandler::getInstance()->getObject($suiteName);
        $relativePath = '_generated' . DIRECTORY_SEPARATOR . $suiteName;
        $fullPath = TESTS_MODULE_PATH . DIRECTORY_SEPARATOR . $relativePath;

        DirSetupUtil::createGroupDir($fullPath);
        $this->generateGroupFile($fullPath);
        $this->generateRelevantGroupTests($suiteName, $suite->getCests());
        $this->appendGroupEntryToConfig($suiteName, $fullPath);
        print "Suite ${suiteName} generated to ${relativePath}.\n";
    }

    /**
     * Method which will be needed for adding preconditions and creating a corresponding group file for codeception.
     *
     * @return void
     */
    private function generateGroupFile()
    {
        // generate group file here
    }

    /**
     * Function which accepts a suite name and suite path and appends a new group entry to the codeception.yml.dist
     * file in order to register the set of tests as a new group.
     *
     * @param string $suiteName
     * @param string $suitePath
     * @return void
     */
    private function appendGroupEntryToConfig($suiteName, $suitePath)
    {
        $configYmlPath = dirname(dirname(TESTS_BP)) . DIRECTORY_SEPARATOR;
        $configYmlFile = $configYmlPath . self::YAML_CODECEPTION_CONFIG_FILENAME;
        $defaultConfigYmlFile = $configYmlPath . self::YAML_CODECEPTION_DIST_FILENAME;
        $relativeSuitePath = substr($suitePath, strlen(dirname(dirname(TESTS_BP))) + 1);

        $ymlContents = null;
        if (file_exists($configYmlFile)) {
            $ymlContents = file_get_contents($configYmlFile);
        } else {
            $ymlContents = file_get_contents($defaultConfigYmlFile);
        }

        $ymlArray = Yaml::parse($ymlContents) ?? [];

        if (!array_key_exists('group', $ymlArray)) {
            $ymlArray['group']= [];
        }

        $ymlArray['group'][$suiteName] = $relativeSuitePath;
        $ymlText = self::YAML_COPYRIGHT_TEXT . Yaml::dump($ymlArray, 10);
        file_put_contents($configYmlFile, $ymlText);
    }

    /**
     * Function which takes a string which is the desired output directory (under _generated) and an array of cests
     * relevant to the suite to be generated. The function takes this information and creates a new instance of the test
     * generator which is then called to create all the cest files for the suite.
     *
     * @param string $path
     * @param array $cests
     * @return void
     */
    private function generateRelevantGroupTests($path, $cests)
    {
        $testGenerator = TestGenerator::getInstance($path, $cests);
        $testGenerator->createAllCestFiles();
    }
}
