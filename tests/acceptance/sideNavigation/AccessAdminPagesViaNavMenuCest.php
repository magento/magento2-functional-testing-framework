<?php

use Page\Acceptance\SideNavigation as SideNav;

/**
 * @group skip
 */
class AccessAdminPagesViaNavMenuCest
{
    public function _before(\Step\Acceptance\Admin $I)
    {
        $I->goToTheAdminLoginPage();
        $I->loginAsAnExistingAdmin();
    }

    // Dashboard Menu Test
    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnTheDashboardPage(\Step\Acceptance\Admin $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access the DASHBOARD Page using the Side Nav Menus');
        $I->goToRandomAdminPage();
        $sideNavMenu->iClickOnDashboardInTheSideNavMenu($I);
        $I->shouldBeOnTheAdminDashboardPage();
    }

    // Sales Menu Tests
    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnEachOfTheSalesPages(\Step\Acceptance\Admin $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the SALES Admin Pages using the Side Nav Menus');
        $sideNavMenu->iClickOnSalesInTheSideNavMenu($I);
        $sideNavMenu->iClickOnOrdersInTheSalesNavMenu($I);
        $I->shouldBeOnTheAdminSalesOrdersPage();

        $sideNavMenu->iClickOnSalesInTheSideNavMenu($I);
        $sideNavMenu->iClickOnInvoicesInTheSalesNavMenu($I);
        $I->shouldBeOnTheAdminSalesInvoicesPage();

        $sideNavMenu->iClickOnSalesInTheSideNavMenu($I);
        $sideNavMenu->iClickOnShipmentsInTheSalesNavMenu($I);
        $I->shouldBeOnTheAdminSalesShipmentsPage();

        $sideNavMenu->iClickOnSalesInTheSideNavMenu($I);
        $sideNavMenu->iClickOnCreditMemosInTheSalesNavMenu($I);
        $I->shouldBeOnTheAdminSalesCreditMemosPage();

        $sideNavMenu->iClickOnSalesInTheSideNavMenu($I);
        $sideNavMenu->iClickOnBillingAgreementsInTheSalesNavMenu($I);
        $I->shouldBeOnTheAdminSalesBillingAgreementsPage();

        $sideNavMenu->iClickOnSalesInTheSideNavMenu($I);
        $sideNavMenu->iClickOnTransactionsInTheSalesNavMenu($I);
        $I->shouldBeOnTheAdminSalesTransactionsPage();
    }

    // Products Menu Tests
    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnEachOfTheProductsPages(\Step\Acceptance\Admin $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the PRODUCTS Admin Pages using the Side Nav Menus');
        $sideNavMenu->iClickOnProductsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnCatalogInTheProductNavMenu($I);
        $I->shouldBeOnTheAdminProductsCatalogPage();

        $sideNavMenu->iClickOnProductsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnCategoriesInTheProductNavMenu($I);
        $I->shouldBeOnTheAdminProductsCategoriesPage();
    }

    // Customers Menu Tests
    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLanOnEachOfTheCustomersPages(\Step\Acceptance\Admin $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the CUSTOMERS Admin Pages using the Side Nav Menus');
        $sideNavMenu->iClickOnCustomersInTheSideNavMenu($I);
        $sideNavMenu->iClickOnAllCustomersInTheCustomersNavMenu($I);
        $I->shouldBeOnTheAdminCustomersAllCustomersPage();

        $sideNavMenu->iClickOnCustomersInTheSideNavMenu($I);
        $sideNavMenu->iClickOnNowOnlineInTheCustomersNavMenu($I);
        $I->shouldBeOnTheAdminCustomersNowOnlinePage();
    }

    // Marketing Menu Tests
    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnEachOfTheMarketingPages(\Step\Acceptance\Admin $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the MARKETING Admin Pages using the Side Nav Menus');
        $sideNavMenu->iClickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->iClickOnCatalogPriceRulesInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingCatalogPriceRulePage();

        $sideNavMenu->iClickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->iClickOnCartPriceRulesInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingCartPriceRulePage();

        $sideNavMenu->iClickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->iClickOnEmailTemplatesInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingEmailTemplatesPage();

        $sideNavMenu->iClickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->iClickOnNewsletterTemplatesInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingNewsletterTemplatePage();

        $sideNavMenu->iClickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->iClickOnNewsletterQueueInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingNewsletterQueuePage();

        $sideNavMenu->iClickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->iClickOnNewsletterSubscribersInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingNewsletterSubscribersPage();

        $sideNavMenu->iClickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->iClickOnURLRewritesInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingURLRewritesPage();

        $sideNavMenu->iClickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->iClickOnSearchTermsInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingSearchTermsPage();

        $sideNavMenu->iClickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->iClickOnSearchSynonymsInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingSearchSynonymsPage();

        $sideNavMenu->iClickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->iClickOnSiteMapInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingSiteMapPage();

        $sideNavMenu->iClickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->iClickOnContentReviewsInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingReviewsPage();
    }

    // Content Menu Tests
    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnEachOfTheContentPages(\Step\Acceptance\Admin $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the CONTENT Admin Pages using the Side Nav Menus');
        $sideNavMenu->iClickOnContentInTheSideNavMenu($I);
        $sideNavMenu->iClickOnPagesInTheContentNavMenu($I);
        $I->shouldBeOnTheAdminContentPagesPage();

        $sideNavMenu->iClickOnContentInTheSideNavMenu($I);
        $sideNavMenu->iClickOnBlocksInTheContentNavMenu($I);
        $I->shouldBeOnTheAdminContentBlocksPage();

        $sideNavMenu->iClickOnContentInTheSideNavMenu($I);
        $sideNavMenu->iClickOnWidgetsInTheContentNavMenu($I);
        $I->shouldBeOnTheAdminContentWidgetsPage();

        $sideNavMenu->iClickOnContentInTheSideNavMenu($I);
        $sideNavMenu->iClickOnConfigurationInTheContentNavMenu($I);
        $I->shouldBeOnTheAdminContentConfigurationPage();

        $sideNavMenu->iClickOnContentInTheSideNavMenu($I);
        $sideNavMenu->iClickOnThemesInTheContentNavMenu($I);
        $I->shouldBeOnTheAdminContentThemesPage();

        $sideNavMenu->iClickOnContentInTheSideNavMenu($I);
        $sideNavMenu->iClickOnScheduleInTheContentNavMenu($I);
        $I->shouldBeOnTheAdminContentSchedulePage();
    }

    // Reports Menu Tests
    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnEachOfTheReportsPages(\Step\Acceptance\Admin $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the REPORTS Admin Pages using the Side Nav Menu');
        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnProductsInCartInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsProductsInCartPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnSearchTermsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsSearchTermsPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnAbandonedCartsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsAbandonedCartsPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnNewsletterProblemReportsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsNewsletterProblemReportsPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnByCustomersInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsByCustomersPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnByProductsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsByProductsPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnOrdersInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsOrdersPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOTaxnInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsTaxPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnInvoicedInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsInvoicedPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnShippingInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsShippingPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnRefundsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsRefundsPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnCouponsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsCouponsPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnPaypalSettlementInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsPayPalSettlementPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnBraintreeSettlementInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsBraintreeSettlementPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnOrderTotalInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsOrderTotalPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnOrderCountInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsOrderCountPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnNewInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsNewPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnViewsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsViewsPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnBestSellersInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsBestsellersPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnLowStockInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsLowStockPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnOrderedInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsOrderedPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnDownloadsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsDownloadsPage();

        $sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->iClickOnRefreshStatisticsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportRefreshStatisticsPage();
    }

    // Stores Menu Tests
    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnEachOfTheStoresPages(\Step\Acceptance\Admin $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the STORES Admin Pages using the Side Nav Menu');
        $sideNavMenu->iClickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->iClickOnAllStoresInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresAllStoresPage();

        $sideNavMenu->iClickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->iClickOnConfigurationInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresConfigurationPage();

        $sideNavMenu->iClickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->iClickOnTermsAndConditionsInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresTermsAndConditionsPage();

        $sideNavMenu->iClickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->iClickOnOrderStatusInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresOrderStatusPage();

        $sideNavMenu->iClickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->iClickOnTaxRuleInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresTaxRulesPage();

        $sideNavMenu->iClickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->iClickOnTaxZonesAndRatesInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresTaxZonesAndRatesPage();

        $sideNavMenu->iClickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->iClickOnCurrencyRatesInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresCurrencyRatesPage();

        $sideNavMenu->iClickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->iClickOnCurrencySymbolsInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresCurrencySymbolsPage();

        $sideNavMenu->iClickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->iClickOnProductInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresProductPage();

        $sideNavMenu->iClickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->iClickOnAttributesSetInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresAttributeSetPage();

        $sideNavMenu->iClickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->iClickOnRatingsInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresRatingPage();

        $sideNavMenu->iClickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->iClickOnCustomerGroupInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresCustomerGroupsPage();
    }

    // System Menu Tests
    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnEachOfTheSystemPages(\Step\Acceptance\Admin $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the SYSTEM Admin Pages using the Side Nav Menu');
        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnImportInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemImportPage();

        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnExportInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemExportPage();

        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnImportExportTaxRatesInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemImportExportTaxRatesPage();

        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnImportHistoryInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemImportHistoryPage();

        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnIntegrationsInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemIntegrationsPage();

        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnCacheManagementInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemCacheManagementPage();

        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnBackupsInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemBackupsPage();

        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnIndexManagementInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemIndexManagementPage();

        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnAllUsersInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemAllUsersPage();

        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnLockedUsersInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemLockedUsersPage();

        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnUserRolesInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemUserRolesPage();

        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnNotificationsInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemNotificationsPage();

        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnCustomVariablesInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemCustomVariablesPage();

        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnManageEncryptionKeyInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemManageEncryptionKeyPage();
    }

    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnTheWebSetupWizardPage(\Step\Acceptance\Admin $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access the Web Setup Wizard Admin Page using the Side Nav Menu');
        $sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->iClickOnWebSetupWizardInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemWebSetupWizardPage();
        $I->goToTheAdminLogoutPage();
    }

    /**
     * @env chrome
     * @env firefox
     * @group slow
     */
    public function shouldLandOnThePartnersAndExtensionsPage(\Step\Acceptance\Admin $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access the Partners and Extensions Admin Page using the Side Nav Menu');
        $sideNavMenu->iClickOnFindPartnersAndExtensionsInTheSideNavMenu($I);
        $I->shouldBeOnTheAdminFindPartnersAndExtensionsPage();
    }

}
