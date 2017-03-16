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
        $sideNavMenu->clickOnDashboardInTheSideNavMenu($I);
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
        $sideNavMenu->clickOnSalesInTheSideNavMenu($I);
        $sideNavMenu->clickOnOrdersInTheSalesNavMenu($I);
        $I->shouldBeOnTheAdminSalesOrdersPage();

        $sideNavMenu->clickOnSalesInTheSideNavMenu($I);
        $sideNavMenu->clickOnInvoicesInTheSalesNavMenu($I);
        $I->shouldBeOnTheAdminSalesInvoicesPage();

        $sideNavMenu->clickOnSalesInTheSideNavMenu($I);
        $sideNavMenu->clickOnShipmentsInTheSalesNavMenu($I);
        $I->shouldBeOnTheAdminSalesShipmentsPage();

        $sideNavMenu->clickOnSalesInTheSideNavMenu($I);
        $sideNavMenu->clickOnCreditMemosInTheSalesNavMenu($I);
        $I->shouldBeOnTheAdminSalesCreditMemosPage();

        $sideNavMenu->clickOnSalesInTheSideNavMenu($I);
        $sideNavMenu->clickOnBillingAgreementsInTheSalesNavMenu($I);
        $I->shouldBeOnTheAdminSalesBillingAgreementsPage();

        $sideNavMenu->clickOnSalesInTheSideNavMenu($I);
        $sideNavMenu->clickOnTransactionsInTheSalesNavMenu($I);
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
        $sideNavMenu->clickOnProductsInTheSideNavMenu($I);
        $sideNavMenu->clickOnCatalogInTheProductNavMenu($I);
        $I->shouldBeOnTheAdminProductsCatalogPage();

        $sideNavMenu->clickOnProductsInTheSideNavMenu($I);
        $sideNavMenu->clickOnCategoriesInTheProductNavMenu($I);
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
        $sideNavMenu->clickOnCustomersInTheSideNavMenu($I);
        $sideNavMenu->clickOnAllCustomersInTheCustomersNavMenu($I);
        $I->shouldBeOnTheAdminCustomersAllCustomersPage();

        $sideNavMenu->clickOnCustomersInTheSideNavMenu($I);
        $sideNavMenu->clickOnNowOnlineInTheCustomersNavMenu($I);
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
        $sideNavMenu->clickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->clickOnCatalogPriceRulesInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingCatalogPriceRulePage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->clickOnCartPriceRulesInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingCartPriceRulePage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->clickOnEmailTemplatesInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingEmailTemplatesPage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->clickOnNewsletterTemplatesInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingNewsletterTemplatePage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->clickOnNewsletterQueueInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingNewsletterQueuePage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->clickOnNewsletterSubscribersInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingNewsletterSubscribersPage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->clickOnURLRewritesInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingURLRewritesPage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->clickOnSearchTermsInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingSearchTermsPage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->clickOnSearchSynonymsInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingSearchSynonymsPage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->clickOnSiteMapInTheMarketingNavMenu($I);
        $I->shouldBeOnTheAdminMarketingSiteMapPage();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu($I);
        $sideNavMenu->clickOnContentReviewsInTheMarketingNavMenu($I);
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
        $sideNavMenu->clickOnContentInTheSideNavMenu($I);
        $sideNavMenu->clickOnPagesInTheContentNavMenu($I);
        $I->shouldBeOnTheAdminContentPagesPage();

        $sideNavMenu->clickOnContentInTheSideNavMenu($I);
        $sideNavMenu->clickOnBlocksInTheContentNavMenu($I);
        $I->shouldBeOnTheAdminContentBlocksPage();

        $sideNavMenu->clickOnContentInTheSideNavMenu($I);
        $sideNavMenu->clickOnWidgetsInTheContentNavMenu($I);
        $I->shouldBeOnTheAdminContentWidgetsPage();

        $sideNavMenu->clickOnContentInTheSideNavMenu($I);
        $sideNavMenu->clickOnConfigurationInTheContentNavMenu($I);
        $I->shouldBeOnTheAdminContentConfigurationPage();

        $sideNavMenu->clickOnContentInTheSideNavMenu($I);
        $sideNavMenu->clickOnThemesInTheContentNavMenu($I);
        $I->shouldBeOnTheAdminContentThemesPage();

        $sideNavMenu->clickOnContentInTheSideNavMenu($I);
        $sideNavMenu->clickOnScheduleInTheContentNavMenu($I);
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
        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnProductsInCartInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsProductsInCartPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnSearchTermsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsSearchTermsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnAbandonedCartsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsAbandonedCartsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnNewsletterProblemReportsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsNewsletterProblemReportsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnByCustomersInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsByCustomersPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnByProductsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsByProductsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnOrdersInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsOrdersPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOTaxInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsTaxPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnInvoicedInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsInvoicedPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnShippingInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsShippingPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnRefundsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsRefundsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnCouponsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsCouponsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnPayPalSettlementInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsPayPalSettlementPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnBraintreeSettlementInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsBraintreeSettlementPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnOrderTotalInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsOrderTotalPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnOrderCountInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsOrderCountPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnNewInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsNewPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnViewsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsViewsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnBestSellersInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsBestsellersPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnLowStockInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsLowStockPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnOrderedInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsOrderedPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnDownloadsInTheReportsNavMenu($I);
        $I->shouldBeOnTheAdminReportsDownloadsPage();

        $sideNavMenu->clickOnReportsInTheSideNavMenu($I);
        $sideNavMenu->clickOnRefreshStatisticsInTheReportsNavMenu($I);
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
        $sideNavMenu->clickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->clickOnAllStoresInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresAllStoresPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->clickOnConfigurationInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresConfigurationPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->clickOnTermsAndConditionsInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresTermsAndConditionsPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->clickOnOrderStatusInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresOrderStatusPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->clickOnTaxRuleInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresTaxRulesPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->clickOnTaxZonesAndRatesInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresTaxZonesAndRatesPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->clickOnCurrencyRatesInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresCurrencyRatesPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->clickOnCurrencySymbolsInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresCurrencySymbolsPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->clickOnProductInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresProductPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->clickOnAttributesSetInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresAttributeSetPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->clickOnRatingsInTheStoresNavMenu($I);
        $I->shouldBeOnTheAdminStoresRatingPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu($I);
        $sideNavMenu->clickOnCustomerGroupInTheStoresNavMenu($I);
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
        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnImportInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemImportPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnExportInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemExportPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnImportExportTaxRatesInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemImportExportTaxRatesPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnImportHistoryInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemImportHistoryPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnIntegrationsInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemIntegrationsPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnCacheManagementInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemCacheManagementPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnBackupsInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemBackupsPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnIndexManagementInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemIndexManagementPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnAllUsersInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemAllUsersPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnLockedUsersInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemLockedUsersPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnUserRolesInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemUserRolesPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnNotificationsInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemNotificationsPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnCustomVariablesInTheSystemNavMenu($I);
        $I->shouldBeOnTheAdminSystemCustomVariablesPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnManageEncryptionKeyInTheSystemNavMenu($I);
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
        $sideNavMenu->clickOnSystemInTheSideNavMenu($I);
        $sideNavMenu->clickOnWebSetupWizardInTheSystemNavMenu($I);
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
        $sideNavMenu->clickOnFindPartnersAndExtensionsInTheSideNavMenu($I);
        $I->shouldBeOnTheAdminFindPartnersAndExtensionsPage();
    }

}
