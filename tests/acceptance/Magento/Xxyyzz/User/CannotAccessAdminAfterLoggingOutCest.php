<?php
namespace Magento\Xxyyzz\User;

use Magento\Xxyyzz\Step\Backend\Admin;

/**
 * @env chrome
 * @env firefox
 * @env phantomjs
 * @group smoke
 */
class CannotAccessAdminAfterLoggingOutCest
{
    public function shouldNotBeAbleToAccessAdminAfterLogout(Admin $I)
    {
        $I->wantTo('make sure you cannot access Admin pages after logging out');

        $I->goToTheAdminLoginPage();
        $I->loginAsAdmin();
        $I->goToTheAdminLogoutPage();

        $I->goToRandomAdminPage();
        $I->shouldBeOnTheAdminLoginPage();

        $I->goToRandomAdminPage();
        $I->shouldBeOnTheAdminLoginPage();

        $I->goToRandomAdminPage();
        $I->shouldBeOnTheAdminLoginPage();
    }
}
