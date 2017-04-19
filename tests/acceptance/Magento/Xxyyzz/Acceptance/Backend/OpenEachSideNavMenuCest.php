<?php
namespace Magento\Xxyyzz\Acceptance\Backend;

use Magento\Xxyyzz\Page\Backend\SideNavigation as SideNav;
use Magento\Xxyyzz\Step\Backend\AdminStep;

/**
 * @group skip
 */
class OpenEachSideNavMenuCest
{
    public function _before(AdminStep $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldBeAbleToOpenEachSideNavMenu(SideNav $sideNavMenu)
    {
        $sideNavMenu->clickOnSalesInTheSideNavMenu();
        $sideNavMenu->shouldSeeTheSalesNavMenu();

        $sideNavMenu->clickOnProductsInTheSideNavMenu();
        $sideNavMenu->shouldSeeTheProductNavMenu();

        $sideNavMenu->clickOnCustomersInTheSideNavMenu();
        $sideNavMenu->shouldSeeTheCustomersNavMenu();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->shouldSeeTheMarketingNavMenu();

        $sideNavMenu->clickOnContentInTheSideNavMenu();
        $sideNavMenu->shouldSeeTheContentNavMenu();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->shouldSeeTheReportsNavMenu();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->shouldSeeTheStoresNavMenu();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->shouldSeeTheSystemNavMenu();
    }
}
