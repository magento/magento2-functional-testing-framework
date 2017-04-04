<?php
namespace Magento\Xxyyzz\Acceptance\Backend;

use Magento\Xxyyzz\Step\Backend\AdminStep;

/**
 * @group skip
 */
class AccessAdminPagesDirectlyCest
{
    public function _before(AdminStep $I)
    {
        $I->goToTheAdminLoginPage();
        $I->loginAsAdmin();
    }

    /**
     * @env chrome
     * @env firefox
     * @env phantomjs
     * @group slow
     */
    public function shouldBeAbleToAccessEachAdminPageDirectly(AdminStep $I)
    {
        $I->goToTheAdminSalesOrdersPage();
        $I->shouldBeOnTheAdminSalesOrdersPage();

        $I->goToTheAdminSalesInvoicesPage();
        $I->shouldBeOnTheAdminSalesInvoicesPage();

        $I->goToTheAdminSalesShipmentsPage();
        $I->shouldBeOnTheAdminSalesShipmentsPage();

        $I->goToTheAdminSalesCreditMemosPage();
        $I->shouldBeOnTheAdminSalesCreditMemosPage();

        $I->goToTheAdminSalesBillingAgreementsPage();
        $I->shouldBeOnTheAdminSalesBillingAgreementsPage();

        $I->goToTheAdminSalesTransactionsPage();
        $I->shouldBeOnTheAdminSalesTransactionsPage();

        $I->goToTheAdminProductsCatalogPage();
        $I->shouldBeOnTheAdminProductsCatalogPage();

        $I->goToTheAdminProductsCategoriesPage();
        $I->shouldBeOnTheAdminProductsCategoriesPage();

        $I->goToTheAdminCustomersAllCustomersPage();
        $I->shouldBeOnTheAdminCustomersAllCustomersPage();

        $I->goToTheAdminCustomersNowOnlinePage();
        $I->shouldBeOnTheAdminCustomersNowOnlinePage();

        $I->goToTheAdminMarketingCatalogPriceRulePage();
        $I->shouldBeOnTheAdminMarketingCatalogPriceRulePage();

        $I->goToTheAdminMarketingCartPriceRulePage();
        $I->shouldBeOnTheAdminMarketingCartPriceRulePage();

        $I->goToTheAdminMarketingEmailTemplatesPage();
        $I->shouldBeOnTheAdminMarketingEmailTemplatesPage();

        $I->goToTheAdminMarketingNewsletterTemplatePage();
        $I->shouldBeOnTheAdminMarketingNewsletterTemplatePage();

        $I->goToTheAdminMarketingNewsletterQueuePage();
        $I->shouldBeOnTheAdminMarketingNewsletterQueuePage();

        $I->goToTheAdminMarketingNewsletterSubscribersPage();
        $I->shouldBeOnTheAdminMarketingNewsletterSubscribersPage();

        $I->goToTheAdminMarketingURLRewritesPage();
        $I->shouldBeOnTheAdminMarketingURLRewritesPage();

        $I->goToTheAdminMarketingSearchTermsPage();
        $I->shouldBeOnTheAdminMarketingSearchTermsPage();

        $I->goToTheAdminMarketingSearchSynonymsPage();
        $I->shouldBeOnTheAdminMarketingSearchSynonymsPage();

        $I->goToTheAdminMarketingSiteMapPage();
        $I->shouldBeOnTheAdminMarketingSiteMapPage();

        $I->goToTheAdminMarketingReviewsPage();
        $I->shouldBeOnTheAdminMarketingReviewsPage();

        $I->goToTheAdminContentPagesPage();
        $I->shouldBeOnTheAdminContentPagesPage();

        $I->goToTheAdminContentBlocksPage();
        $I->shouldBeOnTheAdminContentBlocksPage();

        $I->goToTheAdminContentWidgetsPage();
        $I->shouldBeOnTheAdminContentWidgetsPage();

        $I->goToTheAdminContentConfigurationPage();
        $I->shouldBeOnTheAdminContentConfigurationPage();

        $I->goToTheAdminContentThemesPage();
        $I->shouldBeOnTheAdminContentThemesPage();

        $I->goToTheAdminContentSchedulePage();
        $I->shouldBeOnTheAdminContentSchedulePage();

        $I->goToTheAdminReportsProductsInCartPage();
        $I->shouldBeOnTheAdminReportsProductsInCartPage();

        $I->goToTheAdminReportsSearchTermsPage();
        $I->shouldBeOnTheAdminReportsSearchTermsPage();

        $I->goToTheAdminReportsAbandonedCartsPage();
        $I->shouldBeOnTheAdminReportsAbandonedCartsPage();

        $I->goToTheAdminReportsNewsletterProblemReportsPage();
        $I->shouldBeOnTheAdminReportsNewsletterProblemReportsPage();

        $I->goToTheAdminReportsByCustomersPage();
        $I->shouldBeOnTheAdminReportsByCustomersPage();

        $I->goToTheAdminReportsByProductsPage();
        $I->shouldBeOnTheAdminReportsByProductsPage();

        $I->goToTheAdminReportsOrdersPage();
        $I->shouldBeOnTheAdminReportsOrdersPage();

        $I->goToTheAdminReportsTaxPage();
        $I->shouldBeOnTheAdminReportsTaxPage();

        $I->goToTheAdminReportsInvoicedPage();
        $I->shouldBeOnTheAdminReportsInvoicedPage();

        $I->goToTheAdminReportsShippingPage();
        $I->shouldBeOnTheAdminReportsShippingPage();

        $I->goToTheAdminReportsRefundsPage();
        $I->shouldBeOnTheAdminReportsRefundsPage();

        $I->goToTheAdminReportsCouponsPage();
        $I->shouldBeOnTheAdminReportsCouponsPage();

        $I->goToTheAdminReportsPayPalSettlementPage();
        $I->shouldBeOnTheAdminReportsPayPalSettlementPage();

        $I->goToTheAdminReportsBraintreeSettlementPage();
        $I->shouldBeOnTheAdminReportsBraintreeSettlementPage();

        $I->goToTheAdminReportsOrderTotalPage();
        $I->shouldBeOnTheAdminReportsOrderTotalPage();

        $I->goToTheAdminReportsOrderCountPage();
        $I->shouldBeOnTheAdminReportsOrderCountPage();

        $I->goToTheAdminReportsNewPage();
        $I->shouldBeOnTheAdminReportsNewPage();

        $I->goToTheAdminReportsViewsPage();
        $I->shouldBeOnTheAdminReportsViewsPage();

        $I->goToTheAdminReportsBestsellersPage();
        $I->shouldBeOnTheAdminReportsBestsellersPage();

        $I->goToTheAdminReportsLowStockPage();
        $I->shouldBeOnTheAdminReportsLowStockPage();

        $I->goToTheAdminReportsOrderedPage();
        $I->shouldBeOnTheAdminReportsOrderedPage();

        $I->goToTheAdminReportsDownloadsPage();
        $I->shouldBeOnTheAdminReportsDownloadsPage();

        $I->goToTheAdminReportRefreshStatisticsPage();
        $I->shouldBeOnTheAdminReportRefreshStatisticsPage();

        $I->goToTheAdminStoresAllStoresPage();
        $I->shouldBeOnTheAdminStoresAllStoresPage();

        $I->goToTheAdminStoresConfigurationPage();
        $I->shouldBeOnTheAdminStoresConfigurationPage();

        $I->goToTheAdminStoresTermsAndConditionsPage();
        $I->shouldBeOnTheAdminStoresTermsAndConditionsPage();

        $I->goToTheAdminStoresOrderStatusPage();
        $I->shouldBeOnTheAdminStoresOrderStatusPage();

        $I->goToTheAdminStoresTaxRulesPage();
        $I->shouldBeOnTheAdminStoresTaxRulesPage();

        $I->goToTheAdminStoresTaxZonesAndRatesPage();
        $I->shouldBeOnTheAdminStoresTaxZonesAndRatesPage();

        $I->goToTheAdminStoresCurrencyRatesPage();
        $I->shouldBeOnTheAdminStoresCurrencyRatesPage();

        $I->goToTheAdminStoresCurrencySymbolsPage();
        $I->shouldBeOnTheAdminStoresCurrencySymbolsPage();

        $I->goToTheAdminStoresProductPage();
        $I->shouldBeOnTheAdminStoresProductPage();

        $I->goToTheAdminStoresAttributeSetPage();
        $I->shouldBeOnTheAdminStoresAttributeSetPage();

        $I->goToTheAdminStoresRatingPage();
        $I->shouldBeOnTheAdminStoresRatingPage();

        $I->goToTheAdminStoresCustomerGroupsPage();
        $I->shouldBeOnTheAdminStoresCustomerGroupsPage();

        $I->goToTheAdminSystemImportPage();
        $I->shouldBeOnTheAdminSystemImportPage();

        $I->goToTheAdminSystemExportPage();
        $I->shouldBeOnTheAdminSystemExportPage();

        $I->goToTheAdminSystemImportExportTaxRatesPage();
        $I->shouldBeOnTheAdminSystemImportExportTaxRatesPage();

        $I->goToTheAdminSystemImportHistoryPage();
        $I->shouldBeOnTheAdminSystemImportHistoryPage();

        $I->goToTheAdminSystemIntegrationsPage();
        $I->shouldBeOnTheAdminSystemIntegrationsPage();

        $I->goToTheAdminSystemCacheManagementPage();
        $I->shouldBeOnTheAdminSystemCacheManagementPage();

        $I->goToTheAdminSystemBackupsPage();
        $I->shouldBeOnTheAdminSystemBackupsPage();

        $I->goToTheAdminSystemIndexManagementPage();
        $I->shouldBeOnTheAdminSystemIndexManagementPage();

        $I->goToTheAdminSystemAllUsersPage();
        $I->shouldBeOnTheAdminSystemAllUsersPage();

        $I->goToTheAdminSystemLockedUsersPage();
        $I->shouldBeOnTheAdminSystemLockedUsersPage();

        $I->goToTheAdminSystemUserRolesPage();
        $I->shouldBeOnTheAdminSystemUserRolesPage();

        $I->goToTheAdminSystemNotificationsPage();
        $I->shouldBeOnTheAdminSystemNotificationsPage();

        $I->goToTheAdminSystemCustomVariablesPage();
        $I->shouldBeOnTheAdminSystemCustomVariablesPage();

        $I->goToTheAdminSystemManageEncryptionKeyPage();
        $I->shouldBeOnTheAdminSystemManageEncryptionKeyPage();
    }
}
