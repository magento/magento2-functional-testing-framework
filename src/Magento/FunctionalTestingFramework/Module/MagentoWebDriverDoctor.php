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
 * initialization and other setup issues. It uses in memory version of MagentoWebDriver's configuration file.
 */
class MagentoWebDriverDoctor extends MagentoWebDriver
{
    const MAGENTO_CLI_COMMAND = 'info:currency:list';
    const EXCEPTION_CONTEXT_SELENIUM = 'selenium';
    const EXCEPTION_CONTEXT_ADMIN = 'admin';
    const EXCEPTION_CONTEXT_STOREFRONT = 'store';
    const EXCEPTION_CONTEXT_CLI = 'cli';

    /**
     * Remote Web Driver
     *
     * @var RemoteWebDriver
     */
    private $remoteWebDriver = null;

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
            $this->connectToSeleniumServer();
        } catch (TestFrameworkException $e) {
            $context[self::EXCEPTION_CONTEXT_SELENIUM] = $e->getMessage();
        }

        try {
            $adminUrl = rtrim(getenv('MAGENTO_BACKEND_BASE_URL'), '/')
                ?: rtrim(getenv('MAGENTO_BASE_URL'), '/')
                . '/' . getenv('MAGENTO_BACKEND_NAME') . '/admin';
            $this->loadPageAtUrl($adminUrl);
        } catch (\Exception $e) {
            $context[self::EXCEPTION_CONTEXT_ADMIN] = $e->getMessage();
        }

        try {
            $storeUrl = getenv('MAGENTO_BASE_URL');
            $this->loadPageAtUrl($storeUrl);
        } catch (\Exception $e) {
            $context[self::EXCEPTION_CONTEXT_STOREFRONT] = $e->getMessage();
        }

        try {
            $this->runMagentoCLI();
        } catch (\Exception $e) {
            $context[self::EXCEPTION_CONTEXT_CLI] = $e->getMessage();
        }

        if (null !== $this->remoteWebDriver) {
            $this->remoteWebDriver->close();
        }

        if (!empty($context)) {
            throw new TestFrameworkException('Exception occurred in MagentoWebDriverDoctor', $context);
        }
    }

    /**
     * Check connecting to running selenium server
     *
     * @return void
     * @throws TestFrameworkException
     */
    private function connectToSeleniumServer()
    {
        try {
            $this->remoteWebDriver = RemoteWebDriver::create(
                $this->wdHost,
                $this->capabilities,
                $this->connectionTimeoutInMs,
                $this->requestTimeoutInMs,
                $this->config['http_proxy'],
                $this->config['http_proxy_port']
            );
            if (null !== $this->remoteWebDriver) {
                return;
            }
        } catch (\Exception $e) {
        }

        throw new TestFrameworkException(
            "Failed to connect Selenium WebDriver at: {$this->wdHost}.\n"
            . "Please make sure that Selenium Server is running."
        );
    }

    /**
     * Validate loading a web page at url in the browser controlled by selenium
     *
     * @param string $url
     * @return void
     * @throws TestFrameworkException
     */
    private function loadPageAtUrl($url)
    {
        try {
            if (null !== $this->remoteWebDriver) {
                // Open the web page at url first
                $this->remoteWebDriver->get($url);

                // Execute Javascript to retrieve HTTP response code
                $script = ''
                    . 'var xhr = new XMLHttpRequest();'
                    . "xhr.open('GET', '" . $url . "', false);"
                    . 'xhr.send(null); '
                    . 'return xhr.status';
                $status = $this->remoteWebDriver->executeScript($script);

                if ($status === 200) {
                    return;
                }
            }
        } catch (\Exception $e) {
        }

        throw new TestFrameworkException(
            "Failed to load page at url: $url\n"
            . "Please check Selenium Browser session have access to Magento instance."
        );
    }

    /**
     * Check running Magento CLI command
     *
     * @return void
     * @throws TestFrameworkException
     */
    private function runMagentoCLI()
    {
        try {
            $regex = '~^.*[\r\n]+.*(?<name>Currency).*(?<code>Code).*~';
            $output = parent::magentoCLI(self::MAGENTO_CLI_COMMAND);
            preg_match($regex, $output, $matches);

            if (isset($matches['name']) && isset($matches['code'])) {
                return;
            }
        } catch (\Exception $e) {
        }

        throw new TestFrameworkException(
            "Failed to run Magento CLI command\n"
            . "Please reference Magento DevDoc to setup command.php and .htaccess files."
        );
    }
}
