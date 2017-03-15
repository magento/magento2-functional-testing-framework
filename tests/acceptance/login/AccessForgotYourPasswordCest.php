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
        $adminLogin->iClickOnForgotYourPassword($I);
        $I->shouldBeOnTheForgotYourPasswordPage();
        $adminLogin->iShouldSeeTheForgotYourPasswordFields($I);
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
        $adminLogin->iClickOnForgotYourPassword($I);
        $adminLogin->iClickOnBackToSignIn($I);
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
        $adminLogin->iClickOnMagentoLogo($I);
        $I->shouldBeOnTheAdminLoginPage();

        $adminLogin->iClickOnForgotYourPassword($I);
        $adminLogin->iClickOnMagentoLogo($I);
        $I->shouldBeOnTheAdminLoginPage();
    }
}
