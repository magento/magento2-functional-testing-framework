<?php

/**
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class CannotAccessAdminPagesCest
{
    public function shouldNotBeAbleToAccessAdminPagesWhenNotLoggedIn(\Step\Acceptance\Admin $I)
    {
        $I->wantTo('make sure you cannot access Admin pages when NOT logged in');
        $I->goToTheAdminLoginPage();

        $I->goToRandomAdminPage();
        $I->shouldBeOnTheAdminLoginPage();

        $I->goToRandomAdminPage();
        $I->shouldBeOnTheAdminLoginPage();

        $I->goToRandomAdminPage();
        $I->shouldBeOnTheAdminLoginPage();

        $I->goToRandomAdminPage();
        $I->shouldBeOnTheAdminLoginPage();

        $I->goToRandomAdminPage();
        $I->shouldBeOnTheAdminLoginPage();
    }
}
