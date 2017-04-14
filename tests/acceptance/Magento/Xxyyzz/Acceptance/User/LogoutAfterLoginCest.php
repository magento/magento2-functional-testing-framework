<?php
namespace Magento\Xxyyzz\Acceptance\User;

use Magento\Xxyyzz\Step\Backend\AdminStep;

/**
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class LogoutAfterLoginCest
{
    public function shouldBeAbleToLogout(AdminStep $I)
    {
        $I->wantTo('logout of the Admin area and land on the Login page');
        
        $I->loginAsAdmin();
        $I->goToTheAdminLogoutPage();
        $I->shouldBeOnTheAdminLoginPage();
    }
}
