<?php

use Page\Acceptance\SideNavigation as SideNav;

/**
 * @group skip
 */
class OpenEachSideNavMenuCest
{
    public function _before(\Step\Acceptance\Admin $I)
    {
        $I->goToTheAdminLoginPage();
        $I->loginAsAnExistingAdmin();
    }

    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldBeAbleToOpenEachSideNavMenu(\Step\Acceptance\Admin $I, SideNav $sideNavMenu)
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
