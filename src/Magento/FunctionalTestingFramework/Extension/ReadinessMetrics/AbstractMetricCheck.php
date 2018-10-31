<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension\ReadinessMetrics;

use Codeception\Exception\ModuleRequireException;
use Codeception\Module\WebDriver;
use Codeception\Step;
use Facebook\WebDriver\Exception\UnexpectedAlertOpenException;
use Magento\FunctionalTestingFramework\Extension\PageReadinessExtension;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Monolog\Logger;

/**
 * Class AbstractMetricCheck
 */
abstract class AbstractMetricCheck
{
    /**
     * Extension being used to verify this metric passes before test metrics
     *
     * @var PageReadinessExtension
     */
    protected $extension;

    /**
     * Current state of the value the metric tracks
     *
     * @var mixed;
     */
    protected $currentValue;

    /**
     * Most recent saved state of the value the metric tracks
     * Updated when the metric passes or is finalized
     *
     * @var mixed;
     */
    protected $storedValue;

    /**
     * Current count of sequential identical failures
     *
     * @var integer;
     */
    protected $failCount;

    /**
     * Number of sequential identical failures before force-resetting the metric
     *
     * @var integer
     */
    protected $resetFailureThreshold;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var boolean
     */
    protected $verbose;

    /**
     * Constructor, called from the beforeTest event
     *
     * @param PageReadinessExtension $extension
     * @param integer                $resetFailureThreshold
     * @throws \Exception
     */
    public function __construct($extension, $resetFailureThreshold)
    {
        $this->extension = $extension;
        $this->logger = LoggingUtil::getInstance()->getLogger(get_class($this));
        $this->verbose = MftfApplicationConfig::getConfig()->verboseEnabled();

        // If the clearFailureOnPage() method is overridden, use the configured failure threshold
        // If not, the default clearFailureOnPage() method does nothing so don't worry about resetting failures
        $reflector = new \ReflectionMethod($this, 'clearFailureOnPage');
        if ($reflector->getDeclaringClass()->getName() === get_class($this)) {
            $this->resetFailureThreshold = $resetFailureThreshold;
        } else {
            $this->resetFailureThreshold = -1;
        }

        $this->resetTracker();
    }

    /**
     * Does the given value pass the readiness metric
     *
     * @param mixed $value
     * @return boolean
     */
    abstract protected function doesMetricPass($value);

    /**
     * Retrieve the active value for the metric to check from the page
     *
     * @return mixed
     * @throws UnexpectedAlertOpenException
     */
    abstract protected function fetchValueFromPage();

    /**
     * Override this method to reset the actual state of the page to make the metric pass
     * This method is called when too many identical failures were encountered in a row
     *
     * @return void
     */
    protected function clearFailureOnPage()
    {
        return;
    }

    /**
     * Get the base class name of the metric implementation
     *
     * @return string
     */
    public function getName()
    {
        $clazz = get_class($this);
        $namespaceBreak = strrpos($clazz, '\\');
        if ($namespaceBreak !== false) {
            $clazz = substr($clazz, $namespaceBreak + 1);
        }
        return $clazz;
    }

    /**
     * Fetches a new value for the metric and checks if it passes, clearing the failure tracking if so
     *
     * Even on a success, the readiness check will continue to be run until all metrics pass at the same time in order
     * to catch cases where a slow request of one metric can trigger calls for other metrics that were previously
     * thought ready
     *
     * @return boolean
     * @throws UnexpectedAlertOpenException
     */
    public function runCheck()
    {
        if ($this->doesMetricPass($this->getCurrentValue(true))) {
            $this->setTracker($this->getCurrentValue(), 0);
            return true;
        }

        return false;
    }

    /**
     * Update the state of the metric including tracked failure state and checking if a failing value is stuck and
     * needs to be reset so future checks can be accurate
     *
     * Called when the readiness check is finished (either all metrics pass or the check has timed out)
     *
     * @param Step $step
     * @return void
     */
    public function finalizeForStep($step)
    {
        try {
            $currentValue = $this->getCurrentValue();
        } catch (UnexpectedAlertOpenException $exception) {
            $this->debugLog(
                'An alert is open, bypassing javascript-based metric check',
                ['step' => $step->__toString()]
            );
            return;
        }

        if ($this->doesMetricPass($currentValue)) {
            $this->setTracker($currentValue, 0);
        } else {
            // If failure happened on the same value as before, increment the fail count, otherwise set at 1
            if (!isset($this->storedValue) || $currentValue !== $this->getStoredValue()) {
                $failCount = 1;
            } else {
                $failCount = $this->getFailureCount() + 1;
            }
            $this->setTracker($currentValue, $failCount);

            $this->errorLog('Failed readiness check', ['step' => $step->__toString()]);

            if ($this->resetFailureThreshold >= 0 && $failCount >= $this->resetFailureThreshold) {
                $this->debugLog(
                    'Too many failures, assuming metric is stuck and resetting state',
                    ['step' => $step->__toString()]
                );
                $this->resetMetric();
            }
        }
    }

    /**
     * Helper function to retrieve the driver being used to run the test
     *
     * @return WebDriver
     * @throws ModuleRequireException
     */
    protected function getDriver()
    {
        return $this->extension->getDriver();
    }

    /**
     * Helper function to execute javascript code, see WebDriver::executeJs for more information
     *
     * @param string $script
     * @param array  $arguments
     * @return mixed
     * @throws ModuleRequireException
     */
    protected function executeJs($script, $arguments = [])
    {
        return $this->extension->getDriver()->executeJS($script, $arguments);
    }

    /**
     * Gets the current state of the given variable
     * Fetches an updated value if not known or $refresh is true
     *
     * @param boolean $refresh
     * @return mixed
     * @throws UnexpectedAlertOpenException
     */
    private function getCurrentValue($refresh = false)
    {
        if ($refresh) {
            unset($this->currentValue);
        }
        if (!isset($this->currentValue)) {
            $this->currentValue = $this->fetchValueFromPage();
        }
        return $this->currentValue;
    }

    /**
     * Returns the value of the given variable for the previous check
     *
     * @return mixed
     */
    public function getStoredValue()
    {
        return $this->storedValue ?? null;
    }

    /**
     * The current count of sequential identical failures
     * Used to detect potentially stuck metrics
     *
     * @return integer
     */
    public function getFailureCount()
    {
        return $this->failCount;
    }

    /**
     * Update the state of the page to pass the metric and clear the saved failure state
     * Called when a failure is found to be stuck
     *
     * @return void
     */
    private function resetMetric()
    {
        $this->clearFailureOnPage();
        $this->resetTracker();
    }

    /**
     * Tracks the most recent value and the number of identical failures in a row
     *
     * @param mixed   $value
     * @param integer $failCount
     * @return void
     */
    public function setTracker($value, $failCount)
    {
        unset($this->currentValue);
        $this->storedValue = $value;
        $this->failCount = $failCount;
    }

    /**
     * Resets the tracked metric values on a new page or stuck failure
     *
     * @return void
     */
    public function resetTracker()
    {
        unset($this->currentValue);
        unset($this->storedValue);
        $this->failCount = 0;
    }

    /**
     * Log the given message to logger->error including context information
     *
     * @param string $message
     * @param array  $context
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function errorLog($message, $context = [])
    {
        $context = array_merge($this->getLogContext(), $context);
        //TODO REMOVE THIS LINE, UNCOMMENT LOGGER
        //$this->logger->error($message, $context);
    }

    /**
     * Log the given message to logger->info including context information
     *
     * @param string $message
     * @param array  $context
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function infoLog($message, $context = [])
    {
        $context = array_merge($this->getLogContext(), $context);
        //TODO REMOVE THIS LINE, UNCOMMENT LOGGER
        //$this->logger->info($message, $context);
    }

    /**
     * If verbose, log the given message to logger->debug including context information
     *
     * @param string $message
     * @param array  $context
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function debugLog($message, $context = [])
    {
        if ($this->verbose) {
            $context = array_merge($this->getLogContext(), $context);
            //TODO REMOVE THIS LINE, UNCOMMENT LOGGER
            //$this->logger->debug($message, $context);
        }
    }

    /**
     * Base context information to include in all log messages: test name, current URI, metric state
     * Reports most recent stored value, not current value, so call setTracker() first to update
     *
     * @return array
     */
    private function getLogContext()
    {
        return [
            'test' => $this->extension->getTestName(),
            'uri' => $this->extension->getUri(),
            $this->getName() => $this->getStoredValue(),
            $this->getName() . '.failCount' => $this->getFailureCount()
        ];
    }
}
