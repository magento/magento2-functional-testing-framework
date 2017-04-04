<?php
namespace Magento\Xxyyzz\Acceptance\User;

use Magento\Xxyyzz\Step\Backend\AdminStep;

/**
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class CannotAccessAdminPagesCest
{
    public function shouldNotBeAbleToAccessAdminPagesWhenNotLoggedIn(AdminStep $I)
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
