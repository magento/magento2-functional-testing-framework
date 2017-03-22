<?php

/**
 * @env chrome
 * @env firefox
 * @env phantomjs
 * @group smoke
 */
class CannotAccessAdminAfterLoggingOutCest
{
    public function shouldNotBeAbleToAccessAdminAfterLogout(\Step\Acceptance\Admin $I)
    {
        $I->wantTo('make sure you cannot access Admin pages after logging out');

        $I->goToTheAdminLoginPage();
        $I->loginAsAnExistingAdmin();
        $I->goToTheAdminLogoutPage();

        $I->goToRandomAdminPage();
        $I->shouldBeOnTheAdminLoginPage();

        $I->goToRandomAdminPage();
        $I->shouldBeOnTheAdminLoginPage();

        $I->goToRandomAdminPage();
        $I->shouldBeOnTheAdminLoginPage();
    }
}
