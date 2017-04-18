<?php
namespace Magento\Xxyyzz\Page\Customer;

use Magento\Xxyyzz\Page\AbstractFrontendPage;

class CustomerAccountDashboardPage extends AbstractFrontendPage
{
    /**
     * Include url of current page.
     */
    public static $URL = '/customer/account/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $customerLoginForm            = '#login-form';
    public static $customerEmailField           = '#email';
    public static $customerPasswordField        = '#pass';
    public static $customerSignInButton         = '#send2';
    public static $customerForgotPasswordLink   = '.action.remind>span';
    public static $createNewAccountLink         = '.action.create.primary>span';

    public function amOnCustomerAccountLoginPage()
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::$URL);
        $I->waitForElementVisible(self::$customerLoginForm, $this->pageLoadTimeout);
    }

    public function signInWithCredentials($email, $password)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$customerEmailField, $email);
        $I->fillField(self::$customerPasswordField, $password);
        $I->click(self::$customerSignInButton);
    }
}
