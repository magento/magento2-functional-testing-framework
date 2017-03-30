<?php
namespace Magento\Xxyyzz\Acceptance\Backend;

use Magento\Xxyyzz\Page\Backend\Admin\SideNavigation as SideNav;
use Magento\Xxyyzz\Step\Backend\Admin;

/**
 * @group skip
 */
class OpenEachSideNavMenuCest
{
    public function _before(Admin $I)
    {
        $I->goToTheAdminLoginPage();
        $I->loginAsAdmin();
    }

    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldBeAbleToOpenEachSideNavMenu(Admin $I, SideNav $sideNavMenu)
    {
        $sideNavMenu->clickOnSalesInTheSideNavMenu($I);
        $sideNavMenu->shouldSeeTheSalesNavMenu($I);

        $sideNavMenu->clickOnProductsInTheSideNavMenu($I);
        $sideNavMenu->shouldSeeTheProductNavMenu($I);

        $sideNavMenu->clickOnCustomersInTheSideNavMenu($I);
        $sideNavMenu->shouldSeeTheCustomersNavMenu($I);

        $sideNavMenu->clickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->shouldSeeTheMarketingNavMenu($I);

        $sideNavMenu->clickOnContentInTheSideNavMenu($I);
        $sideNavMenu->shouldSeeTheContentNavMenu($I);

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->shouldSeeTheReportsNavMenu($I);

        $sideNavMenu->clickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->shouldSeeTheStoresNavMenu($I);

        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->shouldSeeTheSystemNavMenu($I);
    }
}
