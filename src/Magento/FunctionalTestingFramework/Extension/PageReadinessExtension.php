<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension;

use Codeception\Event\StepEvent;
use Codeception\Event\TestEvent;
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
class PageReadinessExtension extends BaseExtension
{
    /**
     * List of action types that should bypass metric checks
     * shouldSkipCheck() also checks for the 'Comment' step type, which doesn't follow the $step->getAction() pattern
     *
     * @var array
     */
    private $ignoredActions = [
        'saveScreenshot',
        'skipReadinessCheck',
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
     * Initialize local vars
     *
     * @return void
     * @throws \Exception
     */
    public function _initialize()
    {
        $this->logger = LoggingUtil::getInstance()->getLogger(get_class($this));
        $this->verbose = MftfApplicationConfig::getConfig()->verboseEnabled();
        parent::_initialize();
    }

    /**
     * Initialize the readiness metrics for the test
     *
     * @param TestEvent $e
     * @return void
     * @throws \Exception
     */
    public function beforeTest(TestEvent $e)
    {
        parent::beforeTest($e);
        if (isset($this->config['resetFailureThreshold'])) {
            $failThreshold = intval($this->config['resetFailureThreshold']);
        } else {
            $failThreshold = 3;
        }

        $this->testName = $e->getTest()->getMetadata()->getName();

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
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function beforeStep(StepEvent $e)
    {
        $step = $e->getStep();
        $manualSkip = $this->getDriver()->_getConfig()['skipReadiness'];
        if ($this->shouldSkipCheck($step, $manualSkip)) {
            return;
        }

        // Check if page has changed and reset metric tracking if so
        if ($this->pageChanged($step)) {
            $this->logDebug(
                'Page URI changed; resetting readiness metric failure tracking',
                [
                    'step' => $step->__toString(),
                    'newUri' => $this->getUri()
                ]
            );
            /** @var AbstractMetricCheck $metric */
            foreach ($this->readinessMetrics as $metric) {
                $metric->resetTracker();
            }
        }

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
                'uri' => $this->getUri()
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
