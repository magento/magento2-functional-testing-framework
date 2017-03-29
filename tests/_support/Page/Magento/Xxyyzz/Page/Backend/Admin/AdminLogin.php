<?php
namespace Magento\Xxyyzz\Page\Backend\Admin;

class AdminLogin
{
    // include url of current page
    public static $URL = '/admin/admin/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $mainArea           = '#adminhtml-auth-login .page-wrapper';
    public static $logoLink           = '.logo';
    public static $logoImage          = '.logo-img';
    public static $title              = '.admin__legend';
    public static $usernameTitle      = '.field-username label';
    public static $username           = '#username';
    public static $passwordTitle      = '.field-password label';
    public static $password           = '#login';
    public static $forgotYourPassword = '.action-forgotpassword';
    public static $signIn             = '.actions .action-primary';
    public static $copyRight          = '.login-footer';

    public static $forgotPasswordMain = '.adminhtml-auth-forgotpassword';
    public static $forgotPasswordText = '.admin__field-info';
    public static $emailAddressTitle  = '.field-email label';
    public static $emailAddress       = '#email';
    public static $retrievePassword   = '.actions .action-primary';
    public static $backToSignIn       = '.action-back';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

    /**
     * @var \AcceptanceTester;
     */
    protected $acceptanceTester;

    public function __construct(\AcceptanceTester $I)
    {
        $this->acceptanceTester = $I;
    }

    public function clickOnMagentoLogo(\AcceptanceTester $I)
    {
        $I->click(self::$logoImage);
    }

    public function clickOnForgotYourPassword(\AcceptanceTester $I)
    {
        $I->click(self::$forgotYourPassword);
    }

    public function clickOnSignIn(\AcceptanceTester $I)
    {
        $I->click(self::$signIn);
    }

    public function enterTheUsername(\AcceptanceTester $I, $username)
    {
        $I->fillField(self::$username, $username);
    }

    public function enterThePassword(\AcceptanceTester $I, $password)
    {
        $I->fillField(self::$password, $password);
    }

    public function enterTheLoginCredentials(\AcceptanceTester $I, $username, $password)
    {
        $this->enterTheUsername($I, $username);
        $this->enterThePassword($I, $password);
    }

    public function shouldSeeTheLoginMainArea(\AcceptanceTester $I)
    {
        $I->seeElement(self::$mainArea);
    }

    public function shouldSeeTheLoginLogoLink(\AcceptanceTester $I)
    {
        $I->seeElement(self::$logoLink);
    }

    public function shouldSeeTheLoginLogoImage(\AcceptanceTester $I)
    {
        $I->seeElement(self::$logoImage);
    }

    public function shouldSeeTheLoginTitle(\AcceptanceTester $I)
    {
        $I->seeElement(self::$title);
    }

    public function shouldSeeTheLoginUsernameTitle(\AcceptanceTester $I)
    {
        $I->seeElement(self::$usernameTitle);
    }

    public function shouldSeeTheLoginUsernameField(\AcceptanceTester $I)
    {
        $I->seeElement(self::$username);
    }

    public function shouldSeeTheLoginPasswordTitle(\AcceptanceTester $I)
    {
        $I->seeElement(self::$passwordTitle);
    }

    public function shouldSeeTheLoginPasswordField(\AcceptanceTester $I)
    {
        $I->seeElement(self::$password);
    }

    public function shouldSeeTheLoginForgotPasswordLink(\AcceptanceTester $I)
    {
        $I->seeElement(self::$forgotYourPassword);
    }

    public function shouldSeeTheLoginSignInButton(\AcceptanceTester $I)
    {
        $I->seeElement(self::$signIn);
    }

    public function shouldSeeTheLoginCopyrightText(\AcceptanceTester $I)
    {
        $I->seeElement(self::$copyRight);
    }

    public function shouldSeeTheLoginPageFields(\AcceptanceTester $I)
    {
        $this->shouldSeeTheLoginMainArea($I);
        $this->shouldSeeTheLoginLogoLink($I);
        $this->shouldSeeTheLoginLogoImage($I);
        $this->shouldSeeTheLoginTitle($I);
        $this->shouldSeeTheLoginUsernameTitle($I);
        $this->shouldSeeTheLoginUsernameField($I);
        $this->shouldSeeTheLoginPasswordTitle($I);
        $this->shouldSeeTheLoginPasswordField($I);
        $this->shouldSeeTheLoginForgotPasswordLink($I);
        $this->shouldSeeTheLoginSignInButton($I);
        $this->shouldSeeTheLoginCopyrightText($I);
    }

    public function enterTheEmailAddress(\AcceptanceTester $I, $emailAddress)
    {
        $I->fillField(self::$emailAddress, $emailAddress);
    }

    public function clickOnRetrievePassword(\AcceptanceTester $I)
    {
        $I->click(self::$retrievePassword);
    }

    public function clickOnBackToSignIn(\AcceptanceTester $I)
    {
        $I->click(self::$backToSignIn);
    }

    public function shouldSeeTheForgotYourPasswordFields(\AcceptanceTester $I)
    {
        $I->seeElement(self::$forgotPasswordMain);
        $I->seeElement(self::$logoLink);
        $I->seeElement(self::$logoImage);
        $I->seeElement(self::$title);
        $I->seeElement(self::$emailAddressTitle);
        $I->seeElement(self::$emailAddress);
        $I->seeElement(self::$retrievePassword);
        $I->seeElement(self::$backToSignIn);
    }
}
