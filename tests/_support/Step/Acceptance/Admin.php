<?php

namespace Step\Acceptance;

require_once __DIR__.'/../../Helper/URL_List.php';

class Admin extends \AcceptanceTester
{

    public function goToTheAdminLoginPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminLogin);
    }

    public function goToTheAdminLogoutPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminLogout);
    }

    public function goToRandomAdminPage()
    {
        $I = $this;

        $admin_url_list = array(
            "/admin/admin/dashboard/",
            "/admin/sales/order/",
            "/admin/sales/invoice/",
            "/admin/sales/shipment/",
            "/admin/sales/creditmemo/",
            "/admin/paypal/billing_agreement/",
            "/admin/sales/transactions/",
            "/admin/catalog/product/",
            "/admin/catalog/category/",
            "/admin/customer/index/",
            "/admin/customer/online/",
            "/admin/catalog_rule/promo_catalog/",
            "/admin/sales_rule/promo_quote/",
            "/admin/admin/email_template/",
            "/admin/newsletter/template/",
            "/admin/newsletter/queue/",
            "/admin/newsletter/subscriber/",
            "/admin/admin/url_rewrite/index/",
            "/admin/search/term/index/",
            "/admin/search/synonyms/index/",
            "/admin/admin/sitemap/",
            "/admin/review/product/index/",
            "/admin/cms/page/",
            "/admin/cms/block/",
            "/admin/admin/widget_instance/",
            "/admin/theme/design_config/",
            "/admin/admin/system_design_theme/",
            "/admin/admin/system_design/",
            "/admin/reports/report_shopcart/product/",
            "/admin/search/term/report/",
            "/admin/reports/report_shopcart/abandoned/",
            "/admin/newsletter/problem/",
            "/admin/reports/report_review/customer/",
            "/admin/reports/report_review/product/",
            "/admin/reports/report_sales/sales/",
            "/admin/reports/report_sales/tax/",
            "/admin/reports/report_sales/invoiced/",
            "/admin/reports/report_sales/shipping/",
            "/admin/reports/report_sales/refunded/",
            "/admin/reports/report_sales/coupons/",
            "/admin/paypal/paypal_reports/",
            "/admin/braintree/report/",
            "/admin/reports/report_customer/totals/",
            "/admin/reports/report_customer/orders/",
            "/admin/reports/report_customer/accounts/",
            "/admin/reports/report_product/viewed/",
            "/admin/reports/report_sales/bestsellers/",
            "/admin/reports/report_product/lowstock/",
            "/admin/reports/report_product/sold/",
            "/admin/reports/report_product/downloads/",
            "/admin/reports/report_statistics/",
            "/admin/admin/system_store/",
            "/admin/admin/system_config/",
            "/admin/checkout/agreement/",
            "/admin/sales/order_status/",
            "/admin/tax/rule/",
            "/admin/tax/rate/",
            "/admin/admin/system_currency/",
            "/admin/admin/system_currencysymbol/",
            "/admin/catalog/product_attribute/",
            "/admin/catalog/product_set/",
            "/admin/review/rating/",
            "/admin/customer/group/",
            "/admin/admin/import/",
            "/admin/admin/export/",
            "/admin/tax/rate/importExport/",
            "/admin/admin/history/",
            "/admin/admin/integration/",
            "/admin/admin/cache/",
            "/admin/backup/index/",
            "/admin/indexer/indexer/list/",
            "/admin/admin/user/",
            "/admin/admin/locks/",
            "/admin/admin/user_role/",
            "/admin/admin/notification/",
            "/admin/admin/system_variable/",
            "/admin/admin/crypt_key/"
        );

        $random_admin_url = array_rand($admin_url_list, 1);
        
        $I->amOnPage($admin_url_list[$random_admin_url]);

        return $admin_url_list[$random_admin_url];
    }

    public function waitForSpinnerToDisappear()
    {
        $I = $this;
        $I->wait(1);
        $I->waitForElementNotVisible('.admin__data-grid-loading-mask', 15);
    }

    // Sales
    public function goToTheAdminSalesOrdersPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSalesOrders);
    }

    public function goToTheAdminSalesInvoicesPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSalesInvoices);
    }

    public function goToTheAdminSalesShipmentsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSalesShipments);
    }

    public function goToTheAdminSalesCreditMemosPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSalesCreditMemos);
    }

    public function goToTheAdminSalesBillingAgreementsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSalesBillingAgreements);
    }

    public function goToTheAdminSalesTransactionsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSalesTransactions);
    }

    // Products
    public function goToTheAdminProductsCatalogPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminProductsCatalog);
    }

    public function goToTheAdminProductsCategoriesPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminProductsCategories);
    }

    // Customers
    public function goToTheAdminCustomersAllCustomersPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminCustomersAllCustomers);
    }

    public function goToTheAdminCustomersNowOnlinePage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminCustomersNowOnline);
    }

    // Marketing
    public function goToTheAdminMarketingCatalogPriceRulePage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminMarketingCatalogPriceRule);
    }

    public function goToTheAdminMarketingCartPriceRulePage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminMarketingCartPriceRules);
    }

    public function goToTheAdminMarketingEmailTemplatesPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminMarketingEmailTemplates);
    }

    public function goToTheAdminMarketingNewsletterTemplatePage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminMarketingNewsletterTemplate);
    }

    public function goToTheAdminMarketingNewsletterQueuePage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminMarketingNewsletterQueue);
    }

    public function goToTheAdminMarketingNewsletterSubscribersPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminMarketingNewsletterSubscribers);
    }

    public function goToTheAdminMarketingURLRewritesPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminMarketingURLRewrites);
    }

    public function goToTheAdminMarketingSearchTermsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminMarketingSearchTerms);
    }

    public function goToTheAdminMarketingSearchSynonymsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminMarketingSearchSynonyms);
    }

    public function goToTheAdminMarketingSiteMapPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminMarketingSiteMap);
    }

    public function goToTheAdminMarketingReviewsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminMarketingReviews);
    }

    // Content
    public function goToTheAdminContentPagesPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminContentPages);
    }

    public function goToTheAdminContentBlocksPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminContentBlocks);
    }

    public function goToTheAdminContentWidgetsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminContentWidgets);
    }

    public function goToTheAdminContentConfigurationPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminContentConfiguration);
    }

    public function goToTheAdminContentThemesPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminContentThemes);
    }

    public function goToTheAdminContentSchedulePage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminContentSchedule);
    }

    // Reports
    public function goToTheAdminReportsProductsInCartPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsProductsInCart);
    }

    public function goToTheAdminReportsSearchTermsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsSearchTerms);
    }

    public function goToTheAdminReportsAbandonedCartsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsAbandonedCArts);
    }

    public function goToTheAdminReportsNewsletterProblemReportsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsNewsletterProblemReports);
    }

    public function goToTheAdminReportsByCustomersPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsByCustomers);
    }

    public function goToTheAdminReportsByProductsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsByProducts);
    }

    public function goToTheAdminReportsOrdersPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsOrders);
    }

    public function goToTheAdminReportsTaxPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsTax);
    }

    public function goToTheAdminReportsInvoicedPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsInvoiced);
    }

    public function goToTheAdminReportsShippingPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsShipping);
    }

    public function goToTheAdminReportsRefundsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsRefunds);
    }

    public function goToTheAdminReportsCouponsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsCoupons);
    }

    public function goToTheAdminReportsPayPalSettlementPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsPayPalSettlement);
    }

    public function goToTheAdminReportsBraintreeSettlementPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsBraintreeSettlement);
    }

    public function goToTheAdminReportsOrderTotalPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsOrderTotal);
    }

    public function goToTheAdminReportsOrderCountPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsOrderCount);
    }

    public function goToTheAdminReportsNewPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsNew);
    }

    public function goToTheAdminReportsViewsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsViews);
    }

    public function goToTheAdminReportsBestsellersPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsBestsellers);
    }

    public function goToTheAdminReportsLowStockPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsLowStock);
    }

    public function goToTheAdminReportsOrderedPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsOrdered);
    }

    public function goToTheAdminReportsDownloadsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsDownloads);
    }

    public function goToTheAdminReportRefreshStatisticsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminReportsRefreshStatistics);
    }

    // Stores
    public function goToTheAdminStoresAllStoresPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminStoresAllStores);
    }

    public function goToTheAdminStoresConfigurationPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminStoresConfiguration);
    }

    public function goToTheAdminStoresTermsAndConditionsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminStoresTermsAndConditions);
    }

    public function goToTheAdminStoresOrderStatusPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminStoresOrderStatus);
    }

    public function goToTheAdminStoresTaxRulesPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminStoresTaxRules);
    }

    public function goToTheAdminStoresTaxZonesAndRatesPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminStoresTaxZonesAndRates);
    }

    public function goToTheAdminStoresCurrencyRatesPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminStoresCurrencyRates);
    }

    public function goToTheAdminStoresCurrencySymbolsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminStoresCurrencySymbols);
    }

    public function goToTheAdminStoresProductPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminStoresProduct);
    }

    public function goToTheAdminStoresAttributeSetPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminStoresAttributeSet);
    }

    public function goToTheAdminStoresRatingPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminStoresRating);
    }

    public function goToTheAdminStoresCustomerGroupsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminStoresCustomerGroups);
    }

    // System
    public function goToTheAdminSystemImportPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemImport);
    }

    public function goToTheAdminSystemExportPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemExport);
    }

    public function goToTheAdminSystemImportExportTaxRatesPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemImportExportTaxRates);
    }

    public function goToTheAdminSystemImportHistoryPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemImportHistory);
    }

    public function goToTheAdminSystemIntegrationsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemIntegrations);
    }

    public function goToTheAdminSystemCacheManagementPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemCacheManagement);
    }

    public function goToTheAdminSystemBackupsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemBackups);
    }

    public function goToTheAdminSystemIndexManagementPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemIndexManagement);
    }

    public function goToTheAdminSystemWebSetupWizardPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemWebSetupWizard);
    }

    public function goToTheAdminSystemAllUsersPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemAllUsers);
    }

    public function goToTheAdminSystemLockedUsersPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemLockedUsers);
    }

    public function goToTheAdminSystemUserRolesPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemUserRoles);
    }

    public function goToTheAdminSystemNotificationsPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemNotifications);
    }

    public function goToTheAdminSystemCustomVariablesPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemCustomVariables);
    }

    public function goToTheAdminSystemManageEncryptionKeyPage()
    {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminSystemManageEncryptionKey);
    }

    public function goToTheAdminFindPartnersAndExtensionsPage() {
        $I = $this;
        $I->amOnPage(\Page\Acceptance\AdminURLList::$adminFindPartnersAndExtensions);
    }

    public function shouldBeOnTheAdminLoginPage() {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminLogin);
    }

    public function shouldBeOnTheAdminDashboardPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminDashboard);
        $I->see('Dashboard', '.page-title');
    }

    public function shouldBeOnTheForgotYourPasswordPage() {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminForgotYourPassword);
    }

    // Sales
    public function shouldBeOnTheAdminSalesOrdersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSalesOrders);
        $I->see('Orders', '.page-title');
    }

    public function shouldBeOnTheAdminSalesInvoicesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSalesInvoices);
        $I->see('Invoices', '.page-title');
    }

    public function shouldBeOnTheAdminSalesShipmentsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSalesShipments);
        $I->see('Shipments', '.page-title');
    }

    public function shouldBeOnTheAdminSalesCreditMemosPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSalesCreditMemos);
        $I->see('Credit Memos', '.page-title');
    }

    public function shouldBeOnTheAdminSalesBillingAgreementsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSalesBillingAgreements);
        $I->see('Billing Agreements', '.page-title');
    }

    public function shouldBeOnTheAdminSalesTransactionsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSalesTransactions);
        $I->see('Transactions', '.page-title');
    }

    // Products
    public function shouldBeOnTheAdminProductsCatalogPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminProductsCatalog);
        $I->see('Catalog', '.page-title');
    }

    public function shouldBeOnTheAdminProductsCategoriesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminProductsCategories);
        $I->see('Default Category', '.page-title');
    }

    // Customers
    public function shouldBeOnTheAdminCustomersAllCustomersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminCustomersAllCustomers);
        $I->see('Customers', '.page-title');
    }

    public function shouldBeOnTheAdminCustomersNowOnlinePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminCustomersNowOnline);
        $I->see('Customers Now Online', '.page-title');
    }

    // Marketing
    public function shouldBeOnTheAdminMarketingCatalogPriceRulePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminMarketingCatalogPriceRule);
        $I->see('Catalog Price Rule', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingCartPriceRulePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminMarketingCartPriceRules);
        $I->see('Cart Price Rules', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingEmailTemplatesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminMarketingEmailTemplates);
        $I->see('Email Templates', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingNewsletterTemplatePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminMarketingNewsletterTemplate);
        $I->see('Newsletter Templates', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingNewsletterQueuePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminMarketingNewsletterQueue);
        $I->see('Newsletter Queue', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingNewsletterSubscribersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminMarketingNewsletterSubscribers);
        $I->see('Newsletter Subscribers', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingURLRewritesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminMarketingURLRewrites);
        $I->see('URL Rewrites', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingSearchTermsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminMarketingSearchTerms);
        $I->see('Search Terms', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingSearchSynonymsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminMarketingSearchSynonyms);
        $I->see('Search Synonyms', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingSiteMapPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminMarketingSiteMap);
        $I->see('Site Map', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingReviewsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminMarketingReviews);
        $I->see('Reviews', '.page-title');
    }

    // Content
    public function shouldBeOnTheAdminContentPagesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminContentPages);
        $I->see('Pages', '.page-title');
    }

    public function shouldBeOnTheAdminContentBlocksPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminContentBlocks);
        $I->see('Blocks', '.page-title');
    }

    public function shouldBeOnTheAdminContentWidgetsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminContentWidgets);
        $I->see('Widgets', '.page-title');
    }

    public function shouldBeOnTheAdminContentConfigurationPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminContentConfiguration);
        $I->see('Design Configuration', '.page-title');
    }

    public function shouldBeOnTheAdminContentThemesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminContentThemes);
        $I->see('Themes', '.page-title');
    }

    public function shouldBeOnTheAdminContentSchedulePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminContentSchedule);
        $I->see('Store Design Schedule', '.page-title');
    }

    // Reports
    public function shouldBeOnTheAdminReportsProductsInCartPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsProductsInCart);
        $I->see('Products in Carts', '.page-title');
    }

    public function shouldBeOnTheAdminReportsSearchTermsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsSearchTerms);
        $I->see('Search Terms Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsAbandonedCartsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsAbandonedCArts);
        $I->see('Abandoned Carts', '.page-title');
    }

    public function shouldBeOnTheAdminReportsNewsletterProblemReportsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsNewsletterProblemReports);
        $I->see('Newsletter Problems Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsByCustomersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsByCustomers);
        $I->see('Customer Reviews Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsByProductsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsByProducts);
        $I->see('Product Reviews Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsOrdersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsOrders);
        $I->see('Orders Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsTaxPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsTax);
        $I->see('Tax Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsInvoicedPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsInvoiced);
        $I->see('Invoice Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsShippingPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsShipping);
        $I->see('Shipping Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsRefundsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsRefunds);
        $I->see('Refunds Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsCouponsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsCoupons);
        $I->see('Coupons Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsPayPalSettlementPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsPayPalSettlement);
        $I->see('PayPal Settlement Reports', '.page-title');
    }

    public function shouldBeOnTheAdminReportsBraintreeSettlementPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsBraintreeSettlement);
        $I->see('Braintree Settlement Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsOrderTotalPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsOrderTotal);
        $I->see('Order Total Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsOrderCountPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsOrderCount);
        $I->see('Order Count Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsNewPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsNew);
        $I->see('New Accounts Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsViewsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsViews);
        $I->see('Product Views Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsBestsellersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsBestsellers);
        $I->see('Bestsellers Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsLowStockPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsLowStock);
        $I->see('Low Stock Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsOrderedPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsOrdered);
        $I->see('Ordered Products Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsDownloadsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsDownloads);
        $I->see('Downloads Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportRefreshStatisticsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminReportsRefreshStatistics);
        $I->see('Refresh Statistics', '.page-title');
    }

    // Stores
    public function shouldBeOnTheAdminStoresAllStoresPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminStoresAllStores);
        $I->see('Stores', '.page-title');
    }

    public function shouldBeOnTheAdminStoresConfigurationPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminStoresConfiguration);
        $I->see('Configuration', '.page-title');
    }

    public function shouldBeOnTheAdminStoresTermsAndConditionsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminStoresTermsAndConditions);
        $I->see('Terms and Conditions', '.page-title');
    }

    public function shouldBeOnTheAdminStoresOrderStatusPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminStoresOrderStatus);
        $I->see('Order Status', '.page-title');
    }

    public function shouldBeOnTheAdminStoresTaxRulesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminStoresTaxRules);
        $I->see('Tax Rules', '.page-title');
    }

    public function shouldBeOnTheAdminStoresTaxZonesAndRatesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminStoresTaxZonesAndRates);
        $I->see('Tax Zones and Rates', '.page-title');
    }

    public function shouldBeOnTheAdminStoresCurrencyRatesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminStoresCurrencyRates);
        $I->see('Currency Rates', '.page-title');
    }

    public function shouldBeOnTheAdminStoresCurrencySymbolsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminStoresCurrencySymbols);
        $I->see('Currency Symbols', '.page-title');
    }

    public function shouldBeOnTheAdminStoresProductPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminStoresProduct);
        $I->see('Product Attributes', '.page-title');
    }

    public function shouldBeOnTheAdminStoresAttributeSetPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminStoresAttributeSet);
        $I->see('Attribute Sets', '.page-title');
    }

    public function shouldBeOnTheAdminStoresRatingPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminStoresRating);
        $I->see('Ratings', '.page-title');
    }

    public function shouldBeOnTheAdminStoresCustomerGroupsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminStoresCustomerGroups);
        $I->see('Customer Groups', '.page-title');
    }

    // System
    public function shouldBeOnTheAdminSystemImportPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemImport);
        $I->see('Import', '.page-title');
    }

    public function shouldBeOnTheAdminSystemExportPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemExport);
        $I->see('Export', '.page-title');
    }

    public function shouldBeOnTheAdminSystemImportExportTaxRatesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemImportExportTaxRates);
        $I->see('Import and Export Tax Rates', '.page-title');
    }

    public function shouldBeOnTheAdminSystemImportHistoryPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemImportHistory);
        $I->see('Import History', '.page-title');
    }

    public function shouldBeOnTheAdminSystemIntegrationsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemIntegrations);
        $I->see('Integrations', '.page-title');
    }

    public function shouldBeOnTheAdminSystemCacheManagementPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemCacheManagement);
        $I->see('Cache Management', '.page-title');
    }

    public function shouldBeOnTheAdminSystemBackupsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemBackups);
        $I->see('Backups', '.page-title');
    }

    public function shouldBeOnTheAdminSystemIndexManagementPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemIndexManagement);
        $I->see('Index Management', '.page-title');
    }

    public function shouldBeOnTheAdminSystemWebSetupWizardPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemWebSetupWizard);
        $I->see('Setup Wizard', '.page-title');
    }

    public function shouldBeOnTheAdminSystemAllUsersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemAllUsers);
        $I->see('Users', '.page-title');
    }

    public function shouldBeOnTheAdminSystemLockedUsersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemLockedUsers);
        $I->see('Locked Users', '.page-title');
    }

    public function shouldBeOnTheAdminSystemUserRolesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemUserRoles);
        $I->see('Roles', '.page-title');
    }

    public function shouldBeOnTheAdminSystemNotificationsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemNotifications);
        $I->see('Notifications', '.page-title');
    }

    public function shouldBeOnTheAdminSystemCustomVariablesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemCustomVariables);
        $I->see('Custom Variables', '.page-title');
    }

    public function shouldBeOnTheAdminSystemManageEncryptionKeyPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminSystemManageEncryptionKey);
        $I->see('Encryption Key', '.page-title');
    }

    public function shouldBeOnTheAdminFindPartnersAndExtensionsPage() {
        $I = $this;
        $I->seeInCurrentUrl(\Page\Acceptance\AdminURLList::$adminFindPartnersAndExtensions);
        $I->see('Magento Marketplace', '.page-title');
    }

    public function closeAdminNotification() {
        $I = $this;

        // Cheating here for the minute. Still working on the best method to deal with this issue.
        $I->executeJS("jQuery('.modal-popup').remove(); jQuery('.modals-overlay').remove();");

//        try {
//            $I->waitForElementVisible('._show .action-close', 1);
//            $I->click('._show .action-close');
//            $I->waitForElementNotVisible('._show .action-close', 1);
//        } catch (\Exception $e) {
//            return false;
//        }
    }

    public function loginAsTheFollowingAdmin($username, $password) {
        $I = $this;
        $I->fillField('login[username]', $username);
        $I->fillField('login[password]', $password);
        $I->click('Sign in');

        $I->closeAdminNotification();
    }

    public function loginAsAnExistingAdmin() {
        $I = $this;
        $I->fillField('login[username]', 'admin');
        $I->fillField('login[password]', 'admin123');
        $I->click('Sign in');

        $I->closeAdminNotification();
    }
}