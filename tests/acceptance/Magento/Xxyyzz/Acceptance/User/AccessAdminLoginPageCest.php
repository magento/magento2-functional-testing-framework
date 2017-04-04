<?php
namespace Magento\Xxyyzz\Acceptance\User;

use Magento\Xxyyzz\Step\Backend\AdminStep;

/**
 * @env chrome
 * @env firefox
 * @env phantomjs
 * @group smoke
 */
class AccessAdminLoginPageCest
{
    public function shouldBeAbleToAccessTheAdminLoginPage(AdminStep $I)
    {
        $I->am('an Admin');
        $I->wantTo('verify that I can access the Admin Login page');

        $I->goToTheAdminLoginPage();
        $I->shouldBeOnTheAdminLoginPage();
        $I->lookForwardTo('being on the Admin Login page');
    }
}
