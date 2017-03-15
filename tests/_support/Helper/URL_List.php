<?php
namespace Page\Acceptance;

class AdminURLList
{
    public static $adminLogin                           = '/admin/admin/';
    public static $adminLogout                          = '/admin/admin/auth/logout/';
    public static $adminForgotYourPassword              = '/admin/admin/auth/forgotpassword/';

    public static $adminDashboard                       = '/admin/admin/dashboard/';

    public static $adminSalesOrders                     = '/admin/sales/order/';
    public static $adminSalesInvoices                   = '/admin/sales/invoice/';
    public static $adminSalesShipments                  = '/admin/sales/shipment/';
    public static $adminSalesCreditMemos                = '/admin/sales/creditmemo/';
    public static $adminSalesBillingAgreements          = '/admin/paypal/billing_agreement/';
    public static $adminSalesTransactions               = '/admin/sales/transactions/';

    public static $adminProductsCatalog                 = '/admin/catalog/product/';
    public static $adminProductsCategories              = '/admin/catalog/category/';

    public static $adminCustomersAllCustomers           = '/admin/customer/index/';
    public static $adminCustomersNowOnline              = '/admin/customer/online/';

    public static $adminMarketingCatalogPriceRule       = '/admin/catalog_rule/promo_catalog/';
    public static $adminMarketingCartPriceRules         = '/admin/sales_rule/promo_quote/';
    public static $adminMarketingEmailTemplates         = '/admin/admin/email_template/';
    public static $adminMarketingNewsletterTemplate     = '/admin/newsletter/template/';
    public static $adminMarketingNewsletterQueue        = '/admin/newsletter/queue/';
    public static $adminMarketingNewsletterSubscribers  = '/admin/newsletter/subscriber/';
    public static $adminMarketingURLRewrites            = '/admin/admin/url_rewrite/index/';
    public static $adminMarketingSearchTerms            = '/admin/search/term/index/';
    public static $adminMarketingSearchSynonyms         = '/admin/search/synonyms/index/';
    public static $adminMarketingSiteMap                = '/admin/admin/sitemap/';
    public static $adminMarketingReviews                = '/admin/review/product/index/';

    public static $adminContentPages                    = '/admin/cms/page/';
    public static $adminContentBlocks                   = '/admin/cms/block/';
    public static $adminContentWidgets                  = '/admin/admin/widget_instance/';
    public static $adminContentConfiguration            = '/admin/theme/design_config/';
    public static $adminContentThemes                   = '/admin/admin/system_design_theme/';
    public static $adminContentSchedule                 = '/admin/admin/system_design/';

    public static $adminReportsProductsInCart           = '/admin/reports/report_shopcart/product/';
    public static $adminReportsSearchTerms              = '/admin/search/term/report/';
    public static $adminReportsAbandonedCArts           = '/admin/reports/report_shopcart/abandoned/';
    public static $adminReportsNewsletterProblemReports = '/admin/newsletter/problem/';
    public static $adminReportsByCustomers              = '/admin/reports/report_review/customer/';
    public static $adminReportsByProducts               = '/admin/reports/report_review/product/';
    public static $adminReportsOrders                   = '/admin/reports/report_sales/sales/';
    public static $adminReportsTax                      = '/admin/reports/report_sales/tax/';
    public static $adminReportsInvoiced                 = '/admin/reports/report_sales/invoiced/';
    public static $adminReportsShipping                 = '/admin/reports/report_sales/shipping/';
    public static $adminReportsRefunds                  = '/admin/reports/report_sales/refunded/';
    public static $adminReportsCoupons                  = '/admin/reports/report_sales/coupons/';
    public static $adminReportsPayPalSettlement         = '/admin/paypal/paypal_reports/';
    public static $adminReportsBraintreeSettlement      = '/admin/braintree/report/';
    public static $adminReportsOrderTotal               = '/admin/reports/report_customer/totals/';
    public static $adminReportsOrderCount               = '/admin/reports/report_customer/orders/';
    public static $adminReportsNew                      = '/admin/reports/report_customer/accounts/';
    public static $adminReportsViews                    = '/admin/reports/report_product/viewed/';
    public static $adminReportsBestsellers              = '/admin/reports/report_sales/bestsellers/';
    public static $adminReportsLowStock                 = '/admin/reports/report_product/lowstock/';
    public static $adminReportsOrdered                  = '/admin/reports/report_product/sold/';
    public static $adminReportsDownloads                = '/admin/reports/report_product/downloads/';
    public static $adminReportsRefreshStatistics        = '/admin/reports/report_statistics/';

    public static $adminStoresAllStores                 = '/admin/admin/system_store/';
    public static $adminStoresConfiguration             = '/admin/admin/system_config/';
    public static $adminStoresTermsAndConditions        = '/admin/checkout/agreement/';
    public static $adminStoresOrderStatus               = '/admin/sales/order_status/';
    public static $adminStoresTaxRules                  = '/admin/tax/rule/';
    public static $adminStoresTaxZonesAndRates          = '/admin/tax/rate/';
    public static $adminStoresCurrencyRates             = '/admin/admin/system_currency/';
    public static $adminStoresCurrencySymbols           = '/admin/admin/system_currencysymbol/';
    public static $adminStoresProduct                   = '/admin/catalog/product_attribute/';
    public static $adminStoresAttributeSet              = '/admin/catalog/product_set/';
    public static $adminStoresRating                    = '/admin/review/rating/';
    public static $adminStoresCustomerGroups            = '/admin/customer/group/';

    public static $adminSystemImport                    = '/admin/admin/import/';
    public static $adminSystemExport                    = '/admin/admin/export/';
    public static $adminSystemImportExportTaxRates      = '/admin/tax/rate/importExport/';
    public static $adminSystemImportHistory             = '/admin/admin/history/';
    public static $adminSystemIntegrations              = '/admin/admin/integration/';
    public static $adminSystemCacheManagement           = '/admin/admin/cache/';
    public static $adminSystemBackups                   = '/admin/backup/index/';
    public static $adminSystemIndexManagement           = '/admin/indexer/indexer/list/';
    public static $adminSystemWebSetupWizard            = '/setup/#/home';
    public static $adminSystemAllUsers                  = '/admin/admin/user/';
    public static $adminSystemLockedUsers               = '/admin/admin/locks/';
    public static $adminSystemUserRoles                 = '/admin/admin/user_role/';
    public static $adminSystemNotifications             = '/admin/admin/notification/';
    public static $adminSystemCustomVariables           = '/admin/admin/system_variable/';
    public static $adminSystemManageEncryptionKey       = '/admin/admin/crypt_key/';

    public static $adminFindPartnersAndExtensions       = '/admin/marketplace/index/';
}