<?php
namespace Magento\Xxyyzz\Acceptance\User;

use Magento\Xxyyzz\Step\Backend\Admin;

/**
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class LogoutAfterLoginCest
{
    public function shouldBeAbleToLogout(Admin $I)
    {
        $I->wantTo('logout of the Admin area and land on the Login page');

        $I->goToTheAdminLoginPage();
        $I->loginAsAdmin();
        $I->goToTheAdminLogoutPage();
        $I->shouldBeOnTheAdminLoginPage();
    }
}
