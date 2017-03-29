<?php
namespace Magento\Xxyyzz\Helper;

/**
 * Class AdminUiHelper
 * 
 * Define general Magento Admin Ui actions
 * All public methods declared in helper class will be available in $I
 */
class AdminUiHelper extends \Codeception\Module
{
    /**
     * Magento admin username
     * @var string
     */
    protected static $adminUsername;

    /**
     * Magento admin password
     * @var string
     */
    protected static $adminPassword;

    /**
     * @var \Codeception\Module\WebDriver
     */
    protected $webDriver;

    /**
     * Initialize helper.
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->webDriver = $this->getModule('WebDriver');
        if (!isset(self::$adminUsername) || !isset(self::$adminUsername)) {
            $config = \Codeception\Configuration::suiteSettings('acceptance', \Codeception\Configuration::config());
            self::$adminUsername = $config['data']['magento']['admin_username'];
            self::$adminPassword = $config['data']['magento']['admin_password'];
        }
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
        $this->webDriver->fillField('login[username]', !is_null($username) ? $username : self::$adminUsername);
        $this->webDriver->fillField('login[password]', !is_null($password) ? $password : self::$adminPassword);
        $this->webDriver->click('Sign in');

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
        $this->webDriver->executeJS("jQuery('.modal-popup').remove(); jQuery('.modals-overlay').remove();");

//        try {
//            $I->waitForElementVisible('._show .action-close', 1);
//            $I->click('._show .action-close');
//            $I->waitForElementNotVisible('._show .action-close', 1);
//        } catch (\Exception $e) {
//            return false;
//        }
    }
}
