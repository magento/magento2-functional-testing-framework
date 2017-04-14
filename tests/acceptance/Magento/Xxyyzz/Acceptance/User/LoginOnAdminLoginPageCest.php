<?php
namespace Magento\Xxyyzz\Acceptance\User;

use Magento\Xxyyzz\Step\Backend\AdminStep;

/**
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class LoginOnAdminLoginPageCest
{
    public function shouldBeAbleToLogin(AdminStep $I)
    {
        $I->wantTo('verify that I can login via the Admin Login page');
        
        $I->loginAsAdmin();
        $I->shouldBeOnTheAdminDashboardPage();
    }
}
