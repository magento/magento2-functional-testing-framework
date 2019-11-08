<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Module;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * MagentoWebDriverDoctor module extends MagentoWebDriver module and is a light weighted module to diagnose webdriver
 * initialization and other setup issues. It uses in memory version of MagentoWebDriver's configuration file
 */
class MagentoWebDriverDoctor extends MagentoWebDriver
{
    const MAGENTO_CLI_COMMAND = 'list';
    const EXCEPTION_TYPE_SELENIUM = 'selenium';
    const EXCEPTION_TYPE_MAGENTO_CLI = 'cli';

    /**
     * Go through parent initialization routines and in addition diagnose potential environment issues
     *
     * @return void
     * @throws TestFrameworkException
     */
    public function _initialize()
    {
        parent::_initialize();

        $context = [];

        try {
            $this->checkSeleniumServerReadiness();
        } catch (TestFrameworkException $e) {
            $context[self::EXCEPTION_TYPE_SELENIUM] = $e->getMessage();
        }

        try {
            $this->checkMagentoCLI();
        } catch (TestFrameworkException $e) {
            $context[self::EXCEPTION_TYPE_MAGENTO_CLI] = $e->getMessage();
        }

        if (!empty($context)) {
            throw new TestFrameworkException('MagentoWebDriverDoctor initialization failed', $context);
        }
    }

    /**
     * Check connectivity to running selenium server
     *
     * @return void
     * @throws TestFrameworkException
     */
    private function checkSeleniumServerReadiness()
    {
        try {
            $driver = RemoteWebDriver::create(
                $this->wdHost,
                $this->capabilities,
                $this->connectionTimeoutInMs,
                $this->requestTimeoutInMs,
                $this->httpProxy,
                $this->httpProxyPort
            );
            $driver->close();
        } catch (\Exception $e) {
            throw new TestFrameworkException(
                "Can't connect to Webdriver at {$this->wdHost}.\n"
                . "Please make sure that Selenium Server is running."
            );
        }
    }

    /**
     * Check Magento CLI setup
     *
     * @return void
     * @throws TestFrameworkException
     */
    private function checkMagentoCLI()
    {
        parent::magentoCLI(self::MAGENTO_CLI_COMMAND);
    }
}
