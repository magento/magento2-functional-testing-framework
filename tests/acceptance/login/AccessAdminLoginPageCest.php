<?php

/**
 * @env chrome
 * @env firefox
 * @env phantomjs
 * @group smoke
 */
class AccessAdminLoginPageCest
{
    public function shouldBeAbleToAccessTheAdminLoginPage(\Step\Acceptance\Admin $I)
    {
        $I->am('an Admin');
        $I->wantTo('verify that I can access the Admin Login page');

        $I->goToTheAdminLoginPage();
        $I->shouldBeOnTheAdminLoginPage();
    }
}
