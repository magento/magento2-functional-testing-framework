<?php
namespace Magento\Xxyyzz\Acceptance\Backend;

use Magento\Xxyyzz\Page\Backend\AdminSideNavigation as SideNav;
use Magento\Xxyyzz\Step\Backend\AdminStep;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Model\SeverityLevel;
use Yandex\Allure\Adapter\Annotation\TestCaseId;

/**
 * Class OpenEachSideNavMenuCest
 *
 * Allure annotations
 * @Features({"Admin Nav Menu"})
 * @Stories({"Open each Admin Nav Menu"})
 *
 * Codeception annotations
 * @group skip
 * @group slow
 * @group nav-menu
 * @env chrome
 * @env firefox
 */
class OpenEachSideNavMenuCest
{
    public function _before(AdminStep $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * Allure annotations
     * @Title("Open each Admin Nav Menu")
     * @Description("Attempt to open each of the Admin Nav Menus and verify all of the proper menus are displayed.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param SideNav $sideNavMenu
     * @return void
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
