<?php

/**
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class LoginOnAdminLoginPageCest
{
    public function shouldBeAbleToLogin(\Step\Acceptance\Admin $I)
    {
        $I->wantTo('verify that I can login via the Admin Login page');

        $I->goToTheAdminLoginPage();
        $I->loginAsAnExistingAdmin();
        $I->shouldBeOnTheAdminDashboardPage();
    }
}
