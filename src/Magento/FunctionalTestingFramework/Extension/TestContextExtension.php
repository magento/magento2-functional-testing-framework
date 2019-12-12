<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension;

use Codeception\Events;
use Magento\FunctionalTestingFramework\Allure\AllureHelper;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\PersistedObjectHandler;

/**
 * Class TestContextExtension
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TestContextExtension extends BaseExtension
{
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
     * Initialize local vars
     *
     * @return void
     * @throws \Exception
     */
    public function _initialize()
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
    public function testStart()
    {
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
                return $cest->getTestResultObject();
            },
            $cest
        ));

        // check for errors in all test hooks and attach in allure
        if (!empty($testResultObject->errors())) {
            foreach ($testResultObject->errors() as $error) {
                if ($error->failedTest()->getTestMethod() == $cest->getTestMethod()) {
                    $this->attachExceptionToAllure($error->thrownException(), $cest->getTestMethod());
                }
            }
        }

        // check for failures in all test hooks and attach in allure
        if (!empty($testResultObject->failures())) {
            foreach ($testResultObject->failures() as $failure) {
                if ($failure->failedTest()->getTestMethod() == $cest->getTestMethod()) {
                    $this->attachExceptionToAllure($failure->thrownException(), $cest->getTestMethod());
                }
            }
        }
        // Reset Session and Cookies after all Test Runs, workaround due to functional.suite.yml restart: true
        $this->getDriver()->_runAfter($e->getTest());
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
            if (strpos($traceClass, $class) != 0) {
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

        //pop suppressed exceptions and attach to allure
        $change = function () {
            if ($this instanceof \PHPUnit\Framework\ExceptionWrapper) {
                return $this->previous;
            } else {
                return $this->getPrevious();
            }
        };

        $previousException = $change->call($exception);

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
     * Codeception event listener function, triggered after step.
     * Calls ErrorLogger to log JS errors encountered.
     * @param \Codeception\Event\StepEvent $e
     * @return void
     * @throws \Exception
     */
    public function afterStep(\Codeception\Event\StepEvent $e)
    {
        $browserLog = $this->getDriver()->webDriver->manage()->getLog("browser");
        if (getenv('ENABLE_BROWSER_LOG') === 'true') {
            foreach (explode(',', getenv('BROWSER_LOG_BLACKLIST')) as $source) {
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
            $output[] = $this->localizePath(\Codeception\Test\Descriptor::getTestFullName($fail->failedTest()));
        }
        foreach ($result->errors() as $fail) {
            $output[] = $this->localizePath(\Codeception\Test\Descriptor::getTestFullName($fail->failedTest()));
        }
        foreach ($result->notImplemented() as $fail) {
            $output[] = $this->localizePath(\Codeception\Test\Descriptor::getTestFullName($fail->failedTest()));
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
        if (substr($path, 0, strlen($root)) == $root) {
            return substr($path, strlen($root));
        }
        return $path;
    }
}
