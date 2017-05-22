<?php
namespace Magento\Xxyyzz\Page\Backend;

use Magento\Xxyyzz\Page\AbstractAdminPage;

class AdminLogin extends AbstractAdminPage
{
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

    public function clickOnMagentoLogo()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$logoImage);
    }

    public function clickOnForgotYourPassword()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$forgotYourPassword);
    }

    public function clickOnSignIn()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$signIn);
    }

    public function enterTheUsername($username)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$username, $username);
    }

    public function enterThePassword($password)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$password, $password);
    }

    public function enterTheLoginCredentials($username, $password)
    {
        $I = $this->acceptanceTester;
        $this->enterTheUsername($username);
        $this->enterThePassword($password);
    }

    public function shouldSeeTheLoginMainArea()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$mainArea);
    }

    public function shouldSeeTheLoginLogoLink()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$logoLink);
    }

    public function shouldSeeTheLoginLogoImage()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$logoImage);
    }

    public function shouldSeeTheLoginTitle()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$title);
    }

    public function shouldSeeTheLoginUsernameTitle()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$usernameTitle);
    }

    public function shouldSeeTheLoginUsernameField()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$username);
    }

    public function shouldSeeTheLoginPasswordTitle()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$passwordTitle);
    }

    public function shouldSeeTheLoginPasswordField()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$password);
    }

    public function shouldSeeTheLoginForgotPasswordLink()
    {
        $I->seeElement(self::$forgotYourPassword);
    }

    public function shouldSeeTheLoginSignInButton()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$signIn);
    }

    public function shouldSeeTheLoginCopyrightText()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$copyRight);
    }

    public function shouldSeeTheLoginPageFields()
    {
        $this->shouldSeeTheLoginMainArea();
        $this->shouldSeeTheLoginLogoLink();
        $this->shouldSeeTheLoginLogoImage();
        $this->shouldSeeTheLoginTitle();
        $this->shouldSeeTheLoginUsernameTitle();
        $this->shouldSeeTheLoginUsernameField();
        $this->shouldSeeTheLoginPasswordTitle();
        $this->shouldSeeTheLoginPasswordField();
        $this->shouldSeeTheLoginForgotPasswordLink();
        $this->shouldSeeTheLoginSignInButton();
        $this->shouldSeeTheLoginCopyrightText();
    }

    public function enterTheEmailAddress($emailAddress)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$emailAddress, $emailAddress);
    }

    public function clickOnRetrievePassword()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$retrievePassword);
    }

    public function clickOnBackToSignIn()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$backToSignIn);
    }

    public function shouldSeeTheForgotYourPasswordFields()
    {
        $I = $this->acceptanceTester;
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
