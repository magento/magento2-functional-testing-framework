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
 * Class AccessAdminPagesViaNavMenuCest
 * 
 * Allure annotations
 * @Features({"Admin Nav Menu"})
 * @Stories({"Access Admin pages via the Admin Nav Menu"})
 *
 * Codeception annotations
 * @group skip
 * @group slow
 * @group nav-menu-access
 * @env chrome
 * @env firefox
 */
class AccessAdminPagesViaNavMenuCest
{
    public function _before(AdminStep $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * Allure annotations
     * @Title("DASHBOARD Menu Test")
     * @Description("Attempt to access the DASHBOARD via the Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnTheDashboardPage(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access the DASHBOARD Page using the Side Nav Menu.');
        $I->goToRandomAdminPage();
        $sideNavMenu->clickOnDashboardInTheSideNavMenu();
        $I->shouldBeOnTheAdminDashboardPage();
    }

    /**
     * Allure annotations
     * @Title("SALES Menu Tests")
     * @Description("Attempt to access all of the SALES pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnEachOfTheSalesPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the SALES Admin Pages using the Side Nav Menus');
        $sideNavMenu->clickOnSalesInTheSideNavMenu();
        $sideNavMenu->clickOnOrdersInTheSalesNavMenu();
        $I->shouldBeOnTheAdminSalesOrdersPage();

        $sideNavMenu->clickOnSalesInTheSideNavMenu();
        $sideNavMenu->clickOnInvoicesInTheSalesNavMenu();
        $I->shouldBeOnTheAdminSalesInvoicesPage();

        $sideNavMenu->clickOnSalesInTheSideNavMenu();
        $sideNavMenu->clickOnShipmentsInTheSalesNavMenu();
        $I->shouldBeOnTheAdminSalesShipmentsPage();

        $sideNavMenu->clickOnSalesInTheSideNavMenu();
        $sideNavMenu->clickOnCreditMemosInTheSalesNavMenu();
        $I->shouldBeOnTheAdminSalesCreditMemosPage();

        $sideNavMenu->clickOnSalesInTheSideNavMenu();
        $sideNavMenu->clickOnBillingAgreementsInTheSalesNavMenu();
        $I->shouldBeOnTheAdminSalesBillingAgreementsPage();

        $sideNavMenu->clickOnSalesInTheSideNavMenu();
        $sideNavMenu->clickOnTransactionsInTheSalesNavMenu();
        $I->shouldBeOnTheAdminSalesTransactionsPage();
    }

    /**
     * Allure annotations
     * @Title("PRODUCT Menu Tests")
     * @Description("Attempt to access all of the PRODUCT pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnEachOfTheProductsPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the PRODUCT Admin Pages using the Side Nav Menus');
        $sideNavMenu->clickOnProductsInTheSideNavMenu();
        $sideNavMenu->clickOnCatalogInTheProductNavMenu();
        $I->shouldBeOnTheAdminProductsCatalogPage();

        $sideNavMenu->clickOnProductsInTheSideNavMenu();
        $sideNavMenu->clickOnCategoriesInTheProductNavMenu();
        $I->shouldBeOnTheAdminProductsCategoriesPage();
    }

    /**
     * Allure annotations
     * @Title("CUSTOMER Menu Tests")
     * @Description("Attempt to access all of the CUSTOMER pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLanOnEachOfTheCustomersPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the CUSTOMER Admin Pages using the Side Nav Menus');
        $sideNavMenu->clickOnCustomersInTheSideNavMenu();
        $sideNavMenu->clickOnAllCustomersInTheCustomersNavMenu();
        $I->shouldBeOnTheAdminCustomersAllCustomersPage();

        $sideNavMenu->clickOnCustomersInTheSideNavMenu();
        $sideNavMenu->clickOnNowOnlineInTheCustomersNavMenu();
        $I->shouldBeOnTheAdminCustomersNowOnlinePage();
    }

    // Marketing Menu Tests
    /**
     * Allure annotations
     * @Title("MARKETING Menu Tests")
     * @Description("Attempt to access all of the MARKETING pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnEachOfTheMarketingPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the MARKETING Admin Pages using the Side Nav Menus');
        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnCatalogPriceRulesInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminMarketingCatalogPriceRulePage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnCartPriceRulesInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminMarketingCartPriceRulePage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnEmailTemplatesInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminMarketingEmailTemplatesPage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnNewsletterTemplatesInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminMarketingNewsletterTemplatePage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnNewsletterQueueInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminMarketingNewsletterQueuePage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnNewsletterSubscribersInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminMarketingNewsletterSubscribersPage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnURLRewritesInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminMarketingURLRewritesPage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnSearchTermsInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminMarketingSearchTermsPage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnSearchSynonymsInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminMarketingSearchSynonymsPage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnSiteMapInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminMarketingSiteMapPage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnContentReviewsInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminMarketingReviewsPage();
    }

    // Content Menu Tests
    /**
     * Allure annotations
     * @Title("CONTENT Menu Tests")
     * @Description("Attempt to access all of the CONTENT pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnEachOfTheContentPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the CONTENT Admin Pages using the Side Nav Menus');
        $sideNavMenu->clickOnContentInTheSideNavMenu();
        $sideNavMenu->clickOnPagesInTheContentNavMenu();
        $I->shouldBeOnTheAdminContentPagesPage();

        $sideNavMenu->clickOnContentInTheSideNavMenu();
        $sideNavMenu->clickOnBlocksInTheContentNavMenu();
        $I->shouldBeOnTheAdminContentBlocksPage();

        $sideNavMenu->clickOnContentInTheSideNavMenu();
        $sideNavMenu->clickOnWidgetsInTheContentNavMenu();
        $I->shouldBeOnTheAdminContentWidgetsPage();

        $sideNavMenu->clickOnContentInTheSideNavMenu();
        $sideNavMenu->clickOnConfigurationInTheContentNavMenu();
        $I->shouldBeOnTheAdminContentConfigurationPage();

        $sideNavMenu->clickOnContentInTheSideNavMenu();
        $sideNavMenu->clickOnThemesInTheContentNavMenu();
        $I->shouldBeOnTheAdminContentThemesPage();

        $sideNavMenu->clickOnContentInTheSideNavMenu();
        $sideNavMenu->clickOnScheduleInTheContentNavMenu();
        $I->shouldBeOnTheAdminContentSchedulePage();
    }

    /**
     * Allure annotations
     * @Title("REPORT Menu Tests")
     * @Description("Attempt to access all of the REPORT pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnEachOfTheReportsPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the REPORT Admin Pages using the Side Nav Menu');
        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnProductsInCartInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsProductsInCartPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnSearchTermsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsSearchTermsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnAbandonedCartsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsAbandonedCartsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnNewsletterProblemReportsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsNewsletterProblemReportsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnByCustomersInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsByCustomersPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnByProductsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsByProductsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnOrdersInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsOrdersPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOTaxInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsTaxPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnInvoicedInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsInvoicedPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnShippingInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsShippingPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnRefundsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsRefundsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnCouponsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsCouponsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnPayPalSettlementInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsPayPalSettlementPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnBraintreeSettlementInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsBraintreeSettlementPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnOrderTotalInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsOrderTotalPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnOrderCountInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsOrderCountPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnNewInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsNewPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnViewsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsViewsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnBestSellersInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsBestsellersPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnLowStockInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsLowStockPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnOrderedInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsOrderedPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnDownloadsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportsDownloadsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnRefreshStatisticsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminReportRefreshStatisticsPage();
    }

    /**
     * Allure annotations
     * @Title("STORE Menu Tests")
     * @Description("Attempt to access all of the STORE pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnEachOfTheStoresPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the STORE Admin Pages using the Side Nav Menu');
        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnAllStoresInTheStoresNavMenu();
        $I->shouldBeOnTheAdminStoresAllStoresPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnConfigurationInTheStoresNavMenu();
        $I->shouldBeOnTheAdminStoresConfigurationPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnTermsAndConditionsInTheStoresNavMenu();
        $I->shouldBeOnTheAdminStoresTermsAndConditionsPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnOrderStatusInTheStoresNavMenu();
        $I->shouldBeOnTheAdminStoresOrderStatusPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnTaxRuleInTheStoresNavMenu();
        $I->shouldBeOnTheAdminStoresTaxRulesPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnTaxZonesAndRatesInTheStoresNavMenu();
        $I->shouldBeOnTheAdminStoresTaxZonesAndRatesPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnCurrencyRatesInTheStoresNavMenu();
        $I->shouldBeOnTheAdminStoresCurrencyRatesPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnCurrencySymbolsInTheStoresNavMenu();
        $I->shouldBeOnTheAdminStoresCurrencySymbolsPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnProductInTheStoresNavMenu();
        $I->shouldBeOnTheAdminStoresProductPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnAttributesSetInTheStoresNavMenu();
        $I->shouldBeOnTheAdminStoresAttributeSetPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnRatingsInTheStoresNavMenu();
        $I->shouldBeOnTheAdminStoresRatingPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnCustomerGroupInTheStoresNavMenu();
        $I->shouldBeOnTheAdminStoresCustomerGroupsPage();
    }

    // System Menu Tests
    /**
     * Allure annotations
     * @Title("SYSTEM Menu Tests")
     * @Description("Attempt to access all of the SYSTEM pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnEachOfTheSystemPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the SYSTEM Admin Pages using the Side Nav Menu');
        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnImportInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemImportPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnExportInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemExportPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnImportExportTaxRatesInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemImportExportTaxRatesPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnImportHistoryInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemImportHistoryPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnIntegrationsInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemIntegrationsPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnCacheManagementInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemCacheManagementPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnBackupsInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemBackupsPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnIndexManagementInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemIndexManagementPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnAllUsersInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemAllUsersPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnLockedUsersInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemLockedUsersPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnUserRolesInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemUserRolesPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnNotificationsInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemNotificationsPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnCustomVariablesInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemCustomVariablesPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnManageEncryptionKeyInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemManageEncryptionKeyPage();
    }

    /**
     * Allure annotations
     * @Title("WEB SETUP WIZARD Menu Test")
     * @Description("Attempt to access all of the WEB SETUP WIZARD page using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnTheWebSetupWizardPage(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access the WEB SETUP WIZARD Admin Page using the Side Nav Menu');
        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnWebSetupWizardInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemWebSetupWizardPage();
        $I->goToTheAdminLogoutPage();
    }

    /**
     * Allure annotations
     * @Title("PARTNERS & EXTENSIONS Menu Test")
     * @Description("Attempt to access all of the PARTNERS & EXTENSIONS page using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnThePartnersAndExtensionsPage(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access the Partners and Extensions Admin Page using the Side Nav Menu');
        $sideNavMenu->clickOnFindPartnersAndExtensionsInTheSideNavMenu();
        $I->shouldBeOnTheAdminFindPartnersAndExtensionsPage();
    }
}
