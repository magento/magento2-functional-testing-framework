<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension;

use Codeception\Events;
use Codeception\Step;
use Codeception\Test\Test;
use Magento\FunctionalTestingFramework\Allure\AllureHelper;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\PersistedObjectHandler;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Qameta\Allure\Allure;
use Qameta\Allure\AllureLifecycleInterface;
use Qameta\Allure\Model\StepResult;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Qameta\Allure\Model\TestResult;
use Qameta\Allure\Model\Status;
use Magento\FunctionalTestingFramework\Codeception\Subscriber\Console;

/**
 * Class TestContextExtension
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TestContextExtension extends BaseExtension
{
    private const STEP_PASSED = "passed";
    
    /**
     * Test files cache.
     *
     * @var array
     */
    private $testFiles = [];

    /**
     * Action group step key.
     *
     * @var null|string
     */
    private $actionGroupStepKey = null;

    /**
     * Boolean value to indicate if steps are invisible steps
     *
     * @var boolean
     */
    private $atInvisibleSteps = false;
    const TEST_PHASE_AFTER = "_after";
    const TEST_PHASE_BEFORE = "_before";

    const TEST_FAILED_FILE = 'failed';
    const TEST_HOOKS = [
        self::TEST_PHASE_AFTER => 'AfterHook',
        self::TEST_PHASE_BEFORE => 'BeforeHook'
    ];

    /**
     * Codeception Events Mapping to methods
     * @var array
     */
    public static $events;

    /**
     * The name of the currently running test
     * @var string
     */
    public $currentTest;

    /**
     * Initialize local vars
     *
     * @return void
     * @throws \Exception
     */
    public function _initialize(): void
    {
        $events = [
            Events::TEST_START => 'testStart',
            Events::STEP_AFTER => 'afterStep',
            Events::TEST_END => 'testEnd',
            Events::RESULT_PRINT_AFTER => 'saveFailed'
        ];
        self::$events = array_merge(parent::$events, $events);
        parent::_initialize();
    }

    /**
     * Codeception event listener function, triggered on test start.
     * @throws \Exception
     * @return void
     */
    public function testStart(\Codeception\Event\TestEvent $e)
    {
        if (getenv('ENABLE_CODE_COVERAGE') === 'true') {
            // Curl against test.php and pass in the test name. Used when gathering code coverage.
            $this->currentTest = $e->getTest()->getMetadata()->getName();
            $cURLConnection = curl_init();
            curl_setopt_array($cURLConnection, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => getenv('MAGENTO_BASE_URL') . "/test.php?test=" . $this->currentTest,
            ]);
            curl_exec($cURLConnection);
            if (version_compare(PHP_VERSION, '8.0') < 0) {
                // this function no longer has an effect in PHP 8.0, but it's required in earlier versions
                curl_close($cURLConnection);
            }
        }

        PersistedObjectHandler::getInstance()->clearHookObjects();
        PersistedObjectHandler::getInstance()->clearTestObjects();
    }

    /**
     * Codeception event listener function, triggered on test ending naturally or by errors/failures.
     * @param \Codeception\Event\TestEvent $e
     * @return void
     * @throws \Exception
     */
    public function testEnd(\Codeception\Event\TestEvent $e)
    {
        $cest = $e->getTest();

        //Access private TestResultObject to find stack and if there are any errors/failures
        $testResultObject = call_user_func(\Closure::bind(
            function () use ($cest) {
                return $cest->getResultAggregator();
            },
            $cest
        ));

        // check for errors in all test hooks and attach in allure
        if (!empty($testResultObject->errors())) {
            foreach ($testResultObject->errors() as $error) {
                if ($error->getTest()->getTestMethod() === $cest->getTestMethod()) {
                    $this->attachExceptionToAllure($error->getFail(), $cest->getTestMethod());
                }
            }
        }

        // check for failures in all test hooks and attach in allure
        if (!empty($testResultObject->failures())) {
            foreach ($testResultObject->failures() as $failure) {
                if ($failure->getTest()->getTestMethod() === $cest->getTestMethod()) {
                    $this->attachExceptionToAllure($failure->getFail(), $cest->getTestMethod());
                }
            }
        }
        // Reset Session and Cookies after all Test Runs, workaround due to functional.suite.yml restart: true
        $this->getDriver()->_runAfter($e->getTest());

        $lifecycle = Allure::getLifecycle();
        $lifecycle->updateTest(
            function (TestResult $testResult) {
                $this->getFormattedSteps($testResult);
            }
        );

        $this->addTestsInSuites($lifecycle, $cest);
    }

    /**
     * Function to add test under the suites.
     *
     * @param object $lifecycle
     * @param object $cest
     *
     * @return void
     */
    private function addTestsInSuites($lifecycle, $cest): void
    {
        $groupName = null;
        if ($this->options['groups'] !== null) {
            $group =  $this->options['groups'][0];
            $groupName = $this->sanitizeGroupName($group);
        }
        $lifecycle->updateTest(
            function (TestResult $testResult) use ($groupName, $cest) {
                $labels = $testResult->getLabels();
                foreach ($labels as $label) {
                    if ($groupName !== null && $label->getName() === "parentSuite") {
                        $label->setValue(sprintf('%s\%s', $label->getValue(), $groupName));
                    }
                    if ($label->getName() === "package") {
                        $className = $cest->getReportFields()['class'];
                        $className = preg_replace('{_[0-9]*_G}', '', $className);
                        $label->setValue($className);
                    }
                }
            }
        );
    }

    /**
     * Function which santizes any group names changed by the framework for execution in order to consolidate reporting.
     *
     * @param string $group
     * @return string
     */
    private function sanitizeGroupName($group): string
    {
        $suiteNames = array_keys(SuiteObjectHandler::getInstance()->getAllObjects());
        $exactMatch = in_array($group, $suiteNames);

        // if this is an existing suite name we dont' need to worry about changing it
        if ($exactMatch || strpos($group, "_") === false) {
            return $group;
        }

        // if we can't find this group in the generated suites we have to assume that the group was split for generation
        $groupNameSplit = explode("_", $group);
        array_pop($groupNameSplit);
        array_pop($groupNameSplit);
        $originalName = implode("_", $groupNameSplit);

        // confirm our original name is one of the existing suite names otherwise just return the original group name
        $originalName = in_array($originalName, $suiteNames) ? $originalName : $group;

        return $originalName;
    }

    /**
     * @param TestResult $testResult
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * Revisited to reduce cyclomatic complexity, left unrefactored for readability
     */
    private function getFormattedSteps(TestResult $testResult): void
    {
        $steps = $testResult->getSteps();
        $formattedSteps = [];
        $actionGroupKey = null;
        foreach ($steps as $key => $step) {
            if (str_contains($step->getName(), 'start before hook')
                || str_contains($step->getName(), 'end before hook')
                || str_contains($step->getName(), 'start after hook')
                || str_contains($step->getName(), 'end after hook')
             ) {
                 $step->setName(strtoupper($step->getName()));
            }
            // Remove all parameters from step because parameters already added in formatted step
            call_user_func(\Closure::bind(
                function () use ($step) {
                    $step->parameters = [];
                },
                null,
                $step
            ));
            if (strpos($step->getName(), ActionGroupObject::ACTION_GROUP_CONTEXT_START) !== false) {
                $step->setName(str_replace(ActionGroupObject::ACTION_GROUP_CONTEXT_START, '', $step->getName()));
                $actionGroupKey = $key;
                $formattedSteps[$actionGroupKey] = $step;
                continue;
            }
            if (stripos($step->getName(), ActionGroupObject::ACTION_GROUP_CONTEXT_END) !== false) {
                $actionGroupKey = null;
                continue;
            }
            if ($actionGroupKey !== null) {
                if ($step->getName() !== null) {
                    $formattedSteps[$actionGroupKey]->addSteps($step);
                    if ($step->getStatus()->jsonSerialize() !== self::STEP_PASSED) {
                        $formattedSteps[$actionGroupKey]->setStatus($step->getStatus());
                        $actionGroupKey = null;
                    }
                }
            } else {
                if ($step->getName() !== null) {
                    $formattedSteps[$key] = $step;
                }
            }
        }
        /** @var StepResult[] $formattedSteps*/
        $formattedSteps = array_values($formattedSteps);

        // No public function for setting the testResult steps
        call_user_func(\Closure::bind(
            function () use ($testResult, $formattedSteps) {
                $testResult->steps = $formattedSteps;
            },
            null,
            $testResult
        ));
    }

    /**
     * Extracts hook method from trace, looking specifically for the cest class given.
     * @param array  $trace
     * @param string $class
     * @return string
     */
    public function extractContext($trace, $class)
    {
        foreach ($trace as $entry) {
            $traceClass = $entry["class"] ?? null;
            if (strpos($traceClass, $class) !== 0) {
                return $entry["function"];
            }
        }
        return null;
    }

    /**
     * Attach stack trace of exceptions thrown in each test hook to allure.
     * @param  \Exception $exception
     * @param  string     $testMethod
     * @return mixed
     */
    public function attachExceptionToAllure($exception, $testMethod)
    {
        if (is_subclass_of($exception, \PHPUnit\Framework\Exception::class)) {
            $trace = $exception->getSerializableTrace();
        } else {
            $trace = $exception->getTrace();
        }

        $context = $this->extractContext($trace, $testMethod);

        if (isset(self::TEST_HOOKS[$context])) {
            $context = self::TEST_HOOKS[$context];
        } else {
            $context = 'TestMethod';
        }

        AllureHelper::addAttachmentToCurrentStep($exception, $context . 'Exception');

        $previousException = null;
        if ($exception instanceof \PHPUnit\Framework\ExceptionWrapper) {
            $previousException = $exception->getPreviousWrapped();
        } elseif ($exception instanceof \Throwable) {
            $previousException = $exception->getPrevious();
        }

        if ($previousException !== null) {
            $this->attachExceptionToAllure($previousException, $testMethod);
        }
    }

    /**
     * Codeception event listener function, triggered before step.
     * Check if it's a new page.
     *
     * @param \Codeception\Event\StepEvent $e
     * @return void
     * @throws \Exception
     */
    public function beforeStep(\Codeception\Event\StepEvent $e)
    {

        if ($this->pageChanged($e->getStep())) {
            $this->getDriver()->cleanJsError();
        }
    }

    /**
     * @param \Codeception\Event\StepEvent $e
     * @return string|void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * Revisited to reduce cyclomatic complexity, left unrefactored for readability
     */
    public function stepName(\Codeception\Event\StepEvent $e)
    {
        $stepAction = $e->getStep()->getAction();
        if (in_array($stepAction, ActionObject::INVISIBLE_STEP_ACTIONS)) {
            $this->atInvisibleSteps = true;
            return;
        }
        // Set back atInvisibleSteps flag
        if ($this->atInvisibleSteps && !in_array($stepAction, ActionObject::INVISIBLE_STEP_ACTIONS)) {
            $this->atInvisibleSteps = false;
        }

        //Hard set to 200; we don't expose this config in MFTF
        $argumentsLength = 200;
        $stepKey = null;

        if (!($e->getStep() instanceof Comment)) {
            $stepKey = $this->retrieveStepKeyForAllure($e->getStep(), $e->getTest()->getMetadata()->getFilename());
            $isActionGroup = (
                strpos(
                    $e->getStep()->__toString(),
                    ActionGroupObject::ACTION_GROUP_CONTEXT_START
                ) !== false
            );
            if ($isActionGroup) {
                preg_match(TestGenerator::ACTION_GROUP_STEP_KEY_REGEX, $e->getStep()->__toString(), $matches);
                if (!empty($matches['actionGroupStepKey'])) {
                    $this->actionGroupStepKey = ucfirst($matches['actionGroupStepKey']);
                }
            }
        }
        // DO NOT alter action if actionGroup is starting, need the exact actionGroup name for good logging
        if (strpos($stepAction, ActionGroupObject::ACTION_GROUP_CONTEXT_START) === false
            && !($e->getStep() instanceof Comment)
        ) {
            $stepAction = $e->getStep()->getHumanizedActionWithoutArguments();
        }
        $stepArgs = $e->getStep()->getArgumentsAsString($argumentsLength);
        if (!trim($stepAction)) {
            $stepAction = $e->getStep()->getMetaStep()->getHumanizedActionWithoutArguments();
            $stepArgs = $e->getStep()->getMetaStep()->getArgumentsAsString($argumentsLength);
        }
        $stepName = '';

        if (isset($stepName)) {
            $stepName .= '[' . $stepKey . '] ';
            if (empty($stepKey)) {
                $stepName = "";
            }
        }
        $stepName .= $stepAction . ' ' . $stepArgs;
        // Strip control characters so that report generation does not fail
        $stepName = preg_replace('/[[:cntrl:]]/', '', $stepName);
        if (stripos($stepName, "\mftf\helper")) {
            preg_match("/\[(.*?)\]/", $stepName, $matches);
            $stepKeyData = preg_split('/\s+/', ucwords($matches[1]));
            if (count($stepKeyData) > 0) {
                $this->actionGroupStepKey = (isset($this->actionGroupStepKey))
                    ?$this->actionGroupStepKey
                    : "";
                $stepKeyHelper = str_replace($this->actionGroupStepKey, '', lcfirst(implode("", $stepKeyData)));
                $stepName= '['.$stepKeyHelper.'] '.preg_replace('#\[.*\]#', '', $stepName);
            }
        }
        return ucfirst($stepName);
    }

    /**
     * Codeception event listener function, triggered after step.
     * Calls ErrorLogger to log JS errors encountered.
     * @param \Codeception\Event\StepEvent $e
     * @return void
     * @throws \Exception
     */
    public function afterStep(\Codeception\Event\StepEvent $e)
    {
        $lifecycle = Allure::getLifecycle();
        $stepName = $this->stepName($e);
        $lifecycle->updateStep(
            function (StepResult $step) use ($stepName) {
                $step->setName($stepName);
            }
        );
        $browserLog = [];
        try {
            $browserLog = $this->getDriver()->webDriver->manage()->getLog("browser");
        } catch (\Exception $exception) {
        }
        if (getenv('ENABLE_BROWSER_LOG') === 'true') {
            foreach (explode(',', getenv('BROWSER_LOG_BLOCKLIST')) as $source) {
                $browserLog = BrowserLogUtil::filterLogsOfType($browserLog, $source);
            }
            if (!empty($browserLog)) {
                AllureHelper::addAttachmentToCurrentStep(json_encode($browserLog, JSON_PRETTY_PRINT), "Browser Log");
            }
        }
        BrowserLogUtil::logErrors($browserLog, $this->getDriver(), $e);
    }

    /**
     * Saves failed tests from last codecept run command into a file in _output directory
     * Removes file if there were no failures in last run command
     * @param \Codeception\Event\PrintResultEvent $e
     * @return void
     */
    public function saveFailed(\Codeception\Event\PrintResultEvent $e)
    {
        $file = $this->getLogDir() . self::TEST_FAILED_FILE;
        $result = $e->getResult();
        $output = [];

        // Remove previous file regardless if we're writing a new file
        if (is_file($file)) {
            unlink($file);
        }

        foreach ($result->failures() as $fail) {
            $output[] = $this->localizePath(\Codeception\Test\Descriptor::getTestFullName($fail->getTest()));
        }
        foreach ($result->errors() as $fail) {
            $output[] = $this->localizePath(\Codeception\Test\Descriptor::getTestFullName($fail->getTest()));
        }
        foreach ($result->incomplete() as $fail) {
            $output[] = $this->localizePath(\Codeception\Test\Descriptor::getTestFullName($fail->getTest()));
        }

        if (empty($output)) {
            return;
        }

        file_put_contents($file, implode("\n", $output));
    }

    /**
     * Returns localized path to string, for writing failed file.
     * @param string $path
     * @return string
     */
    protected function localizePath($path)
    {
        $root = realpath($this->getRootDir()) . DIRECTORY_SEPARATOR;
        if (substr($path, 0, strlen($root)) === $root) {
            return substr($path, strlen($root));
        }
        return $path;
    }
    
    /**
     * Reading stepKey from file.
     *
     * @param Step   $step
     * @param string $filePath
     * @return string|null
     */
    private function retrieveStepKeyForAllure(Step $step, string $filePath)
    {
        $stepKey = null;
        $stepLine = $step->getLineNumber();
        $stepLine = $stepLine - 1;

        //If the step's filepath is different from the test, it's a comment action.
        if ($this->getRootDir() . $step->getFilePath() != $filePath) {
            return "";
        }

        if (!array_key_exists($filePath, $this->testFiles)) {
            $this->testFiles[$filePath] = explode(PHP_EOL, file_get_contents($filePath));
        }

        preg_match(TestGenerator::ACTION_STEP_KEY_REGEX, $this->testFiles[$filePath][$stepLine], $matches);
        if (!empty($matches['stepKey'])) {
            $stepKey = $matches['stepKey'];
        }
        if ($this->actionGroupStepKey !== null) {
            $stepKey = str_replace($this->actionGroupStepKey, '', $stepKey);
        }

        $stepKey = $stepKey === '[]' ? null : $stepKey;
        return $stepKey;
    }
}
