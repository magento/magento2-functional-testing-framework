<?php

use Page\Acceptance\AdminLogin as AdminLogin;

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
