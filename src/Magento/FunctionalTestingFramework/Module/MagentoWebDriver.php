<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Module;

use Codeception\Events;
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
use Magento\FunctionalTestingFramework\Util\Protocol\CurlTransport;
use Magento\FunctionalTestingFramework\Util\Protocol\CurlInterface;
use Magento\Setup\Exception;
use Magento\FunctionalTestingFramework\Util\ConfigSanitizerUtil;
use Yandex\Allure\Adapter\Event\TestCaseFinishedEvent;
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
    public static $loadingMasksLocators = [
        '//div[contains(@class, "loading-mask")]',
        '//div[contains(@class, "admin_data-grid-loading-mask")]',
        '//div[contains(@class, "admin__data-grid-loading-mask")]',
        '//div[contains(@class, "admin__form-loading-mask")]',
        '//div[@data-role="spinner"]'
    ];

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
     * Current Test Interface
     *
     * @var TestInterface
     */
    private $current_test;

    /**
     * Png image filepath for current test
     *
     * @var string
     */
    private $pngReport;

    /**
     * Html filepath for current test
     *
     * @var string
     */
    private $htmlReport;

    public function _initialize()
    {
        $this->config = ConfigSanitizerUtil::sanitizeWebDriverConfig($this->config);
        parent::_initialize();
    }

    public function _resetConfig()
    {
        parent::_resetConfig();
        $this->config = ConfigSanitizerUtil::sanitizeWebDriverConfig($this->config);
    }

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
     * Assert that the current webdriver url does not equal the expected string.
     *
     * @param string $url
     * @return void
     */
    public function dontSeeCurrentUrlEquals($url)
    {
        $this->assertNotEquals($url, $this->webDriver->getCurrentURL());
    }

    /**
     * Assert that the current webdriver url does not match the expected regex.
     *
     * @param string $regex
     * @return void
     */
    public function dontSeeCurrentUrlMatches($regex)
    {
        $this->assertNotRegExp($regex, $this->webDriver->getCurrentURL());
    }

    /**
     * Assert that the current webdriver url does not contain the expected string.
     *
     * @param string $needle
     * @return void
     */
    public function dontSeeInCurrentUrl($needle)
    {
        $this->assertNotContains($needle, $this->webDriver->getCurrentURL());
    }

    /**
     * Return the current webdriver url or return the first matching capture group.
     *
     * @param string|null $regex
     * @return string
     */
    public function grabFromCurrentUrl($regex = null)
    {
        $fullUrl = $this->webDriver->getCurrentURL();
        if (!$regex) {
            return $fullUrl;
        }
        $matches = [];
        $res = preg_match($regex, $fullUrl, $matches);
        if (!$res) {
            $this->fail("Couldn't match $regex in " . $fullUrl);
        }
        if (!isset($matches[1])) {
            $this->fail("Nothing to grab. A regex parameter with a capture group is required. Ex: '/(foo)(bar)/'");
        }
        return $matches[1];
    }

    /**
     * Assert that the current webdriver url equals the expected string.
     *
     * @param string $url
     * @return void
     */
    public function seeCurrentUrlEquals($url)
    {
        $this->assertEquals($url, $this->webDriver->getCurrentURL());
    }

    /**
     * Assert that the current webdriver url matches the expected regex.
     *
     * @param string $regex
     * @return void
     */
    public function seeCurrentUrlMatches($regex)
    {
        $this->assertRegExp($regex, $this->webDriver->getCurrentURL());
    }

    /**
     * Assert that the current webdriver url contains the expected string.
     *
     * @param string $needle
     * @return void
     */
    public function seeInCurrentUrl($needle)
    {
        $this->assertContains($needle, $this->webDriver->getCurrentURL());
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
     * @throws \Exception
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

        $this->selectMultipleOptions($selectSearchText, $selectSearchResult, $options);

        if ($requireAction) {
            $selectAction = $select . ' button[class=action-default]';
            $this->waitForPageLoad();
            $this->click($selectAction);
        }
    }

    /**
     * Select multiple options from a drop down using a filter and text field to narrow results.
     *
     * @param string $selectSearchTextField
     * @param string $selectSearchResult
     * @param string[] $options
     * @return void
     */
    public function selectMultipleOptions($selectSearchTextField, $selectSearchResult, array $options)
    {
        foreach ($options as $option) {
            $this->waitForPageLoad();
            $this->fillField($selectSearchTextField, '');
            $this->waitForPageLoad();
            $this->fillField($selectSearchTextField, $option);
            $this->waitForPageLoad();
            $this->click($selectSearchResult);
        }
    }

    /**
     * Wait for all Ajax calls to finish.
     *
     * @param int $timeout
     */
    public function waitForAjaxLoad($timeout = null)
    {
        $timeout = $timeout ?? $this->_getConfig()['pageload_timeout'];

        try {
            $this->waitForJS('return !!window.jQuery && window.jQuery.active == 0;', $timeout);
        } catch (\Exception $exceptione) {
            $this->debug("js never executed, performing {$timeout} second wait.");
            $this->wait($timeout);
        }
        $this->wait(1);
    }

    /**
     * Wait for all JavaScript to finish executing.
     *
     * @param int $timeout
     * @throws \Exception
     */
    public function waitForPageLoad($timeout = null)
    {
        $timeout = $timeout ?? $this->_getConfig()['pageload_timeout'];

        $this->waitForJS('return document.readyState == "complete"', $timeout);
        $this->waitForAjaxLoad($timeout);
        $this->waitForLoadingMaskToDisappear();
    }

    /**
     * Wait for all visible loading masks to disappear. Gets all elements by mask selector, then loops over them.
     *
     * @throws \Exception
     */
    public function waitForLoadingMaskToDisappear()
    {
        foreach( self::$loadingMasksLocators as $maskLocator) {
            // Get count of elements found for looping.
            // Elements are NOT useful for interaction, as they cannot be fed to codeception actions.
            $loadingMaskElements = $this->_findElements($maskLocator);
            for ($i = 1; $i <= count($loadingMaskElements); $i++) {
                // Formatting and looping on i as we can't interact elements returned above
                // eg.  (//div[@data-role="spinner"])[1]
                $this->waitForElementNotVisible("({$maskLocator})[{$i}]", 30);
            }
        }
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
     * Takes given $command and executes it against exposed MTF CLI entry point. Returns response from server.
     * @param string $command
     * @returns string
     */
    public function executeMagentoCLICommand($command)
    {

        $apiURL = $this->config['url'] . getenv('MAGENTO_CLI_COMMAND_PATH');
        $executor = new CurlTransport();
        $executor->write($apiURL, [getenv('MAGENTO_CLI_COMMAND_PARAMETER') => $command], CurlInterface::POST, []);
        $response = $executor->read();
        $executor->close();
        return $response;
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
     * Assert that an element contains a given value for the specific attribute.
     *
     * @param string $selector
     * @param string $attribute
     * @param $value
     */
    public function assertElementContainsAttribute($selector, $attribute, $value)
    {
        $attributes = $this->grabAttributeFrom($selector, $attribute);

        if (isset($value) && empty($value)) {
            // If an "attribute" is blank, "", or null we need to be able to assert that it's present.
            // When an "attribute" is blank or null it returns "true" so we assert that "true" is present.
            $this->assertEquals($attributes, 'true');
        } else {
            $this->assertContains($value, $attributes);
        }
    }

    public function _before(TestInterface $test)
    {
        $this->current_test = $test;
        $this->htmlReport = null;
        $this->pngReport = null;

        parent::_before($test);
    }

    /**
     * Override for _failed method in Codeception method. Adds png and html attachments to allure report
     * following parent execution of test failure processing.
     *
     * @param TestInterface $test
     * @param \Exception $fail
     */
    public function _failed(TestInterface $test, $fail)
    {
        $this->debugWebDriverLogs($test);

        if ($this->pngReport == null && $this->htmlReport == null) {
            $this->saveScreenshot();
        }

        $this->addAttachment($this->pngReport, $test->getMetadata()->getName() . '.png', 'image/png');
        $this->addAttachment($this->htmlReport, $test->getMetadata()->getName() . '.html', 'text/html');

        $this->debug("Failure due to : {$fail->getMessage()}");
        $this->debug("Screenshot saved to {$this->pngReport}");
        $this->debug("Html saved to {$this->htmlReport}");
    }

    /**
     * Function which saves a screenshot of the current stat of the browser
     */
    public function saveScreenshot()
    {
        $test = $this->current_test;
        $filename = preg_replace('~\W~', '.', Descriptor::getTestSignature($test));
        $outputDir = codecept_output_dir();
        $this->_saveScreenshot($this->pngReport = $outputDir . mb_strcut($filename, 0, 245, 'utf-8') . '.fail.png');
        $this->_savePageSource($this->htmlReport = $outputDir . mb_strcut($filename, 0, 244, 'utf-8') . '.fail.html');
    }
}