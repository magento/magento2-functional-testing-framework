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
use Codeception\TestInterface;
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
        Events::STEP_BEFORE => 'beforeStep',
        Events::STEP_AFTER => 'afterStep'
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
     * Active test object
     *
     * @var TestInterface
     */
    private $test;

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
        $this->test = $e->getTest();

        if (isset($this->config['resetFailureThreshold'])) {
            $failThreshold = intval($this->config['resetFailureThreshold']);
        } else {
            $failThreshold = 3;
        }

        $metrics = [];
        foreach ($this->config['readinessMetrics'] as $metricClass) {
            $metrics[] = new $metricClass($this, $this->test, $failThreshold);
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
        if ($step->getAction() == 'saveScreenshot') {
            return;
        }

        try {
            $this->test->getMetadata()->setCurrent(['uri', $this->getDriver()->_getCurrentUri()]);
        } catch (\Exception $exception) {
            $this->logDebug('Could not retrieve current URI', ['action' => $e->getStep()->getAction()]);
        }

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
            $metric->finalize($step);
        }
    }

    /**
     * Checks to see if the step changed the uri and resets failure tracking if so
     *
     * @param StepEvent $e
     * @return void
     */
    public function afterStep(StepEvent $e)
    {
        $step = $e->getStep();
        if ($step->getAction() == 'saveScreenshot') {
            return;
        }

        try {
            $currentUri = $this->getDriver()->_getCurrentUri();
        } catch (\Exception $e) {
            // $this->debugLog('Could not retrieve current URI', ['action' => $step()->getAction()]);
            return;
        }

        $previousUri = $this->test->getMetadata()->getCurrent('uri');

        if ($previousUri !== $currentUri) {
            $this->logDebug(
                'Page URI changed; resetting readiness metric failure tracking',
                [
                    'action' => $step->getAction(),
                    'newUri' => $currentUri
                ]
            );

            /** @var AbstractMetricCheck $metric */
            foreach ($this->readinessMetrics as $metric) {
                $metric->setTracker();
            }
        }
    }

    /**
     * If verbose, log the given message to logger->debug including test context information
     *
     * @param string $message
     * @param array  $context
     * @return void
     */
    private function logDebug($message, $context = [])
    {
        if ($this->verbose) {
            $testMeta = $this->test->getMetadata();
            $logContext = [
                'test' => $testMeta->getName(),
                'uri' => $testMeta->getCurrent('uri')
            ];
            foreach ($this->readinessMetrics as $metric) {
                $logContext[$metric->getName()] = $metric->getStoredValue();
                $logContext[$metric->getName() . '.failCount'] = $metric->getFailureCount();
            }
            $context = array_merge($logContext, $context);
            $this->logger->info($message, $context);
        }
    }
}
