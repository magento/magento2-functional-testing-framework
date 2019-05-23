<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Allure\Adapter;

use Magento\FunctionalTestingFramework\Util\TestGenerator;

/**
 * Class MagentoAllureStepKeyReader
 *
 * Parse a mftf generated Codeception php file for actions and step keys
 *
 * @package Magento\FunctionalTestingFramework\Allure
 */

class MagentoAllureStepKeyReader
{
    const BEFORE_MARK = "before";
    const AFTER_MARK = "after";
    const FAILED_MARK = "failed";
    const TEST_MARK = "test";
    const METHOD_BEFORE = "public function _" . self::BEFORE_MARK;
    const METHOD_AFTER = "public function _" . self::AFTER_MARK;
    const METHOD_FAILED = "public function _" . self::FAILED_MARK;
    const METHOD_ENDING = "\t}";
    const FAILED_ACTION_NAME = "saveScreenshot";
    const FAILED_STEP_KEY = "saveScreenshot";
    const REGEX_STEP_KEY = "~(?<=" . TestGenerator::STEPKEY_IN_COMMENT . ").*~";
    const REGEX_ACTION_NAME = "~(?<=\\". TestGenerator::ACTOR . ")([^\\(]*)(?=\()~";

    /**
     * test filename
     *
     * @var string
     */
    private $filename;

    /**
     * test method name
     *
     * @var string
     */
    private $method;

    /**
     * array of lines in a file
     *
     * @var array
     */
    private $lines;

    /**
     * steps in failed
     *
     * @var array
     */
    private $failedSteps;

    /**
     * steps in _before
     *
     * @var array
     */
    private $beforeSteps;

    /**
     * steps in _after
     *
     * @var array
     */
    private $afterSteps;

    /**
     * steps in test
     *
     * @var array
     */
    private $testSteps;

    /**
     * count of steps in methods
     *
     * @var array
     */
    private $stepCount;

    /**
     * MagentoAllureStepKeyReader constructor
     *
     * @param string $filename
     * @param string $method
     */
    public function __construct($filename, $method)
    {
        $this->filename = $filename;
        $this->method = $method;
        $this->load();
    }

    /**
     * Load file contents
     *
     * @return void
     */
    private function load()
    {
        $this->lines = [];
        if (!file_exists($this->filename)) {
            return;
        }
        $lines = file($this->filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines !== false) {
            $this->lines = $lines;
        }
        $this->beforeSteps = $this->parseStepsForMethod(self::METHOD_BEFORE);
        $this->testSteps = $this->parseStepsForMethod("public function " . $this->method);
        $this->afterSteps = $this->parseStepsForMethod(self::METHOD_AFTER);
        $this->failedSteps[] = $this->parseStepsForFailed();
        $this->stepCount[self::BEFORE_MARK] = count($this->beforeSteps);
        $this->stepCount[self::AFTER_MARK] = count($this->afterSteps);
        $this->stepCount[self::TEST_MARK] = count($this->testSteps);
        $this->stepCount[self::FAILED_MARK] = count($this->failedSteps);
    }

    /**
     * Return test step actions and step keys based on number of passed steps
     *
     * @param integer $passedCount
     *
     * @return array
     */
    public function getSteps($passedCount)
    {
        $steps = [];
        $total = $this->stepCount[self::BEFORE_MARK]
            + $this->stepCount[self::TEST_MARK]
            + $this->stepCount[self::AFTER_MARK];
        if ($passedCount < 0 || $passedCount > $total) {
            return $steps;
        }

        if ($passedCount == $total) { /* test passed */
            $steps = $this->beforeSteps;
            $steps = array_merge($steps, $this->testSteps);
            $steps = array_merge($steps, $this->afterSteps);
        } elseif ($passedCount < $this->stepCount[self::BEFORE_MARK]) { /* failed in _before() */
            $steps = array_slice($this->beforeSteps, 0, $passedCount);
            $steps = array_merge($steps, $this->failedSteps);
            $steps = array_merge($steps, $this->afterSteps);
        } elseif ($passedCount < ($this->stepCount[self::BEFORE_MARK] + $this->stepCount[self::TEST_MARK])) {
            $steps = $this->beforeSteps; /* failed in test() */
            $steps = array_merge(
                $steps,
                array_slice($this->testSteps, 0, $passedCount - $this->stepCount[self::BEFORE_MARK])
            );
            $steps = array_merge($steps, $this->failedSteps);
            $steps = array_merge($steps, $this->afterSteps);
        } else { /* failed in _after() */
            $steps = $this->beforeSteps;
            $steps = array_merge($steps, $this->testSteps);
            $steps = array_merge(
                $steps,
                array_slice(
                    $this->afterSteps,
                    0,
                    $passedCount - $this->stepCount[self::BEFORE_MARK] - $this->stepCount[self::TEST_MARK]
                )
            );
            $steps = array_merge($steps, $this->failedSteps);
        }
        return $steps;
    }

    /**
     * Parse test steps for a method
     *
     * @param string $method
     *
     * @return array
     */
    private function parseStepsForMethod($method)
    {
        $process = false;
        $steps = [];
        foreach ($this->lines as $line) {
            if (!$process && strpos($line, $method) !== false) {
                $process = true;
            }
            if ($process && strpos($line, self::METHOD_ENDING) !== false) {
                $process = false;
            }
            if ($process && preg_match(self::REGEX_STEP_KEY, $line, $stepKeys)) {
                if (preg_match(self::REGEX_ACTION_NAME, $line, $actions)) {
                    $steps[] = [
                        'action' => $this->humanize($actions[0]),
                        'stepKey' => $stepKeys[0]
                    ];
                }
            }
        }
        return $steps;
    }

    /**
     * Parse test steps for failed method
     *
     * @return array
     */
    private function parseStepsForFailed()
    {
        return [
            'action' => $this->humanize(self::FAILED_ACTION_NAME),
            'stepKey' => self::FAILED_STEP_KEY
        ];
    }

    /**
     * Convert input string into human readable words in lower case
     *
     * @param string $inStr
     *
     * @return string
     */
    private function humanize($inStr)
    {
        $inStr = preg_replace('/([A-Z]+)([A-Z][a-z])/', '\\1 \\2', $inStr);
        $inStr = preg_replace('/([a-z\d])([A-Z])/', '\\1 \\2', $inStr);
        $inStr = preg_replace('~\bdont\b~', 'don\'t', $inStr);
        return strtolower($inStr);
    }
}
