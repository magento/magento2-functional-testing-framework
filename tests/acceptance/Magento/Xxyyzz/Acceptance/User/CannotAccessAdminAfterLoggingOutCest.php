<?php
namespace Magento\Xxyyzz\Acceptance\User;

use Magento\Xxyyzz\Step\Backend\AdminStep;

/**
 * @env chrome
 * @env firefox
 * @env phantomjs
 * @group smoke
 */
class CannotAccessAdminAfterLoggingOutCest
{
    public function shouldNotBeAbleToAccessAdminAfterLogout(AdminStep $I)
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
