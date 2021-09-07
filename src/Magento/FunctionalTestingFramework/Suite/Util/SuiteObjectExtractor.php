<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Suite\Util;

use Exception;
use Magento\FunctionalTestingFramework\Exceptions\FastFailException;
use Magento\FunctionalTestingFramework\Exceptions\GenerationErrorCollector;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Util\BaseObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\TestHookObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Util\Validation\NameValidationUtil;
use Magento\FunctionalTestingFramework\Util\ModulePathExtractor;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;

/**
 * Class SuiteObjectExtractor
 * @package Magento\FunctionalTestingFramework\Suite\Util
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SuiteObjectExtractor extends BaseObjectExtractor
{
    const SUITE_ROOT_TAG = 'suites';
    const SUITE_TAG_NAME = 'suite';
    const INCLUDE_TAG_NAME = 'include';
    const EXCLUDE_TAG_NAME = 'exclude';
    const MODULE_TAG_NAME = 'module';
    const TEST_TAG_NAME = 'test';
    const GROUP_TAG_NAME = 'group';

    /**
     * TestHookObjectExtractor initialized in constructor.
     *
     * @var TestHookObjectExtractor
     */
    private $testHookObjectExtractor;

    /**
     * SuiteObjectExtractor constructor
     */
    public function __construct()
    {
        $this->testHookObjectExtractor = new TestHookObjectExtractor();
    }

    /**
     * Takes an array of parsed xml and converts into an array of suite objects.
     *
     * @param array $parsedSuiteData
     * @return array
     * @throws FastFailException
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function parseSuiteDataIntoObjects($parsedSuiteData)
    {
        $suiteObjects = [];

        // make sure there are suites defined before trying to parse as objects.
        if (!array_key_exists(self::SUITE_ROOT_TAG, $parsedSuiteData)) {
            return $suiteObjects;
        }

        foreach ($parsedSuiteData[self::SUITE_ROOT_TAG] as $parsedSuite) {
            if (!is_array($parsedSuite)) {
                // skip non array items parsed from suite (suite objects will always be arrays)
                continue;
            }

            $this->validateSuiteName($parsedSuite);

            try {
                // extract include and exclude references
                $groupTestsToInclude = $parsedSuite[self::INCLUDE_TAG_NAME] ?? [];
                $groupTestsToExclude = $parsedSuite[self::EXCLUDE_TAG_NAME] ?? [];

                // resolve references as test objects
                // continue if failed in include
                $include = $this->extractTestObjectsFromSuiteRef($groupTestsToInclude);
                $includeTests = $include['objects'] ?? [];
                $stepError = $include['status'] ?? 0;
                $includeMessage = '';
                if ($stepError !== 0) {
                    $includeMessage = "ERROR: " . strval($stepError) . " test(s) not included for suite "
                        . $parsedSuite[self::NAME];
                }

                // it's ok if failed in exclude
                $exclude = $this->extractTestObjectsFromSuiteRef($groupTestsToExclude);
                $excludeTests = $exclude['objects'] ?? [];

                // parse any object hooks
                $suiteHooks = $this->parseObjectHooks($parsedSuite);

                // log error if suite is empty
                if ($this->isSuiteEmpty($suiteHooks, $includeTests, $excludeTests)) {
                    LoggingUtil::getInstance()->getLogger(self::class)->error(
                        "Unable to parse suite " . $parsedSuite[self::NAME] . ". Suite must not be empty."
                    );

                    GenerationErrorHandler::getInstance()->addError(
                        'suite',
                        $parsedSuite[self::NAME],
                        self::class . ': ' . 'Suite must not be empty.'
                    );

                    continue;
                };

                // add all test if include tests is completely empty
                if (empty($includeTests)) {
                    $includeTests = TestObjectHandler::getInstance()->getAllObjects();
                }

                if (!empty($includeMessage)) {
                    LoggingUtil::getInstance()->getLogger(self::class)->error($includeMessage);
                    if (MftfApplicationConfig::getConfig()->verboseEnabled()
                        && MftfApplicationConfig::getConfig()->getPhase() === MftfApplicationConfig::GENERATION_PHASE
                    ) {
                        print($includeMessage);
                    }

                    GenerationErrorHandler::getInstance()->addError(
                        'suite',
                        $parsedSuite[self::NAME],
                        self::class . ': ' . $includeMessage,
                        true
                    );
                }
            } catch (FastFailException $e) {
                throw $e;
            } catch (\Exception $e) {
                LoggingUtil::getInstance()->getLogger(self::class)->error(
                    "Unable to parse suite " . $parsedSuite[self::NAME] . "\n" . $e->getMessage()
                );
                if (MftfApplicationConfig::getConfig()->verboseEnabled()
                    && MftfApplicationConfig::getConfig()->getPhase() === MftfApplicationConfig::GENERATION_PHASE) {
                    print("ERROR: Unable to parse suite " . $parsedSuite[self::NAME] . "\n");
                }

                GenerationErrorHandler::getInstance()->addError(
                    'suite',
                    $parsedSuite[self::NAME],
                    self::class . ': Unable to parse suite ' . $e->getMessage()
                );

                continue;
            }

            // create the new suite object
            $suiteObjects[$parsedSuite[self::NAME]] = new SuiteObject(
                $parsedSuite[self::NAME],
                $includeTests,
                $excludeTests,
                $suiteHooks
            );
        }

        return $suiteObjects;
    }

    /**
     * Throws exception for suite names meeting the below conditions:
     * 1. the name used is using special char or the "default" reserved name
     * 2. collisions between suite name and existing group name
     *
     * @param array $parsedSuite
     * @return void
     * @throws FastFailException
     */
    private function validateSuiteName($parsedSuite)
    {
        //check if name used is using special char or the "default" reserved name
        NameValidationUtil::validateName($parsedSuite[self::NAME], 'Suite');
        if ($parsedSuite[self::NAME] === 'default') {
            throw new FastFailException("A Suite can not have the name \"default\"");
        }

        $suiteName = $parsedSuite[self::NAME];
        //check for collisions between suite and existing group names
        $testGroupConflicts = TestObjectHandler::getInstance()->getTestsByGroup($suiteName);
        if (!empty($testGroupConflicts)) {
            $testGroupConflictsFileNames = "";
            foreach ($testGroupConflicts as $test) {
                $testGroupConflictsFileNames .= $test->getFilename() . "\n";
            }
            $exceptionmessage = "\"Suite names and Group names can not have the same value. \t\n" .
                "Suite: \"{$suiteName}\" also exists as a group annotation in: \n{$testGroupConflictsFileNames}";
            throw new FastFailException($exceptionmessage);
        }
    }

    /**
     * Parse object hooks
     *
     * @param array $parsedSuite
     * @return array
     * @throws XmlException
     * @throws TestReferenceException
     */
    private function parseObjectHooks($parsedSuite)
    {
        $suiteHooks = [];

        if (array_key_exists(TestObjectExtractor::TEST_BEFORE_HOOK, $parsedSuite)) {
            $hookObject = $this->testHookObjectExtractor->extractHook(
                $parsedSuite[self::NAME],
                TestObjectExtractor::TEST_BEFORE_HOOK,
                $parsedSuite[TestObjectExtractor::TEST_BEFORE_HOOK]
            );
            // Validate hook actions
            $hookObject->getActions();
            $suiteHooks[TestObjectExtractor::TEST_BEFORE_HOOK] = $hookObject;
        }

        if (array_key_exists(TestObjectExtractor::TEST_AFTER_HOOK, $parsedSuite)) {
            $hookObject = $this->testHookObjectExtractor->extractHook(
                $parsedSuite[self::NAME],
                TestObjectExtractor::TEST_AFTER_HOOK,
                $parsedSuite[TestObjectExtractor::TEST_AFTER_HOOK]
            );
            // Validate hook actions
            $hookObject->getActions();
            $suiteHooks[TestObjectExtractor::TEST_AFTER_HOOK] = $hookObject;
        }

        if (count($suiteHooks) === 1) {
            throw new XmlException(sprintf(
                "Suites that contain hooks must contain both a 'before' and an 'after' hook. Suite: \"%s\"",
                $parsedSuite[self::NAME]
            ));
        }
        return $suiteHooks;
    }

    /**
     * Check if suite hooks are empty/not included and there are no included tests/groups/modules
     *
     * @param array $suiteHooks
     * @param array $includeTests
     * @param array $excludeTests
     * @return boolean
     */
    private function isSuiteEmpty($suiteHooks, $includeTests, $excludeTests)
    {
        $noHooks = count($suiteHooks) === 0 ||
            (
                empty($suiteHooks['before']->getActions()) &&
                empty($suiteHooks['after']->getActions())
            );

        if ($noHooks && empty($includeTests) && empty($excludeTests)) {
            return true;
        }
        return false;
    }

    /**
     * Wrapper method for resolving suite reference data, checks type of suite reference and calls corresponding
     * resolver for each suite reference.
     *
     * @param array $suiteReferences
     * @return array
     * @throws FastFailException
     */
    private function extractTestObjectsFromSuiteRef($suiteReferences)
    {
        $testObjectList = [];
        $errCount = 0;
        foreach ($suiteReferences as $suiteRefName => $suiteRefData) {
            if (!is_array($suiteRefData)) {
                continue;
            }

            try {
                switch ($suiteRefData[self::NODE_NAME]) {
                    case self::TEST_TAG_NAME:
                        $testObject = TestObjectHandler::getInstance()->getObject($suiteRefData[self::NAME]);
                        $testObjectList[$testObject->getName()] = $testObject;
                        break;
                    case self::GROUP_TAG_NAME:
                        $testObjectList = $testObjectList +
                            TestObjectHandler::getInstance()->getTestsByGroup($suiteRefData[self::NAME]);
                        break;
                    case self::MODULE_TAG_NAME:
                        $testObjectList = array_merge(
                            $testObjectList,
                            $this->getTestsByModuleName($suiteRefData[self::NAME])
                        );
                        break;
                }
            } catch (FastFailException $e) {
                throw $e;
            } catch (\Exception $e) {
                $errCount++;
                LoggingUtil::getInstance()->getLogger(self::class)->error(
                    "Unable to find <"
                    . $suiteRefData[self::NODE_NAME]
                    . "> reference "
                    . $suiteRefData[self::NAME]
                    . " for suite "
                    . $suiteRefData[self::NAME]
                );
            }
        }

        return [
            'status' => $errCount,
            'objects' => $testObjectList
        ];
    }

    /**
     * Return all test objects for a module
     *
     * @param string $moduleName
     * @return TestObject[]
     * @throws Exception
     */
    private function getTestsByModuleName($moduleName)
    {
        $testObjects = [];
        $pathExtractor = new ModulePathExtractor();
        $allTestObjects = TestObjectHandler::getInstance()->getAllObjects();
        foreach ($allTestObjects as $testName => $testObject) {
            /** @var TestObject $testObject */
            $filename = $testObject->getFilename();
            if ($pathExtractor->extractModuleName($filename) === $moduleName) {
                $testObjects[$testName] = $testObject;
            }
        }
        return $testObjects;
    }
}
