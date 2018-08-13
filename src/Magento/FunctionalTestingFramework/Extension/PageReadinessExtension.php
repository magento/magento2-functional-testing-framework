<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension;

use Codeception\Event\StepEvent;
use Codeception\Event\TestEvent;
use Codeception\Events;
use Codeception\Exception\ModuleRequireException;
use Codeception\Extension;
use Codeception\Module\WebDriver;
use Codeception\Step;
use Facebook\WebDriver\Exception\UnexpectedAlertOpenException;
use Magento\FunctionalTestingFramework\Extension\ReadinessMetrics\AbstractMetricCheck;
use Facebook\WebDriver\Exception\TimeOutException;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Monolog\Logger;

/**
 * Class PageReadinessExtension
 */
class PageReadinessExtension extends Extension
{
    /**
     * Codeception Events Mapping to methods
     *
     * @var array
     */
    public static $events = [
        Events::TEST_BEFORE => 'beforeTest',
        Events::STEP_BEFORE => 'beforeStep'
    ];

    /**
     * List of action types that should bypass metric checks
     * shouldSkipCheck() also checks for the 'Comment' step type, which doesn't follow the $step->getAction() pattern
     *
     * @var array
     */
    private $ignoredActions = [
        'saveScreenshot',
        'wait'
    ];

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Logger verbosity
     *
     * @var boolean
     */
    private $verbose;

    /**
     * Array of readiness metrics, initialized during beforeTest event
     *
     * @var AbstractMetricCheck[]
     */
    private $readinessMetrics;

    /**
     * The name of the active test
     *
     * @var string
     */
    private $testName;

    /**
     * The current URI of the active page
     *
     * @var string
     */
    private $uri;

    /**
     * Initialize local vars
     *
     * @return void
     * @throws \Exception
     */
    public function _initialize()
    {
        $this->logger = LoggingUtil::getInstance()->getLogger(get_class($this));
        $this->verbose = MftfApplicationConfig::getConfig()->verboseEnabled();
    }

    /**
     * WebDriver instance to use to execute readiness metric checks
     *
     * @return WebDriver
     * @throws ModuleRequireException
     */
    public function getDriver()
    {
        return $this->getModule($this->config['driver']);
    }

    /**
     * Initialize the readiness metrics for the test
     *
     * @param \Codeception\Event\TestEvent $e
     * @return void
     */
    public function beforeTest(TestEvent $e)
    {
        if (isset($this->config['resetFailureThreshold'])) {
            $failThreshold = intval($this->config['resetFailureThreshold']);
        } else {
            $failThreshold = 3;
        }

        $this->testName = $e->getTest()->getMetadata()->getName();
        $this->uri = null;

        $this->getDriver()->_setConfig(['skipReadiness' => false]);

        $metrics = [];
        foreach ($this->config['readinessMetrics'] as $metricClass) {
            $metrics[] = new $metricClass($this, $failThreshold);
        }

        $this->readinessMetrics = $metrics;
    }

    /**
     * Waits for busy page flags to disappear before executing a step
     *
     * @param StepEvent $e
     * @return void
     * @throws \Exception
     */
    public function beforeStep(StepEvent $e)
    {
        $step = $e->getStep();
        $manualSkip = $this->getDriver()->_getConfig()['skipReadiness'];
        if ($this->shouldSkipCheck($step, $manualSkip)) {
            return;
        }

        $this->checkForNewPage($step);

        // todo: Implement step parameter to override global timeout configuration
        if (isset($this->config['timeout'])) {
            $timeout = intval($this->config['timeout']);
        } else {
            $timeout = $this->getDriver()->_getConfig()['pageload_timeout'];
        }

        $metrics = $this->readinessMetrics;

        try {
            $this->getDriver()->webDriver->wait($timeout)->until(
                function () use ($metrics) {
                    $passing = true;

                    /** @var AbstractMetricCheck $metric */
                    foreach ($metrics as $metric) {
                        try {
                            if (!$metric->runCheck()) {
                                $passing = false;
                            }
                        } catch (UnexpectedAlertOpenException $exception) {
                        }
                    }
                    return $passing;
                }
            );
        } catch (TimeoutException $exception) {
        }

        /** @var AbstractMetricCheck $metric */
        foreach ($metrics as $metric) {
            $metric->finalizeForStep($step);
        }
    }

    /**
     * Check if the URI has changed and reset metric tracking if so
     *
     * @param Step $step
     * @return void
     */
    private function checkForNewPage($step)
    {
        try {
            $currentUri = $this->getDriver()->_getCurrentUri();

            if ($this->uri !== $currentUri) {
                $this->logDebug(
                    'Page URI changed; resetting readiness metric failure tracking',
                    [
                        'step' => $step->__toString(),
                        'newUri' => $currentUri
                    ]
                );

                /** @var AbstractMetricCheck $metric */
                foreach ($this->readinessMetrics as $metric) {
                    $metric->resetTracker();
                }

                $this->uri = $currentUri;
            }
        } catch (\Exception $e) {
            $this->logDebug('Could not retrieve current URI', ['step' => $step->__toString()]);
        }
    }

    /**
     * Gets the active page URI from the start of the most recent step
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Gets the name of the active test
     *
     * @return string
     */
    public function getTestName()
    {
        return $this->testName;
    }

    /**
     * Should the given step bypass the readiness checks
     * todo: Implement step parameter to bypass specific metrics (or all) instead of basing on action type
     *
     * @param  Step    $step
     * @param  boolean $manualSkip
     * @return boolean
     */
    private function shouldSkipCheck($step, $manualSkip)
    {
        if ($step instanceof Step\Comment || in_array($step->getAction(), $this->ignoredActions) || $manualSkip) {
            print("here");
            return true;
        }
        return false;
    }

    /**
     * If verbose, log the given message to logger->debug including test context information
     *
     * @param string $message
     * @param array  $context
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    private function logDebug($message, $context = [])
    {
        if ($this->verbose) {
            $logContext = [
                'test' => $this->testName,
                'uri' => $this->uri
            ];
            foreach ($this->readinessMetrics as $metric) {
                $logContext[$metric->getName()] = $metric->getStoredValue();
                $logContext[$metric->getName() . '.failCount'] = $metric->getFailureCount();
            }
            $context = array_merge($logContext, $context);
            //TODO REMOVE THIS LINE, UNCOMMENT LOGGER
            //$this->logger->info($message, $context);
        }
    }
}
