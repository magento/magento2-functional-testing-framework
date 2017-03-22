<?php

use Page\Acceptance\AdminLogin as AdminLogin;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Annotation\TestCaseId;

/**
 * @Features("Admin Login")
 * @Stories("Forgot Your Password")
 * @TestCaseId("ZEP-1")
 * @group test
 */
class AccessForgotYourPasswordCest
{
    public function _before(\Step\Acceptance\Admin $I)
    {
        $I->goToTheAdminLoginPage();
    }

    /**
     * @env chrome
     * @env firefox
     * @env phantomjs
     * @Title("You should land on the Forgot Your Password page.")
     * @Severity("critical")
     */
    public function shouldLandOnTheForgotYourPasswordPage(\Step\Acceptance\Admin $I, AdminLogin $adminLogin)
    {
        $I->wantTo('see if I can access the Forgot Your Password page');
        $adminLogin->clickOnForgotYourPassword($I);
        $I->shouldBeOnTheForgotYourPasswordPage();
        $adminLogin->shouldSeeTheForgotYourPasswordFields($I);
        $I->see('Password Help');
    }

    /**
     * @env chrome
     * @env firefox
     * @env phantomjs
     * @Title("You should land on the Login page after clicking on 'Back to Sign In'.")
     * @Severity("trivial")
     */
    public function shouldLandOnTheLoginPageWhenBackToSignInIsClicked(\Step\Acceptance\Admin $I, AdminLogin $adminLogin)
    {
        $I->wantTo('see if I can access the Login page from the Forgot Your Password page');
        $adminLogin->clickOnForgotYourPassword($I);
        $adminLogin->clickOnBackToSignIn($I);
        $I->shouldBeOnTheAdminLoginPage();
    }

    /**
     * @env chrome
     * @env firefox
     * @env phantomjs
     * @Title("You should land on the Login page after clicking on the Logo.")
     * @Severity("normal")
     */
    public function shouldLandOnTheLoginPageWhenTheLogoIsClicked(\Step\Acceptance\Admin $I, AdminLogin $adminLogin)
    {
        $I->wantTo('see if I can access the Login page by clicking on the Logo');
        $adminLogin->clickOnMagentoLogo($I);
        $I->shouldBeOnTheAdminLoginPage();

        $adminLogin->clickOnForgotYourPassword($I);
        $adminLogin->clickOnMagentoLogo($I);
        $I->shouldBeOnTheAdminLoginPage();
    }
}
