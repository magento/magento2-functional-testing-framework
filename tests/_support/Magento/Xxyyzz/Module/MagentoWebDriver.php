<?php
namespace Magento\Xxyyzz\Module;

use Codeception\Module\WebDriver;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Codeception\Exception\ElementNotFound;
use Codeception\Exception\ModuleConfigException;
use Codeception\Exception\ModuleException;
use Codeception\Util\Uri;
use Codeception\Util\ActionSequence;

/**
 * MagentoWebDriver module provides common Magento web actions through Selenium WebDriver.
 *
 * Configuration:
 *
 * ```
 * modules:
 *     enabled:
 *         - \Magento\Xxyyzz\Module\MagentoWebDriver
 *     config:
 *         \Magento\Xxyyzz\Module\MagentoWebDriver:
 *             url: magento_base_url
 *             backend_name: magento_backend_name
 *             username: admin_username
 *             password: admin_password
 *             browser: chrome
 * ```
 */
class MagentoWebDriver extends WebDriver
{
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
     * Returns URL of a host.
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

        $this->waitPageLoad();
        $this->waitForElementVisible($selectDropdown);
        $this->click($selectDropdown);
        foreach ($options as $option) {
            $this->waitPageLoad();
            $this->fillField($selectSearchText, '');
            $this->waitPageLoad();
            $this->fillField($selectSearchText, $option);
            $this->waitPageLoad();
            $this->click($selectSearchResult);
        }
        if ($requireAction) {
            $selectAction = $select . ' button[class=action-default]';
            $this->waitPageLoad();
            $this->click($selectAction);
        }
    }

    public function waitAjaxLoad($timeout = 15)
    {
        $this->waitForJS('return !!window.jQuery && window.jQuery.active == 0;', $timeout);
        $this->wait(1);
        $this->dontSeeJsError();
    }

    public function waitPageLoad($timeout = 15)
    {
        $this->waitForJS('return document.readyState == "complete"', $timeout);
        $this->waitAjaxLoad($timeout);
        $this->dontSeeJsError();
    }

    public function dontSeeJsError()
    {
        $logs = $this->webDriver->manage()->getLog('browser');
        foreach ($logs as $log) {
            if ($log['level'] == 'SEVERE') {
                throw new ModuleException($this, 'Errors in JavaScript: ' . json_encode($log));
            }
        }
    }
}
