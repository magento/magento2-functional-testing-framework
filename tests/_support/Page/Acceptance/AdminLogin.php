<?php
namespace Page\Acceptance;

class AdminLogin
{
    // include url of current page
    public static $URL = '/admin/admin/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
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

    public function iClickOnMagentoLogo(\AcceptanceTester $I) {
        $I->click(self::$logoImage);
    }

    public function iClickOnForgotYourPassword(\AcceptanceTester $I) {
        $I->click(self::$forgotYourPassword);
    }

    public function iClickOnSignIn(\AcceptanceTester $I) {
        $I->click(self::$signIn);
    }

    public function iEnterTheUsername(\AcceptanceTester $I, $username) {
        $I->fillField(self::$username, $username);
    }

    public function iEnterThePassword(\AcceptanceTester $I, $password) {
        $I->fillField(self::$password, $password);
    }

    public function iEnterTheLoginCredentials(\AcceptanceTester $I, $username, $password) {
        $I->fillField(self::$username, $username);
        $I->fillField(self::$password, $password);
    }

    public function iShouldSeeTheLoginPageFields(\AcceptanceTester $I) {
        $I->seeElement(self::$mainArea);
        $I->seeElement(self::$logoLink);
        $I->seeElement(self::$logoImage);
        $I->seeElement(self::$title);
        $I->seeElement(self::$usernameTitle);
        $I->seeElement(self::$username);
        $I->seeElement(self::$passwordTitle);
        $I->seeElement(self::$password);
        $I->seeElement(self::$forgotYourPassword);
        $I->seeElement(self::$signIn);
        $I->seeElement(self::$copyRight);
    }

    public function iEnterTheEmailAddress(\AcceptanceTester $I, $emailAddress) {
        $I->fillField(self::$emailAddress, $emailAddress);
    }

    public function iClickOnRetrievePassword(\AcceptanceTester $I) {
        $I->click(self::$retrievePassword);
    }

    public function iClickOnBackToSignIn(\AcceptanceTester $I) {
        $I->click(self::$backToSignIn);
    }

    public function iShouldSeeTheForgotYourPasswordFields(\AcceptanceTester $I) {
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
