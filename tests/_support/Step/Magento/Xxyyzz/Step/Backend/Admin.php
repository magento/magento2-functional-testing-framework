<?php
namespace Magento\Xxyyzz\Step\Backend;

require_once __DIR__ . '/../../../../../Helper/Magento/Xxyyzz/Helper/AdminUrlList.php';

class Admin extends \AcceptanceTester
{
    public function goToTheAdminLoginPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminLogin);
    }

    public function goToTheAdminLogoutPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminLogout);
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

    // Sales
    public function goToTheAdminSalesOrdersPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSalesOrders);
    }

    public function goToTheAdminSalesInvoicesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSalesInvoices);
    }

    public function goToTheAdminSalesShipmentsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSalesShipments);
    }

    public function goToTheAdminSalesCreditMemosPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSalesCreditMemos);
    }

    public function goToTheAdminSalesBillingAgreementsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSalesBillingAgreements);
    }

    public function goToTheAdminSalesTransactionsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSalesTransactions);
    }

    // Products
    public function goToTheAdminProductsCatalogPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminProductsCatalog);
    }

    public function goToTheAdminProductsCategoriesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminProductsCategories);
    }

    // Customers
    public function goToTheAdminCustomersAllCustomersPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCustomersAllCustomers);
    }

    public function goToTheAdminCustomersNowOnlinePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCustomersNowOnline);
    }

    // Marketing
    public function goToTheAdminMarketingCatalogPriceRulePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingCatalogPriceRule);
    }

    public function goToTheAdminMarketingCartPriceRulePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingCartPriceRules);
    }

    public function goToTheAdminMarketingEmailTemplatesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingEmailTemplates);
    }

    public function goToTheAdminMarketingNewsletterTemplatePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingNewsletterTemplate);
    }

    public function goToTheAdminMarketingNewsletterQueuePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingNewsletterQueue);
    }

    public function goToTheAdminMarketingNewsletterSubscribersPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingNewsletterSubscribers);
    }

    public function goToTheAdminMarketingURLRewritesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingURLRewrites);
    }

    public function goToTheAdminMarketingSearchTermsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingSearchTerms);
    }

    public function goToTheAdminMarketingSearchSynonymsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingSearchSynonyms);
    }

    public function goToTheAdminMarketingSiteMapPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingSiteMap);
    }

    public function goToTheAdminMarketingReviewsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingReviews);
    }

    // Content
    public function goToTheAdminContentPagesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminContentPages);
    }

    public function goToTheAdminContentBlocksPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminContentBlocks);
    }

    public function goToTheAdminContentWidgetsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminContentWidgets);
    }

    public function goToTheAdminContentConfigurationPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminContentConfiguration);
    }

    public function goToTheAdminContentThemesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminContentThemes);
    }

    public function goToTheAdminContentSchedulePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminContentSchedule);
    }

    // Reports
    public function goToTheAdminReportsProductsInCartPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsProductsInCart);
    }

    public function goToTheAdminReportsSearchTermsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsSearchTerms);
    }

    public function goToTheAdminReportsAbandonedCartsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsAbandonedCArts);
    }

    public function goToTheAdminReportsNewsletterProblemReportsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsNewsletterProblemReports);
    }

    public function goToTheAdminReportsByCustomersPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsByCustomers);
    }

    public function goToTheAdminReportsByProductsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsByProducts);
    }

    public function goToTheAdminReportsOrdersPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsOrders);
    }

    public function goToTheAdminReportsTaxPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsTax);
    }

    public function goToTheAdminReportsInvoicedPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsInvoiced);
    }

    public function goToTheAdminReportsShippingPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsShipping);
    }

    public function goToTheAdminReportsRefundsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsRefunds);
    }

    public function goToTheAdminReportsCouponsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsCoupons);
    }

    public function goToTheAdminReportsPayPalSettlementPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsPayPalSettlement);
    }

    public function goToTheAdminReportsBraintreeSettlementPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsBraintreeSettlement);
    }

    public function goToTheAdminReportsOrderTotalPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsOrderTotal);
    }

    public function goToTheAdminReportsOrderCountPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsOrderCount);
    }

    public function goToTheAdminReportsNewPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsNew);
    }

    public function goToTheAdminReportsViewsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsViews);
    }

    public function goToTheAdminReportsBestsellersPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsBestsellers);
    }

    public function goToTheAdminReportsLowStockPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsLowStock);
    }

    public function goToTheAdminReportsOrderedPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsOrdered);
    }

    public function goToTheAdminReportsDownloadsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsDownloads);
    }

    public function goToTheAdminReportRefreshStatisticsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsRefreshStatistics);
    }

    // Stores
    public function goToTheAdminStoresAllStoresPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresAllStores);
    }

    public function goToTheAdminStoresConfigurationPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresConfiguration);
    }

    public function goToTheAdminStoresTermsAndConditionsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresTermsAndConditions);
    }

    public function goToTheAdminStoresOrderStatusPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresOrderStatus);
    }

    public function goToTheAdminStoresTaxRulesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresTaxRules);
    }

    public function goToTheAdminStoresTaxZonesAndRatesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresTaxZonesAndRates);
    }

    public function goToTheAdminStoresCurrencyRatesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresCurrencyRates);
    }

    public function goToTheAdminStoresCurrencySymbolsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresCurrencySymbols);
    }

    public function goToTheAdminStoresProductPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresProduct);
    }

    public function goToTheAdminStoresAttributeSetPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresAttributeSet);
    }

    public function goToTheAdminStoresRatingPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresRating);
    }

    public function goToTheAdminStoresCustomerGroupsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresCustomerGroups);
    }

    // System
    public function goToTheAdminSystemImportPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemImport);
    }

    public function goToTheAdminSystemExportPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemExport);
    }

    public function goToTheAdminSystemImportExportTaxRatesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemImportExportTaxRates);
    }

    public function goToTheAdminSystemImportHistoryPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemImportHistory);
    }

    public function goToTheAdminSystemIntegrationsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemIntegrations);
    }

    public function goToTheAdminSystemCacheManagementPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemCacheManagement);
    }

    public function goToTheAdminSystemBackupsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemBackups);
    }

    public function goToTheAdminSystemIndexManagementPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemIndexManagement);
    }

    public function goToTheAdminSystemWebSetupWizardPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemWebSetupWizard);
    }

    public function goToTheAdminSystemAllUsersPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemAllUsers);
    }

    public function goToTheAdminSystemLockedUsersPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemLockedUsers);
    }

    public function goToTheAdminSystemUserRolesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemUserRoles);
    }

    public function goToTheAdminSystemNotificationsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemNotifications);
    }

    public function goToTheAdminSystemCustomVariablesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemCustomVariables);
    }

    public function goToTheAdminSystemManageEncryptionKeyPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemManageEncryptionKey);
    }

    public function goToTheAdminFindPartnersAndExtensionsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminFindPartnersAndExtensions);
    }

    public function shouldBeOnTheAdminLoginPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminLogin);
    }

    public function shouldBeOnTheAdminDashboardPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminDashboard);
        $I->see('Dashboard', '.page-title');
    }

    public function shouldBeOnTheForgotYourPasswordPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminForgotYourPassword);
    }

    // Sales
    public function shouldBeOnTheAdminSalesOrdersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSalesOrders);
        $I->see('Orders', '.page-title');
    }

    public function shouldBeOnTheAdminSalesInvoicesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSalesInvoices);
        $I->see('Invoices', '.page-title');
    }

    public function shouldBeOnTheAdminSalesShipmentsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSalesShipments);
        $I->see('Shipments', '.page-title');
    }

    public function shouldBeOnTheAdminSalesCreditMemosPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSalesCreditMemos);
        $I->see('Credit Memos', '.page-title');
    }

    public function shouldBeOnTheAdminSalesBillingAgreementsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSalesBillingAgreements);
        $I->see('Billing Agreements', '.page-title');
    }

    public function shouldBeOnTheAdminSalesTransactionsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSalesTransactions);
        $I->see('Transactions', '.page-title');
    }

    // Products
    public function shouldBeOnTheAdminProductsCatalogPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminProductsCatalog);
        $I->see('Catalog', '.page-title');
    }

    public function shouldBeOnTheAdminProductsCategoriesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminProductsCategories);
        $I->see('Default Category', '.page-title');
    }

    // Customers
    public function shouldBeOnTheAdminCustomersAllCustomersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCustomersAllCustomers);
        $I->see('Customers', '.page-title');
    }

    public function shouldBeOnTheAdminCustomersNowOnlinePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCustomersNowOnline);
        $I->see('Customers Now Online', '.page-title');
    }

    // Marketing
    public function shouldBeOnTheAdminMarketingCatalogPriceRulePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingCatalogPriceRule);
        $I->see('Catalog Price Rule', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingCartPriceRulePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingCartPriceRules);
        $I->see('Cart Price Rules', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingEmailTemplatesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingEmailTemplates);
        $I->see('Email Templates', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingNewsletterTemplatePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingNewsletterTemplate);
        $I->see('Newsletter Templates', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingNewsletterQueuePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingNewsletterQueue);
        $I->see('Newsletter Queue', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingNewsletterSubscribersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingNewsletterSubscribers);
        $I->see('Newsletter Subscribers', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingURLRewritesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingURLRewrites);
        $I->see('URL Rewrites', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingSearchTermsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingSearchTerms);
        $I->see('Search Terms', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingSearchSynonymsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingSearchSynonyms);
        $I->see('Search Synonyms', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingSiteMapPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingSiteMap);
        $I->see('Site Map', '.page-title');
    }

    public function shouldBeOnTheAdminMarketingReviewsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminMarketingReviews);
        $I->see('Reviews', '.page-title');
    }

    // Content
    public function shouldBeOnTheAdminContentPagesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminContentPages);
        $I->see('Pages', '.page-title');
    }

    public function shouldBeOnTheAdminContentBlocksPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminContentBlocks);
        $I->see('Blocks', '.page-title');
    }

    public function shouldBeOnTheAdminContentWidgetsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminContentWidgets);
        $I->see('Widgets', '.page-title');
    }

    public function shouldBeOnTheAdminContentConfigurationPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminContentConfiguration);
        $I->see('Design Configuration', '.page-title');
    }

    public function shouldBeOnTheAdminContentThemesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminContentThemes);
        $I->see('Themes', '.page-title');
    }

    public function shouldBeOnTheAdminContentSchedulePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminContentSchedule);
        $I->see('Store Design Schedule', '.page-title');
    }

    // Reports
    public function shouldBeOnTheAdminReportsProductsInCartPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsProductsInCart);
        $I->see('Products in Carts', '.page-title');
    }

    public function shouldBeOnTheAdminReportsSearchTermsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsSearchTerms);
        $I->see('Search Terms Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsAbandonedCartsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsAbandonedCArts);
        $I->see('Abandoned Carts', '.page-title');
    }

    public function shouldBeOnTheAdminReportsNewsletterProblemReportsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsNewsletterProblemReports);
        $I->see('Newsletter Problems Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsByCustomersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsByCustomers);
        $I->see('Customer Reviews Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsByProductsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsByProducts);
        $I->see('Product Reviews Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsOrdersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsOrders);
        $I->see('Orders Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsTaxPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsTax);
        $I->see('Tax Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsInvoicedPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsInvoiced);
        $I->see('Invoice Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsShippingPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsShipping);
        $I->see('Shipping Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsRefundsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsRefunds);
        $I->see('Refunds Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsCouponsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsCoupons);
        $I->see('Coupons Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsPayPalSettlementPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsPayPalSettlement);
        $I->see('PayPal Settlement Reports', '.page-title');
    }

    public function shouldBeOnTheAdminReportsBraintreeSettlementPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsBraintreeSettlement);
        $I->see('Braintree Settlement Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsOrderTotalPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsOrderTotal);
        $I->see('Order Total Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsOrderCountPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsOrderCount);
        $I->see('Order Count Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsNewPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsNew);
        $I->see('New Accounts Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsViewsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsViews);
        $I->see('Product Views Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsBestsellersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsBestsellers);
        $I->see('Bestsellers Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsLowStockPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsLowStock);
        $I->see('Low Stock Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsOrderedPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsOrdered);
        $I->see('Ordered Products Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportsDownloadsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsDownloads);
        $I->see('Downloads Report', '.page-title');
    }

    public function shouldBeOnTheAdminReportRefreshStatisticsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReportsRefreshStatistics);
        $I->see('Refresh Statistics', '.page-title');
    }

    // Stores
    public function shouldBeOnTheAdminStoresAllStoresPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresAllStores);
        $I->see('Stores', '.page-title');
    }

    public function shouldBeOnTheAdminStoresConfigurationPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresConfiguration);
        $I->see('Configuration', '.page-title');
    }

    public function shouldBeOnTheAdminStoresTermsAndConditionsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresTermsAndConditions);
        $I->see('Terms and Conditions', '.page-title');
    }

    public function shouldBeOnTheAdminStoresOrderStatusPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresOrderStatus);
        $I->see('Order Status', '.page-title');
    }

    public function shouldBeOnTheAdminStoresTaxRulesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresTaxRules);
        $I->see('Tax Rules', '.page-title');
    }

    public function shouldBeOnTheAdminStoresTaxZonesAndRatesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresTaxZonesAndRates);
        $I->see('Tax Zones and Rates', '.page-title');
    }

    public function shouldBeOnTheAdminStoresCurrencyRatesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresCurrencyRates);
        $I->see('Currency Rates', '.page-title');
    }

    public function shouldBeOnTheAdminStoresCurrencySymbolsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresCurrencySymbols);
        $I->see('Currency Symbols', '.page-title');
    }

    public function shouldBeOnTheAdminStoresProductPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresProduct);
        $I->see('Product Attributes', '.page-title');
    }

    public function shouldBeOnTheAdminStoresAttributeSetPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresAttributeSet);
        $I->see('Attribute Sets', '.page-title');
    }

    public function shouldBeOnTheAdminStoresRatingPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresRating);
        $I->see('Ratings', '.page-title');
    }

    public function shouldBeOnTheAdminStoresCustomerGroupsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoresCustomerGroups);
        $I->see('Customer Groups', '.page-title');
    }

    // System
    public function shouldBeOnTheAdminSystemImportPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemImport);
        $I->see('Import', '.page-title');
    }

    public function shouldBeOnTheAdminSystemExportPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemExport);
        $I->see('Export', '.page-title');
    }

    public function shouldBeOnTheAdminSystemImportExportTaxRatesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemImportExportTaxRates);
        $I->see('Import and Export Tax Rates', '.page-title');
    }

    public function shouldBeOnTheAdminSystemImportHistoryPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemImportHistory);
        $I->see('Import History', '.page-title');
    }

    public function shouldBeOnTheAdminSystemIntegrationsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemIntegrations);
        $I->see('Integrations', '.page-title');
    }

    public function shouldBeOnTheAdminSystemCacheManagementPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemCacheManagement);
        $I->see('Cache Management', '.page-title');
    }

    public function shouldBeOnTheAdminSystemBackupsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemBackups);
        $I->see('Backups', '.page-title');
    }

    public function shouldBeOnTheAdminSystemIndexManagementPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemIndexManagement);
        $I->see('Index Management', '.page-title');
    }

    public function shouldBeOnTheAdminSystemWebSetupWizardPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemWebSetupWizard);
        $I->see('Setup Wizard', '.page-title');
    }

    public function shouldBeOnTheAdminSystemAllUsersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemAllUsers);
        $I->see('Users', '.page-title');
    }

    public function shouldBeOnTheAdminSystemLockedUsersPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemLockedUsers);
        $I->see('Locked Users', '.page-title');
    }

    public function shouldBeOnTheAdminSystemUserRolesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemUserRoles);
        $I->see('Roles', '.page-title');
    }

    public function shouldBeOnTheAdminSystemNotificationsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemNotifications);
        $I->see('Notifications', '.page-title');
    }

    public function shouldBeOnTheAdminSystemCustomVariablesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemCustomVariables);
        $I->see('Custom Variables', '.page-title');
    }

    public function shouldBeOnTheAdminSystemManageEncryptionKeyPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSystemManageEncryptionKey);
        $I->see('Encryption Key', '.page-title');
    }

    public function shouldBeOnTheAdminFindPartnersAndExtensionsPage() {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminFindPartnersAndExtensions);
        $I->see('Magento Marketplace', '.page-title');
    }
}
