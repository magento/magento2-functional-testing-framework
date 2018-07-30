<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension;

use \Codeception\Codecept;
use \Codeception\Events;
use Magento\FunctionalTestingFramework\Extension\ErrorLogger;

/**
 * Class TestContextExtension
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class TestContextExtension extends \Codeception\Extension
{
    const TEST_PHASE_AFTER = "_after";
    const CODECEPT_AFTER_VERSION = "2.3.9";

    /**
     * Codeception Events Mapping to methods
     * @var array
     */
    public static $events = [
        Events::TEST_FAIL => 'testFail',
        Events::STEP_AFTER => 'afterStep',
        Events::TEST_END => 'testError'
    ];

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
     * Codeception event listener function, triggered on test error.
     * @param \Codeception\Event\TestEvent $e
     * @return void
     */
    public function testError(\Codeception\Event\TestEvent $e)
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
            $stack = $errors[0]->thrownException()->getTrace();
            $context = $this->extractContext($stack, $cest->getTestMethod());
            // Do not attempt to run _after if failure was in the _after block
            // Try to run _after but catch exceptions to prevent them from overwriting original failure.
            if ($context != TestContextExtension::TEST_PHASE_AFTER) {
                $this->runAfterBlock($e, $cest);
            }
        }
    }

    /**
     * Runs cest's after block, if necessary.
     * @param Symfony\Component\EventDispatcher\Event $e
     * @param \Codeception\TestInterface              $cest
     * @return void
     */
    private function runAfterBlock($e, $cest)
    {
        try {
            $actorClass = $e->getTest()->getMetadata()->getCurrent('actor');
            $I = new $actorClass($cest->getScenario());
            if (version_compare(Codecept::VERSION,TestContextExtension::CODECEPT_AFTER_VERSION, "<=")) {
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
     * Codeception event listener function, triggered after step.
     * Calls ErrorLogger to log JS errors encountered.
     * @param \Codeception\Event\StepEvent $e
     * @return void
     */
    public function afterStep(\Codeception\Event\StepEvent $e)
    {
        // @codingStandardsIgnoreStart
        $webDriver = $this->getModule("\Magento\FunctionalTestingFramework\Module\MagentoWebDriver")->webDriver;
        // @codingStandardsIgnoreEnd
        ErrorLogger::getInstance()->logErrors($webDriver, $e);
    }
}
