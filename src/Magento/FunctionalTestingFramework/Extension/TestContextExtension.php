<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension;

use Codeception\Events;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\PersistedObjectHandler;

/**
 * Class TestContextExtension
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class TestContextExtension extends BaseExtension
{
    const TEST_PHASE_AFTER = "_after";
    const CODECEPT_AFTER_VERSION = "2.3.9";
    const TEST_FAILED_FILE = 'failed';

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
            Events::TEST_FAIL => 'testFail',
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
     * Codeception event listener function, triggered on test failure.
     * @param \Codeception\Event\FailEvent $e
     * @return void
     */
    public function testFail(\Codeception\Event\FailEvent $e)
    {
        $cest = $e->getTest();
        $context = $this->extractContext($e->getFail()->getTrace(), $cest->getTestMethod());
        // Do not attempt to run _after if failure was in the _after block
        // Try to run _after but catch exceptions to prevent them from overwriting original failure.
        if ($context != TestContextExtension::TEST_PHASE_AFTER) {
            $this->runAfterBlock($e, $cest);
        }
    }

    /**
     * Codeception event listener function, triggered on test ending (naturally or by error).
     * @param \Codeception\Event\TestEvent $e
     * @return void
     * @throws \Exception
     */
    public function testEnd(\Codeception\Event\TestEvent $e)
    {
        $cest = $e->getTest();

        //Access private TestResultObject to find stack and if there are any errors (as opposed to failures)
        $testResultObject = call_user_func(\Closure::bind(
            function () use ($cest) {
                return $cest->getTestResultObject();
            },
            $cest
        ));
        $errors = $testResultObject->errors();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                if ($error->failedTest()->getTestMethod() == $cest->getName()) {
                    $stack = $errors[0]->thrownException()->getTrace();
                    $context = $this->extractContext($stack, $cest->getTestMethod());
                    // Do not attempt to run _after if failure was in the _after block
                    // Try to run _after but catch exceptions to prevent them from overwriting original failure.
                    if ($context != TestContextExtension::TEST_PHASE_AFTER) {
                        $this->runAfterBlock($e, $cest);
                    }
                    continue;
                }
            }
        }
        // Reset Session and Cookies after all Test Runs, workaround due to functional.suite.yml restart: true
        $this->getDriver()->_runAfter($e->getTest());
    }

    /**
     * Runs cest's after block, if necessary.
     * @param \Symfony\Component\EventDispatcher\Event $e
     * @param \Codeception\TestInterface               $cest
     * @return void
     */
    private function runAfterBlock($e, $cest)
    {
        try {
            $actorClass = $e->getTest()->getMetadata()->getCurrent('actor');
            $I = new $actorClass($cest->getScenario());
            if (version_compare(\Codeception\Codecept::VERSION, TestContextExtension::CODECEPT_AFTER_VERSION, "<=")) {
                call_user_func(\Closure::bind(
                    function () use ($cest, $I) {
                        $cest->executeHook($I, 'after');
                    },
                    null,
                    $cest
                ));
            }
        } catch (\Exception $e) {
            // Do not rethrow Exception
        }
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
        ErrorLogger::getInstance()->logErrors($this->getDriver(), $e);
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
