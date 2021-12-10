<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\StaticCheck;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\Script\ScriptUtil;
use Symfony\Component\Console\Input\InputInterface;
use Exception;

/**
 * Class AnnotationsCheck
 * @package Magento\FunctionalTestingFramework\StaticCheck
 */
class AnnotationsCheck implements StaticCheckInterface
{
    const ERROR_LOG_FILENAME = 'mftf-annotations-static-check';
    const ERROR_LOG_MESSAGE = 'MFTF Annotations Static Check';

    /**
     * Array containing all errors found after running the execute() function.
     * @var array
     */
    private $errors = [];

    /**
     * String representing the output summary found after running the execute() function.
     * @var string
     */
    private $output;

    /**
     * Array containing
     *   key = Story appended to Title
     *   value = test names that have that pair
     * @var array
     */
    private $storiesTitlePairs = [];

    /**
     * Array containing
     *   key = testCaseId appended to Title
     *   value = test names that have that pair
     * @var array
     */
    private $testCaseIdTitlePairs = [];

    /**
     * Validates test annotations
     *
     * @param InputInterface $input
     * @return void
     * @throws Exception
     */
    public function execute(InputInterface $input)
    {
        // Set MFTF to the UNIT_TEST_PHASE to mute the default DEPRECATION warnings from the TestObjectHandler.
        MftfApplicationConfig::create(
            true,
            MftfApplicationConfig::UNIT_TEST_PHASE,
            false,
            MftfApplicationConfig::LEVEL_DEFAULT,
            true
        );
        $allTests = TestObjectHandler::getInstance(false)->getAllObjects();

        foreach ($allTests as $test) {
            if ($this->validateSkipIssueId($test)) {
                //if test is skipped ignore other checks
                continue;
            }
            $this->validateRequiredAnnotations($test);
            $this->aggregateStoriesTitlePairs($test);
            $this->aggregateTestCaseIdTitlePairs($test);
        }

        $this->validateStoriesTitlePairs();
        $this->validateTestCaseIdTitlePairs();

        $scriptUtil = new ScriptUtil();
        $this->output = $scriptUtil->printErrorsToFile(
            $this->errors,
            StaticChecksList::getErrorFilesPath() . DIRECTORY_SEPARATOR . self::ERROR_LOG_FILENAME . '.txt',
            self::ERROR_LOG_MESSAGE
        );
    }

    /**
     * Return array containing all errors found after running the execute() function.
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Return string of a short human readable result of the check. For example: "No Dependency errors found."
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Validates that the test has the following annotations:
     *   stories
     *   title
     *   description
     *   severity
     *
     * @param TestObject $test
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function validateRequiredAnnotations($test)
    {
        $annotations = $test->getAnnotations();
        $missing = [];

        $stories = $annotations['stories'] ?? null;
        if ($stories === null || !isset($stories[0]) || empty(trim($stories[0]))) {
            $missing[] = "stories";
        }

        $testCaseId = "[NO TESTCASEID]";
        if (isset($annotations['testCaseId'][0])) {
            $testCaseId = trim($annotations['testCaseId'][0]);
        }

        $title = $annotations['title'] ?? null;
        if ($title === null
            || !isset($title[0])
            || empty(trim($title[0]))
            || empty(trim(substr(trim($title[0]), strlen($testCaseId . ': '))))) {
            $missing[] = "title";
        }

        $description = $annotations['description']['main'] ?? null;
        if ($description === null || empty(trim($description))) {
            $missing[] = "description";
        }

        $severity = $annotations['severity'] ?? null;
        if ($severity === null || !isset($severity[0]) || empty(trim($severity[0]))) {
            $missing[] = "severity";
        }

        $allMissing = join(", ", $missing);
        if (strlen($allMissing) > 0) {
            $this->errors[][] = "Test {$test->getName()} is missing the required annotations: " . $allMissing;
        }
    }

    /**
     * Validates that if the test is skipped, that it has an issueId value.
     *
     * @param TestObject $test
     * @return boolean
     */
    private function validateSkipIssueId($test)
    {
        $validateSkipped = false;
        $annotations = $test->getAnnotations();

        $skip = $annotations['skip'] ?? null;
        if ($skip !== null) {
            $validateSkipped = true;
            if ((!isset($skip[0]) || strlen($skip[0]) === 0)
                && (!isset($skip['issueId']) || strlen($skip['issueId']) === 0)) {
                $this->errors[][] = "Test {$test->getName()} is skipped but the issueId is empty.";
            }
        }
        return $validateSkipped;
    }

    /**
     * Add the key = "stories appended to title", value = test name, to the class variable.
     *
     * @param TestObject $test
     * @return void
     */
    private function aggregateStoriesTitlePairs($test)
    {
        $annotations = $test->getAnnotations();
        $stories = $annotations['stories'][0] ?? null;
        $title = $this->getTestTitleWithoutPrefix($test);
        if ($stories !== null && $title !== null) {
            $this->storiesTitlePairs[$stories . $title][] = $test->getName();
        }
    }

    /**
     * Add the key = "testCaseId appended to title", value = test name, to the class variable.
     *
     * @param TestObject $test
     * @return void
     */
    private function aggregateTestCaseIdTitlePairs($test)
    {
        $annotations = $test->getAnnotations();
        $testCaseId = $annotations['testCaseId'][0] ?? null;
        $title = $this->getTestTitleWithoutPrefix($test);
        if ($testCaseId !== null && $title !== null) {
            $this->testCaseIdTitlePairs[$testCaseId . $title][] = $test->getName();
        }
    }

    /**
     * Strip away the testCaseId prefix that was automatically added to the test title
     * so that way we have just the raw title from the XML file.
     *
     * @param TestObject $test
     * @return string|null
     */
    private function getTestTitleWithoutPrefix($test)
    {
        $annotations = $test->getAnnotations();
        $title = $annotations['title'][0] ?? null;
        if ($title === null) {
            return null;
        } else {
            $testCaseId = $annotations['testCaseId'][0] ?? "[NO TESTCASEID]";
            return substr($title, strlen($testCaseId . ": "));
        }
    }

    /**
     * Adds an error if any story+title pairs are used by more than one test.
     *
     * @return void
     */
    private function validateStoriesTitlePairs()
    {
        foreach ($this->storiesTitlePairs as $pair) {
            if (sizeof($pair) > 1) {
                $this->errors[][] = "Stories + title combination must be unique: " . join(", ", $pair);
            }
        }
    }

    /**
     * Adds an error if any testCaseId+title pairs are used by more than one test.
     *
     * @return void
     */
    private function validateTestCaseIdTitlePairs()
    {
        foreach ($this->testCaseIdTitlePairs as $pair) {
            if (sizeof($pair) > 1) {
                $this->errors[][] = "testCaseId + title combination must be unique: " . join(", ", $pair);
            }
        }
    }
}
