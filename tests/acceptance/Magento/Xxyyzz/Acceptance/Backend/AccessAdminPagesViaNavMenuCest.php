<?php
namespace Magento\Xxyyzz\Acceptance\Backend;

use Magento\Xxyyzz\Page\Backend\AdminSideNavigation as SideNav;
use Magento\Xxyyzz\Step\Backend\AdminStep;

/**
 * @group skip
 */
class AccessAdminPagesViaNavMenuCest
{
    public function _before(AdminStep $I)
    {
        $I->loginAsAdmin();
    }

    // Dashboard Menu Test
    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnTheDashboardPage(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access the DASHBOARD Page using the Side Nav Menus');
        $I->goToRandomAdminPage();
        $sideNavMenu->clickOnDashboardInTheSideNavMenu();
        $I->shouldBeOnTheAdminDashboardPage();
    }

    // Sales Menu Tests
    /**
     * @env chrome
     * @env firefox
     * @group slow
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

    // Products Menu Tests
    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnEachOfTheProductsPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the PRODUCTS Admin Pages using the Side Nav Menus');
        $sideNavMenu->clickOnProductsInTheSideNavMenu();
        $sideNavMenu->clickOnCatalogInTheProductNavMenu();
        $I->shouldBeOnTheAdminProductsCatalogPage();

        $sideNavMenu->clickOnProductsInTheSideNavMenu();
        $sideNavMenu->clickOnCategoriesInTheProductNavMenu();
        $I->shouldBeOnTheAdminProductsCategoriesPage();
    }

    // Customers Menu Tests
    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLanOnEachOfTheCustomersPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the CUSTOMERS Admin Pages using the Side Nav Menus');
        $sideNavMenu->clickOnCustomersInTheSideNavMenu();
        $sideNavMenu->clickOnAllCustomersInTheCustomersNavMenu();
        $I->shouldBeOnTheAdminCustomersAllCustomersPage();

        $sideNavMenu->clickOnCustomersInTheSideNavMenu();
        $sideNavMenu->clickOnNowOnlineInTheCustomersNavMenu();
        $I->shouldBeOnTheAdminCustomersNowOnlinePage();
    }

    // Marketing Menu Tests
    /**
     * @env chrome
     * @env firefox
     * @group slow
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
     * @env chrome
     * @env firefox
     * @group slow
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

    // Reports Menu Tests
    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnEachOfTheReportsPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the REPORTS Admin Pages using the Side Nav Menu');
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

    // Stores Menu Tests
    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnEachOfTheStoresPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the STORES Admin Pages using the Side Nav Menu');
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
     * @env chrome
     * @env firefox
     * @group slow
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
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnTheWebSetupWizardPage(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access the Web Setup Wizard Admin Page using the Side Nav Menu');
        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnWebSetupWizardInTheSystemNavMenu();
        $I->shouldBeOnTheAdminSystemWebSetupWizardPage();
        $I->goToTheAdminLogoutPage();
    }

    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnThePartnersAndExtensionsPage(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access the Partners and Extensions Admin Page using the Side Nav Menu');
        $sideNavMenu->clickOnFindPartnersAndExtensionsInTheSideNavMenu();
        $I->shouldBeOnTheAdminFindPartnersAndExtensionsPage();
    }
}
