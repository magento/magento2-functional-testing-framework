<?php
namespace Magento\Xxyyzz\Acceptance\User;

use Magento\Xxyyzz\Step\Backend\Admin;

/**
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class LoginOnAdminLoginPageCest
{
    public function shouldBeAbleToLogin(Admin $I)
    {
        $I->wantTo('verify that I can login via the Admin Login page');

        $I->goToTheAdminLoginPage();
        $I->loginAsAdmin();
        $I->shouldBeOnTheAdminDashboardPage();
    }
}
