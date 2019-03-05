<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Module;

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
     * Wait for a PWA Element to NOT be visible using JavaScript.
     *
     * @param null $selector
     * @param null $timeout
     * @throws \Exception
     * @return void
     */
    public function waitForPwaElementNotVisible($selector, $timeout = null)
    {
        $timeout = $timeout ?? $this->_getConfig()['pageload_timeout'];

        // Determine what type of Selector is used.
        // Then use the correct JavaScript to locate the Element.
        if (\Codeception\Util\Locator::isXPath($selector)) {
            $this->waitForJS("return !document.evaluate(`$selector`, document);", $timeout);
        } else {
            $this->waitForJS("return !document.querySelector(`$selector`);", $timeout);
        }
    }

    /**
     * Wait for a PWA Element to be visible using JavaScript.
     *
     * @param null $selector
     * @param null $timeout
     * @throws \Exception
     * @return void
     */
    public function waitForPwaElementVisible($selector, $timeout = null)
    {
        $timeout = $timeout ?? $this->_getConfig()['pageload_timeout'];

        // Determine what type of Selector is used.
        // Then use the correct JavaScript to locate the Element.
        if (\Codeception\Util\Locator::isXPath($selector)) {
            $this->waitForJS("return !!document && !!document.evaluate(`$selector`, document);", $timeout);
        } else {
            $this->waitForJS("return !!document && !!document.querySelector(`$selector`);", $timeout);
        }
    }
}
