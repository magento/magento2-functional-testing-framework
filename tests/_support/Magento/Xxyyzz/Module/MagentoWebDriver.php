<?php
namespace Magento\Xxyyzz\Module;

use Codeception\Module\WebDriver;
use Codeception\Exception\ModuleConfigException;
use Codeception\Exception\ModuleException;
use Codeception\Util\Uri;

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
        $this->executeJS("jQuery('.modal-popup').remove(); jQuery('.modals-overlay').remove();");

//        try {
//            $I->waitForElementVisible('._show .action-close', 1);
//            $I->click('._show .action-close');
//            $I->waitForElementNotVisible('._show .action-close', 1);
//        } catch (\Exception $e) {
//            return false;
//        }
    }
}
