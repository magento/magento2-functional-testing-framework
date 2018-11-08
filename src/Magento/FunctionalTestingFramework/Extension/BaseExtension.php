<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension;

use Codeception\Events;
use Codeception\Exception\ModuleRequireException;
use Codeception\Extension;
use Codeception\Module\WebDriver;

/**
 * Class BaseExtension
 */
class BaseExtension extends Extension
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
     * The current URI of the active page
     *
     * @var string
     */
    private $uri;

    /**
     * Codeception event listener function - initialize uri before test
     *
     * @param \Codeception\Event\TestEvent $e
     * @return void
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeTest(\Codeception\Event\TestEvent $e)
    {
        $this->uri = null;
    }

    /**
     * Codeception event listener function - check for page uri change before step
     *
     * @param \Codeception\Event\StepEvent $e
     * @return void
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeStep(\Codeception\Event\StepEvent $e)
    {
        $this->pageChanged();
    }

    /**
     * WebDriver instance for execution
     *
     * @return WebDriver
     * @throws ModuleRequireException
     */
    public function getDriver()
    {
        return $this->getModule($this->config['driver']);
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
     * Check if page uri has changed
     *
     * @return boolean
     */
    protected function pageChanged()
    {
        try {
            if ($this->getDriver() === null) {
                return false;
            }
            $currentUri = $this->getDriver()->_getCurrentUri();

            if ($this->uri !== $currentUri) {
                $this->uri = $currentUri;
                return true;
            }
        } catch (\Exception $e) {
            // just fall through and return false
        }
        return false;
    }
}
