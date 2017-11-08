<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Module;

use Codeception\Module\WebDriver;
use Codeception\Test\Descriptor;
use Codeception\TestInterface;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Codeception\Exception\ElementNotFound;
use Codeception\Exception\ModuleConfigException;
use Codeception\Exception\ModuleException;
use Codeception\Util\Uri;
use Codeception\Util\ActionSequence;
use Yandex\Allure\Adapter\Support\AttachmentSupport;

/**
 * MagentoWebDriver module provides common Magento web actions through Selenium WebDriver.
 *
 * Configuration:
 *
 * ```
 * modules:
 *     enabled:
 *         - \Magento\FunctionalTestingFramework\Module\MagentoWebDriver
 *     config:
 *         \Magento\FunctionalTestingFramework\Module\MagentoWebDriver:
 *             url: magento_base_url
 *             backend_name: magento_backend_name
 *             username: admin_username
 *             password: admin_password
 *             browser: chrome
 * ```
 */
// @codingStandardsIgnoreFile
class MagentoWebDriver extends WebDriver
{
    use AttachmentSupport;
    public static $loadingMask = '.loading-mask';

    /**
     * The module required fields, to be set in the suite .yml configuration file.
     *
     * @var array
     */
    protected $requiredFields = [
        'url',
        'backend_name',
        'username',
        'password',
        'browser'
    ];

    /**
     * Set all Locale variables to NULL.
     *
     * @var array $localeAll
     */
    protected static $localeAll = [
        LC_COLLATE => null,
        LC_CTYPE => null,
        LC_MONETARY => null,
        LC_NUMERIC => null,
        LC_TIME => null,
        LC_MESSAGES => null,
    ];

    /**
     * Returns URL of a host.
     *
     * @api
     * @return mixed
     * @throws ModuleConfigException
     */
    public function _getUrl()
    {
        if (!isset($this->config['url'])) {
            throw new ModuleConfigException(
                __CLASS__,
                "Module connection failure. The URL for client can't bre retrieved"
            );
        }
        return $this->config['url'];
    }

    /**
     * Uri of currently opened page.
     *
     * @return string
     * @api
     * @throws ModuleException
     */
    public function _getCurrentUri()
    {
        $url = $this->webDriver->getCurrentURL();
        if ($url == 'about:blank') {
            throw new ModuleException($this, 'Current url is blank, no page was opened');
        }
        return Uri::retrieveUri($url);
    }

    /**
     * Login Magento Admin with given username and password.
     *
     * @param string $username
     * @param string $password
     * @return void
     */
    public function loginAsAdmin($username = null, $password = null)
    {
        $this->amOnPage($this->config['backend_name']);
        $this->fillField('login[username]', !is_null($username) ? $username : $this->config['username']);
        $this->fillField('login[password]', !is_null($password) ? $password : $this->config['password']);
        $this->click('Sign in');
        $this->waitForPageLoad();

        $this->closeAdminNotification();
    }

    /**
     * Close admin notification popup windows.
     *
     * @return void
     */
    public function closeAdminNotification()
    {
        // Cheating here for the minute. Still working on the best method to deal with this issue.
        try {
            $this->executeJS("jQuery('.modal-popup').remove(); jQuery('.modals-overlay').remove();");
        } catch (\Exception $e) {}
    }


    /**
     * Search for and Select multiple options from a Magento Multi-Select drop down menu.
     * e.g. The drop down menu you use to assign Products to Categories.
     *
     * @param $select
     * @param array $options
     * @param bool $requireAction
     */
    public function searchAndMultiSelectOption($select, array $options, $requireAction = false)
    {
        $selectDropdown     = $select . ' .action-select.admin__action-multiselect';
        $selectSearchText   = $select
            . ' .admin__action-multiselect-search-wrap>input[data-role="advanced-select-text"]';
        $selectSearchResult = $select . ' .admin__action-multiselect-label>span';

        $this->waitForPageLoad();
        $this->waitForElementVisible($selectDropdown);
        $this->click($selectDropdown);
        foreach ($options as $option) {
            $this->waitForPageLoad();
            $this->fillField($selectSearchText, '');
            $this->waitForPageLoad();
            $this->fillField($selectSearchText, $option);
            $this->waitForPageLoad();
            $this->click($selectSearchResult);
        }
        if ($requireAction) {
            $selectAction = $select . ' button[class=action-default]';
            $this->waitForPageLoad();
            $this->click($selectAction);
        }
    }

    /**
     * Wait for all Ajax calls to finish.
     *
     * @param int $timeout
     */
    public function waitForAjaxLoad($timeout = 15)
    {
        $this->waitForJS('return !!window.jQuery && window.jQuery.active == 0;', $timeout);
        $this->wait(1);
    }

    /**
     * Wait for all JavaScript to finish executing.
     *
     * @param int $timeout
     */
    public function waitForPageLoad($timeout = 15)
    {
        $this->waitForJS('return document.readyState == "complete"', $timeout);
        $this->waitForAjaxLoad($timeout);
        $this->waitForElementNotVisible('.loading-mask', 30);
        $this->waitForElementNotVisible('.admin_data-grid-loading-mask', 30);
        $this->waitForElementNotVisible('.admin__form-loading-mask', 30);
    }

    /**
     * Wait for the Loading mask to disappear.
     */
    public function waitForLoadingMaskToDisappear()
    {
        $this->waitForElementNotVisible(self::$loadingMask, 30);
    }

    /**
     * Verify that there are no JavaScript errors in the console.
     *
     * @throws ModuleException
     */
    public function dontSeeJsError()
    {
        $logs = $this->webDriver->manage()->getLog('browser');
        foreach ($logs as $log) {
            if ($log['level'] == 'SEVERE') {
                throw new ModuleException($this, 'Errors in JavaScript: ' . json_encode($log));
            }
        }
    }

    /**
     * @param float $money
     * @param string $locale
     * @return array
     */
    public function formatMoney(float $money, $locale = 'en_US.UTF-8')
    {
        $this->mSetLocale(LC_MONETARY, $locale);
        $money = money_format('%.2n', $money);
        $this->mResetLocale();
        $prefix = substr($money, 0, 1);
        $number = substr($money, 1);
        return ['prefix' => $prefix, 'number' => $number];
    }

    /**
     * Parse float number with thousands_sep.
     *
     * @param string $floatString
     * @return float
     */
    public function parseFloat($floatString){
        $floatString = str_replace(',', '', $floatString);
        return floatval($floatString);
    }

    /**
     * @param int $category
     * @param string $locale
    */
    public function mSetLocale(int $category, $locale)
    {
        if (self::$localeAll[$category] == $locale) {
            return;
        }
        foreach (self::$localeAll as $c => $l) {
            self::$localeAll[$c] = setlocale($c, 0);
        }
        setlocale($category, $locale);
    }

    /**
     * Reset Locale setting.
     */
    public function mResetLocale()
    {
        foreach (self::$localeAll as $c => $l) {
            if (!is_null($l)) {
                setlocale($c, $l);
                self::$localeAll[$c] = null;
            }
        }
    }

    /**
     * Scroll to the Top of the Page.
     */
    public function scrollToTopOfPage()
    {
        $this->executeJS('window.scrollTo(0,0);');
    }

    /**
     * Conditional click for an area that should be visible
     *
     * @param string $selector
     * @param string $dependentSelector
     * @param bool $visible
     * @throws \Exception
     */
    public function conditionalClick($selector, $dependentSelector, $visible)
    {
        $el = $this->_findElements($dependentSelector);
        if (sizeof($el) > 1) {
            throw new \Exception("more than one element matches selector " . $selector);
        }

        $clickCondition = null;
        if ($visible) {
            $clickCondition = !empty($el) && $el[0]->isDisplayed();
        } else {
            $clickCondition = empty($el) || !$el[0]->isDisplayed();
        }

        if ($clickCondition) {
            $this->click($selector);
        }
    }

    /**
     * Clear the given Text Field or Textarea
     *
     * @param string $selector
     */
    public function clearField($selector)
    {
        $this->fillField($selector, "");
    }

    /**
     * Override for _failed method in Codeception method. Adds png and html attachments to allure report
     * following parent execution of test failure processing.
     * @param TestInterface $test
     * @param \Exception $fail
     */
    public function _failed(TestInterface $test, $fail)
    {
        parent::_failed($test, $fail);

        // Reconstruct file naming from codeception method
        $filename = preg_replace('~\W~', '.', Descriptor::getTestSignature($test));
        $outputDir = codecept_output_dir();
        $pngReport = $outputDir . mb_strcut($filename, 0, 245, 'utf-8') . '.fail.png';
        $htmlReport = $outputDir . mb_strcut($filename, 0, 244, 'utf-8') . '.fail.html';
        $this->addAttachment($pngReport, $test->getMetadata()->getName() . '.png', 'image/png');
        $this->addAttachment($htmlReport, $test->getMetadata()->getName() . '.html', 'text/html');
    }
}
