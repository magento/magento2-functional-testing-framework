<?php

/**
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class LogoutAfterLoginCest
{
    public function shouldBeAbleToLogout(\Step\Acceptance\Admin $I)
    {
        $I->wantTo('logout of the Admin area and land on the Login page');

        $I->goToTheAdminLoginPage();
        $I->loginAsAnExistingAdmin();
        $I->goToTheAdminLogoutPage();
        $I->shouldBeOnTheAdminLoginPage();
    }
}
