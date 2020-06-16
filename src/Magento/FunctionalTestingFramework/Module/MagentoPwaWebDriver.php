<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Module;

use Codeception\Module\WebDriver;

/**
 * Class MagentoPwaActions
 *
 * Contains all custom PWA action functions to be used in PWA tests.
 *
 * @package Magento\FunctionalTestingFramework\Module
 */
class MagentoPwaWebDriver extends MagentoWebDriver
{
    /**
     * List of known PWA loading masks by selector
     *
     * Overriding the MagentoWebDriver array to contain applicable PWA locators.
     *
     * @var array
     */
    protected $loadingMasksLocators = [
        '//div[contains(@class, "indicator-global-")]',
        '//div[contains(@class, "indicator-root-")]',
        '//img[contains(@class, "indicator-indicator-")]',
        '//span[contains(@class, "indicator-message-")]'
    ];

    /**
     * Go to the page.
     *
     * Overriding the MagentoWebDriver version because it contains 'waitForPageLoad'.
     * The AJAX check in 'waitForPageLoad' does NOT work with a PWA.
     *
     * @param string $page
     * @param integer $timeout
     * @throws \Exception
     * @return void
     */
    public function amOnPage($page, $timeout = null)
    {
        WebDriver::amOnPage($page);
        $this->waitForLoadingMaskToDisappear($timeout);
    }

    /**
     * Wait for a PWA Element to NOT be visible using JavaScript.
     * Add the WAIT_TIMEOUT variable to your .env file for this action.
     *
     * @param string $selector
     * @param integer $timeout
     * @throws \Exception
     * @return void
     */
    public function waitForPwaElementNotVisible($selector, $timeout = null)
    {
        $timeout = $timeout ?? $this->_getConfig()['pageload_timeout'];

        // Determine what type of Selector is used.
        // Then use the correct JavaScript to locate the Element.
        if (\Codeception\Util\Locator::isXPath($selector)) {
            $this->waitForLoadingMaskToDisappear($timeout);
            $this->waitForJS("return !document.evaluate(`$selector`, document);", $timeout);
        } else {
            $this->waitForLoadingMaskToDisappear($timeout);
            $this->waitForJS("return !document.querySelector(`$selector`);", $timeout);
        }
    }

    /**
     * Wait for a PWA Element to be visible using JavaScript.
     * Add the WAIT_TIMEOUT variable to your .env file for this action.
     *
     * @param string $selector
     * @param integer $timeout
     * @throws \Exception
     * @return void
     */
    public function waitForPwaElementVisible($selector, $timeout = null)
    {
        $timeout = $timeout ?? $this->_getConfig()['pageload_timeout'];
        
        // Determine what type of Selector is used.
        // Then use the correct JavaScript to locate the Element.
        if (\Codeception\Util\Locator::isXPath($selector)) {
            $this->waitForLoadingMaskToDisappear($timeout);
            $this->waitForJS("return !!document && !!document.evaluate(`$selector`, document);", $timeout);
        } else {
            $this->waitForLoadingMaskToDisappear($timeout);
            $this->waitForJS("return !!document && !!document.querySelector(`$selector`);", $timeout);
        }
    }
}
