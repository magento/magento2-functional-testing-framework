<?php
namespace Magento\Xxyyzz\Page\Customer;

use Magento\Xxyyzz\Page\AbstractFrontendPage;

class StorefrontCustomerAccountLoginPage extends AbstractFrontendPage
{
    /**
     * Include url of current page.
     */
    public static $URL = '/customer/account/login/';

    /**
     * Declare UI map for customer login page.
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
        $I->waitForPageLoad();
    }

    public function fillFieldCustomerEmail($email)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$customerEmailField, $email);
    }

    public function fillFieldCustomerPassword($password)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$customerPasswordField, $password);
    }

    public function clickSignInButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$customerSignInButton);
        $I->waitForPageLoad();
        $I->seeInCurrentUrl('customer/account');
    }

    public function clickCreateAccountButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$createNewAccountLink);
        $I->waitForPageLoad();
        $I->seeInCurrentUrl('customer/account/create');
    }

    public function signInWithCredentials($email, $password)
    {
        $this->fillFieldCustomerEmail($email);
        $this->fillFieldCustomerPassword($password);
        $this->clickSignInButton();
    }
}
