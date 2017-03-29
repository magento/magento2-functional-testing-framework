<?php
namespace Magento\Xxyyzz\Page\Customer;

class CustomerAccountDashboardPage
{
    // include url of current page
    public static $URL = '/customer/account/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $pageTitle                    = '.page-title';
    public static $customerLoginForm            = '#login-form';
    public static $customerEmailField           = '#email';
    public static $customerPasswordField        = '#pass';
    public static $customerSignInButton         = '#send2';
    public static $customerForgotPasswordLink   = '.action.remind>span';
    public static $createNewAccountLink         = '.action.create.primary>span';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL . $param;
    }

    public function amOnCustomerAccountLoginPage(\AcceptanceTester $I)
    {
        $I->amOnPage(self::$URL);
        $I->waitForElementVisible(self::$customerLoginForm, 30);
    }

    public function signInWithCredentials(\AcceptanceTester $I, $email, $password)
    {
        $I->fillField(self::$customerEmailField, $email);
        $I->fillField(self::$customerPasswordField, $password);
        $I->click(self::$customerSignInButton);
    }
}
