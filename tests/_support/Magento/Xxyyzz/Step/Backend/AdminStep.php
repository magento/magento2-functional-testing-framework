<?php
namespace Magento\Xxyyzz\Step\Backend;

require_once __DIR__ . '/../../Helper/AdminUrlList.php';

class AdminStep extends \Magento\Xxyyzz\AcceptanceTester
{
    public static $adminPageTitle = '.page-title';

    public function openNewTabGoToVerify($url)
    {
        $I = $this;
        $I->openNewTab();
        $I->amOnPage($url);
        $I->waitForPageLoad();
        $I->seeInCurrentUrl($url);
    }

    public function closeNewTab()
    {
        $I = $this;
        $I->closeTab();
    }

    // Key Admin Pages
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
        $I->waitForPageLoad();

        return $admin_url_list[$random_admin_url];
    }

    public function goToTheAdminLoginPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminLoginPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminLogoutPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminLogoutPage);
    }

    // Sales
    public function goToTheAdminOrdersGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminOrdersGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminInvoicesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminInvoicesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminShipmentsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminShipmentsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCreditMemosGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCreditMemosGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminBillingAgreementsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminBillingAgreementsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminTransactionsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminTransactionsGrid);
        $I->waitForPageLoad();
    }

    // Products
    public function goToTheAdminCatalogPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCatalogGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCategoriesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCategoriesPage);
        $I->waitForPageLoad();
    }

    // Customers
    public function goToTheAdminAllCustomersGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminAllCustomersGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCustomersNowOnlineGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCustomersNowOnlineGrid);
        $I->waitForPageLoad();
    }

    // Marketing
    public function goToTheAdminCatalogPriceRuleGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCatalogPriceRuleGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCartPriceRulesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCartPriceRulesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminEmailTemplatesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminEmailTemplatesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminNewsletterTemplateGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminNewsletterTemplateGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminNewsletterQueueGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminNewsletterQueueGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminNewsletterSubscribersGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminNewsletterSubscribersGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminURLRewritesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminURLRewritesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminSearchTermsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSearchTermsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminSearchSynonymsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSearchSynonymsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminSiteMapGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSiteMapGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminReviewsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReviewsGrid);
        $I->waitForPageLoad();
    }

    // Content
    public function goToTheAdminPagesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminPagesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminBlocksGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminBlocksGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminWidgetsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminWidgetsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminDesignConfigurationGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminDesignConfigurationGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminThemesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminThemesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminStoreContentScheduleGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoreContentScheduleGrid);
        $I->waitForPageLoad();
    }

    // Reports
    public function goToTheAdminProductsInCartGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminProductsInCartGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminSearchTermsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSearchTermsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAbandonedCartsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminAbandonedCartsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminNewsletterProblemsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminNewsletterProblemsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCustomerReviewsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCustomerReviewsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminProductReviewsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminProductReviewsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminOrdersReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminOrdersReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminTaxReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminTaxReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminInvoiceReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminInvoiceReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminShippingReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminShippingReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminRefundsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminRefundsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCouponsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCouponsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminPayPalSettlementReportsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminPayPalSettlementReportsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminBraintreeSettlementReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminBraintreeSettlementReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminOrderTotalReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminOrderTotalReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminOrderCountReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminOrderCountReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminNewAccountsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminNewAccountsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminProductViewsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminProductViewsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminBestsellersReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminBestsellersReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminLowStockReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminLowStockReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminOrderedProductsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminOrderedProductsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminDownloadsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminDownloadsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminRefreshStatisticsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminRefreshStatisticsGrid);
        $I->waitForPageLoad();
    }

    // Stores
    public function goToTheAdminAllStoresGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminAllStoresGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminConfigurationGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminConfigurationGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminTermsAndConditionsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminTermsAndConditionsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminOrderStatusGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminOrderStatusGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminTaxRulesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminTaxRulesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminTaxZonesAndRatesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminTaxZonesAndRatesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCurrencyRatesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCurrencyRatesPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCurrencySymbolsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCurrencySymbolsPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminProductAttributesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminProductAttributesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAttributeSetGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminAttributeSetsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminRatingGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminRatingsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCustomerGroupsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCustomerGroupsGrid);
        $I->waitForPageLoad();
    }

    // System
    public function goToTheAdminImportPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminImportPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminExportPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminExportPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminImportAndExportTaxRatesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminImportAndExportTaxRatesPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminImportHistoryGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminImportHistoryGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminIntegrationsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminIntegrationsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCacheManagementGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCacheManagementGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminBackupsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminBackupsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminIndexManagementGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminIndexManagementGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminWebSetupWizardPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminWebSetupWizardPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAllUsersGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminAllUsersGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminLockedUsersGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminLockedUsersGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminUserRolesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminUserRolesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminNotificationsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminNotificationsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCustomVariablesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCustomVariablesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminEncryptionKeyPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminEncryptionKeyPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminFindPartnersAndExtensionsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\Xxyyzz\Helper\AdminUrlList::$adminFindPartnersAndExtensions);
        $I->waitForPageLoad();
    }

    // Key Admin Pages
    public function shouldBeOnTheAdminLoginPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminLoginPage);
    }

    public function shouldBeOnTheAdminDashboardPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminDashboardPage);
        $I->see('Dashboard', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminForgotYourPasswordPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminForgotYourPasswordPage);
    }

    // Sales
    public function shouldBeOnTheAdminOrdersGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminOrdersGrid);
        $I->see('Orders', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminInvoicesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminInvoicesGrid);
        $I->see('Invoices', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminShipmentsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminShipmentsGrid);
        $I->see('Shipments', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCreditMemosGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCreditMemosGrid);
        $I->see('Credit Memos', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminBillingAgreementsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminBillingAgreementsGrid);
        $I->see('Billing Agreements', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminTransactionsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminTransactionsGrid);
        $I->see('Transactions', self::$adminPageTitle);
    }

    // Products
    public function shouldBeOnTheAdminCatalogGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCatalogGrid);
        $I->see('Catalog', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCategoryPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCategoriesPage);
        $I->see('Default Category', self::$adminPageTitle);
    }

    // Customers
    public function shouldBeOnTheAdminAllCustomersGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminAllCustomersGrid);
        $I->see('Customers', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCustomersNowOnlineGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCustomersNowOnlineGrid);
        $I->see('Customers Now Online', self::$adminPageTitle);
    }

    // Marketing
    public function shouldBeOnTheAdminCatalogPriceRuleGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCatalogPriceRuleGrid);
        $I->see('Catalog Price Rule', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCartPriceRulesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCartPriceRulesGrid);
        $I->see('Cart Price Rules', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminEmailTemplatesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminEmailTemplatesGrid);
        $I->see('Email Templates', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminNewsletterTemplateGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminNewsletterTemplateGrid);
        $I->see('Newsletter Templates', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminNewsletterQueueGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminNewsletterQueueGrid);
        $I->see('Newsletter Queue', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminNewsletterSubscribersGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminNewsletterSubscribersGrid);
        $I->see('Newsletter Subscribers', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminURLRewritesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminURLRewritesGrid);
        $I->see('URL Rewrites', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminSearchTermsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSearchTermsGrid);
        $I->see('Search Terms', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminSearchSynonymsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSearchSynonymsGrid);
        $I->see('Search Synonyms', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminSiteMapGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSiteMapGrid);
        $I->see('Site Map', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminReviewsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminReviewsGrid);
        $I->see('Reviews', self::$adminPageTitle);
    }

    // Content
    public function shouldBeOnTheAdminPagesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminPagesGrid);
        $I->see('Pages', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminBlocksGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminBlocksGrid);
        $I->see('Blocks', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminWidgetsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminWidgetsGrid);
        $I->see('Widgets', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminDesignConfigurationGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminDesignConfigurationGrid);
        $I->see('Design Configuration', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminThemesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminThemesGrid);
        $I->see('Themes', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminStoreContentScheduleGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminStoreContentScheduleGrid);
        $I->see('Store Design Schedule', self::$adminPageTitle);
    }

    // Reports
    public function shouldBeOnTheAdminProductsInCartGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminProductsInCartGrid);
        $I->see('Products in Carts', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminSearchTermsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminSearchTermsReportGrid);
        $I->see('Search Terms Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAbandonedCartsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminAbandonedCartsGrid);
        $I->see('Abandoned Carts', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminNewsletterProblemsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminNewsletterProblemsReportGrid);
        $I->see('Newsletter Problems Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCustomerReviewsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCustomerReviewsReportGrid);
        $I->see('Customer Reviews Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminProductReviewsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminProductReviewsReportGrid);
        $I->see('Product Reviews Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminOrdersReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminOrdersReportGrid);
        $I->see('Orders Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminTaxReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminTaxReportGrid);
        $I->see('Tax Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminInvoiceReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminInvoiceReportGrid);
        $I->see('Invoice Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminShippingReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminShippingReportGrid);
        $I->see('Shipping Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminRefundsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminRefundsReportGrid);
        $I->see('Refunds Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCouponsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCouponsReportGrid);
        $I->see('Coupons Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminPayPalSettlementReportsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminPayPalSettlementReportsGrid);
        $I->see('PayPal Settlement Reports', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminBraintreeSettlementReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminBraintreeSettlementReportGrid);
        $I->see('Braintree Settlement Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminOrderTotalReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminOrderTotalReportGrid);
        $I->see('Order Total Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminOrderCountReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminOrderCountReportGrid);
        $I->see('Order Count Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminNewAccountsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminNewAccountsReportGrid);
        $I->see('New Accounts Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminProductViewsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminProductViewsReportGrid);
        $I->see('Product Views Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminBestsellersReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminBestsellersReportGrid);
        $I->see('Bestsellers Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminLowStockReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminLowStockReportGrid);
        $I->see('Low Stock Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminOrderedProductsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminOrderedProductsReportGrid);
        $I->see('Ordered Products Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminDownloadsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminDownloadsReportGrid);
        $I->see('Downloads Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminRefreshStatisticsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminRefreshStatisticsGrid);
        $I->see('Refresh Statistics', self::$adminPageTitle);
    }

    // Stores
    public function shouldBeOnTheAdminAllStoresGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminAllStoresGrid);
        $I->see('Stores', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminConfigurationGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminConfigurationGrid);
        $I->see('Configuration', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminTermsAndConditionsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminTermsAndConditionsGrid);
        $I->see('Terms and Conditions', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminOrderStatusGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminOrderStatusGrid);
        $I->see('Order Status', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminTaxRulesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminTaxRulesGrid);
        $I->see('Tax Rules', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminTaxZonesAndRatesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminTaxZonesAndRatesGrid);
        $I->see('Tax Zones and Rates', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCurrencyRatesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCurrencyRatesPage);
        $I->see('Currency Rates', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCurrencySymbolsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCurrencySymbolsPage);
        $I->see('Currency Symbols', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminProductAttributesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminProductAttributesGrid);
        $I->see('Product Attributes', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAttributeSetsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminAttributeSetsGrid);
        $I->see('Attribute Sets', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminRatingsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminRatingsGrid);
        $I->see('Ratings', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCustomerGroupsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCustomerGroupsGrid);
        $I->see('Customer Groups', self::$adminPageTitle);
    }

    // System
    public function shouldBeOnTheAdminImportPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminImportPage);
        $I->see('Import', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminExportPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminExportPage);
        $I->see('Export', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminImportAndExportTaxRatesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminImportAndExportTaxRatesPage);
        $I->see('Import and Export Tax Rates', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminImportHistoryGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminImportHistoryGrid);
        $I->see('Import History', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminIntegrationsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminIntegrationsGrid);
        $I->see('Integrations', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCacheManagementGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCacheManagementGrid);
        $I->see('Cache Management', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminBackupsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminBackupsGrid);
        $I->see('Backups', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminIndexManagementGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminIndexManagementGrid);
        $I->see('Index Management', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminWebSetupWizardPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminWebSetupWizardPage);
        $I->see('Setup Wizard', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAllUsersGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminAllUsersGrid);
        $I->see('Users', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminLockedUsersGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminLockedUsersGrid);
        $I->see('Locked Users', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminUserRolesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminUserRolesGrid);
        $I->see('Roles', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminNotificationsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminNotificationsGrid);
        $I->see('Notifications', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCustomVariablesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminCustomVariablesGrid);
        $I->see('Custom Variables', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminEncryptionKeyPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminEncryptionKeyPage);
        $I->see('Encryption Key', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminFindPartnersAndExtensionsPage() {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\Xxyyzz\Helper\AdminUrlList::$adminFindPartnersAndExtensions);
        $I->see('Magento Marketplace', self::$adminPageTitle);
    }
}
