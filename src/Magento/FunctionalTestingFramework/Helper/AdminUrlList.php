<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Helper;

/**
 * Class AdminUrlList
 * @SuppressWarnings(PHPMD)
 */
// @codingStandardsIgnoreFile
class AdminUrlList
{
    public static $adminLoginPage                         = '/admin/admin/';
    public static $adminLogoutPage                        = '/admin/admin/auth/logout/';
    public static $adminForgotYourPasswordPage            = '/admin/admin/auth/forgotpassword/';

    public static $adminDashboardPage                     = '/admin/admin/dashboard/';

    public static $adminOrdersGrid                        = '/admin/sales/order/';
    public static $adminOrderByIdPage                     = '/admin/sales/order/view/order_id/';
    public static $adminAddOrderPage                      = '/admin/sales/order_create/index/';
    public static $adminAddOrderForCustomerIdPage         = '/admin/sales/order_create/index/customer_id/';
    public static $adminInvoicesGrid                      = '/admin/sales/invoice/';
    public static $adminAddInvoiceForOrderIdPage          = '/admin/sales/order_invoice/new/order_id/';
    public static $adminShipmentsGrid                     = '/admin/sales/shipment/';
    public static $adminShipmentForIdPage                 = '/admin/sales/shipment/view/shipment_id/';
    public static $adminCreditMemosGrid                   = '/admin/sales/creditmemo/';
    public static $adminCreditMemoForIdPage               = '/admin/sales/creditmemo/view/creditmemo_id/';
    public static $adminBillingAgreementsGrid             = '/admin/paypal/billing_agreement/';
    // TODO: Determine the correct address for Billing Agreements for Billing Agreement ID page URL.
    public static $adminTransactionsGrid                  = '/admin/sales/transactions/';
    // TODO: Determine the correct address for Transactions for Transaction ID page URL.

    public static $adminCatalogGrid                       = '/admin/catalog/product/';
    public static $adminProductForIdPage                  = '/admin/catalog/product/edit/id/';
    public static $adminAddSimpleProductPage              = '/admin/catalog/product/new/set/4/type/simple/';
    public static $adminAddConfigurableProductPage        = '/admin/catalog/product/new/set/4/type/configurable/';
    public static $adminAddGroupedProductPage             = '/admin/catalog/product/new/set/4/type/grouped/';
    public static $adminAddVirtualProductPage             = '/admin/catalog/product/new/set/4/type/virtual/';
    public static $adminAddBundleProductPage              = '/admin/catalog/product/new/set/4/type/bundle/';
    public static $adminAddDownloadableProductPage        = '/admin/catalog/product/new/set/4/type/downloadable/';

    public static $adminCategoriesPage                    = '/admin/catalog/category/';
    public static $adminCategoryForIdPage                 = '/admin/catalog/category/edit/id/';
    public static $adminAddRootCategoryPage               = '/admin/catalog/category/add/store/0/parent/1';
    public static $adminAddSubCategoryPage                = '/admin/catalog/category/add/store/0/parent/2';

    public static $adminAllCustomersGrid                  = '/admin/customer/index/';
    public static $adminCustomersNowOnlineGrid            = '/admin/customer/online/';
    public static $adminCustomerForCustomerIdPage         = '/admin/customer/index/edit/id/';
    public static $adminAddCustomerPage                   = '/admin/customer/index/new/';

    public static $adminCatalogPriceRuleGrid              = '/admin/catalog_rule/promo_catalog/';
    public static $adminCatalogPriceRuleForIdPage         = '/admin/catalog_rule/promo_catalog/edit/id/';
    public static $adminAddCatalogPriceRulePage           = '/admin/catalog_rule/promo_catalog/new/';
    public static $adminCartPriceRulesGrid                = '/admin/sales_rule/promo_quote/';
    public static $adminCartPriceRuleForIdPage            = '/admin/sales_rule/promo_quote/edit/id/';
    public static $adminAddCartPriceRulePage              = '/admin/sales_rule/promo_quote/new/';
    public static $adminEmailTemplatesGrid                = '/admin/admin/email_template/';
    public static $adminEmailTemplateForIdPage            = '/admin/admin/email_template/edit/id/';
    public static $adminAddEmailTemplatePage              = '/admin/admin/email_template/new/';
    public static $adminNewsletterTemplateGrid            = '/admin/newsletter/template/';
    public static $adminNewsletterTemplateForIdPage       = '/admin/newsletter/template/edit/id/';
    public static $adminAddNewsletterTemplatePage         = '/admin/newsletter/template/new/';
    public static $adminNewsletterQueueGrid               = '/admin/newsletter/queue/';
    // TODO: Determine if there is a Details page for the Newsletter Queue.
    public static $adminNewsletterSubscribersGrid         = '/admin/newsletter/subscriber/';
    public static $adminURLRewritesGrid                   = '/admin/admin/url_rewrite/index/';
    public static $adminURLRewriteForIdPage               = '/admin/admin/url_rewrite/edit/id/';
    public static $adminAddURLRewritePage                 = '/admin/admin/url_rewrite/edit/id'; // If you don't list an ID it drops you on the Add page.
    public static $adminSearchTermsGrid                   = '/admin/search/term/index/';
    public static $adminSearchTermForIdPage               = '/admin/search/term/edit/id/';
    public static $adminAddSearchTermPage                 = '/admin/search/term/new/';
    public static $adminSearchSynonymsGrid                = '/admin/search/synonyms/index/';
    public static $adminSearchSynonymGroupForIdPage       = '/admin/search/synonyms/edit/group_id/';
    public static $adminAddSearchSynonymGroupPage         = '/admin/search/synonyms/new/';
    public static $adminSiteMapGrid                       = '/admin/admin/sitemap/';
    public static $adminSiteMapForIdPage                  = '/admin/admin/sitemap/edit/sitemap_id/';
    public static $adminAddSiteMapPage                    = '/admin/admin/sitemap/new/';
    public static $adminReviewsGrid                       = '/admin/review/product/index/';
    public static $adminReviewByIdPage                    = '/admin/review/product/edit/id/';
    public static $adminAddReviewPage                     = '/admin/review/product/new/';

    public static $adminPagesGrid                         = '/admin/cms/page/';
    public static $adminPageForIdPage                     = '/admin/cms/page/edit/page_id/';
    public static $adminAddPagePage                       = '/admin/cms/page/new/';
    public static $adminBlocksGrid                        = '/admin/cms/block/';
    public static $adminBlockForIdPage                    = '/admin/cms/block/edit/block_id/';
    public static $adminAddBlockPage                      = '/admin/cms/block/new/';
    public static $adminWidgetsGrid                       = '/admin/admin/widget_instance/';
    // TODO: Determine how the Edit Widget URLs are generated.
    public static $adminAddWidgetPage                     = '/admin/admin/widget_instance/new/';
    public static $adminDesignConfigurationGrid           = '/admin/theme/design_config/';
    // TODO: Determine how the Design Configuration URLs are generated.
    public static $adminThemesGrid                        = '/admin/admin/system_design_theme/';
    public static $adminThemeByIdPage                     = '/admin/admin/system_design_theme/edit/id/';
    public static $adminStoreContentScheduleGrid          = '/admin/admin/system_design/';
    public static $adminStoreContentScheduleForIdPage     = '/admin/admin/system_design/edit/id/';
    public static $adminAddStoreDesignChangePage          = '/admin/admin/system_design/new/';

    public static $adminProductsInCartGrid                = '/admin/reports/report_shopcart/product/';
    public static $adminSearchTermsReportGrid             = '/admin/search/term/report/';
    public static $adminAbandonedCartsGrid                = '/admin/reports/report_shopcart/abandoned/';
    public static $adminNewsletterProblemsReportGrid      = '/admin/newsletter/problem/';
    public static $adminCustomerReviewsReportGrid         = '/admin/reports/report_review/customer/';
    public static $adminProductReviewsReportGrid          = '/admin/reports/report_review/product/';
    public static $adminProductReviewsForProductIdPage    = '/admin/review/product/index/productId/';
    public static $adminOrdersReportGrid                  = '/admin/reports/report_sales/sales/';
    public static $adminTaxReportGrid                     = '/admin/reports/report_sales/tax/';
    public static $adminInvoiceReportGrid                 = '/admin/reports/report_sales/invoiced/';
    public static $adminShippingReportGrid                = '/admin/reports/report_sales/shipping/';
    public static $adminRefundsReportGrid                 = '/admin/reports/report_sales/refunded/';
    public static $adminCouponsReportGrid                 = '/admin/reports/report_sales/coupons/';
    public static $adminPayPalSettlementReportsGrid       = '/admin/paypal/paypal_reports/';
    public static $adminBraintreeSettlementReportGrid     = '/admin/braintree/report/';
    public static $adminOrderTotalReportGrid              = '/admin/reports/report_customer/totals/';
    public static $adminOrderCountReportGrid              = '/admin/reports/report_customer/orders/';
    public static $adminNewAccountsReportGrid             = '/admin/reports/report_customer/accounts/';
    public static $adminProductViewsReportGrid            = '/admin/reports/report_product/viewed/';
    public static $adminBestsellersReportGrid             = '/admin/reports/report_sales/bestsellers/';
    public static $adminLowStockReportGrid                = '/admin/reports/report_product/lowstock/';
    public static $adminOrderedProductsReportGrid         = '/admin/reports/report_product/sold/';
    public static $adminDownloadsReportGrid               = '/admin/reports/report_product/downloads/';
    public static $adminRefreshStatisticsGrid             = '/admin/reports/report_statistics/';

    public static $adminAllStoresGrid                     = '/admin/admin/system_store/';
    public static $adminCreateStoreViewPage               = '/admin/admin/system_store/newStore/';
    public static $adminCreateStorePage                   = '/admin/admin/system_store/newGroup/';
    public static $adminCreateWebsitePage                 = '/admin/admin/system_store/newWebsite/';
    public static $adminWebsiteByIdPage                   = '/admin/admin/system_store/editWebsite/website_id/';
    public static $adminStoreViewByIdPage                 = '/admin/admin/system_store/editStore/store_id/';
    public static $adminStoreByIdPage                     = '/admin/admin/system_store/editGroup/group_id/';
    public static $adminConfigurationGrid                 = '/admin/admin/system_config/';
    public static $adminTermsAndConditionsGrid            = '/admin/checkout/agreement/';
    public static $adminTermsAndConditionByIdPage         = '/admin/checkout/agreement/edit/id/';
    public static $adminAddNewTermsAndConditionPage       = '/admin/checkout/agreement/new/';
    public static $adminOrderStatusGrid                   = '/admin/sales/order_status/';
    public static $adminAddOrderStatusPage                = '/admin/sales/order_status/new/';
    // TODO: Determine how the Order Status URLs are generated.
    public static $adminTaxRulesGrid                      = '/admin/tax/rule/';
    public static $adminTaxRuleByIdPage                   = '/admin/tax/rule/edit/rule/';
    public static $adminAddTaxRulePage                    = '/admin/tax/rule/new/';
    public static $adminTaxZonesAndRatesGrid              = '/admin/tax/rate/';
    public static $adminTaxZoneAndRateByIdPage            = '/admin/tax/rate/edit/rate/';
    public static $adminAddTaxZoneAndRatePage             = '/admin/tax/rate/add/';
    public static $adminCurrencyRatesPage                 = '/admin/admin/system_currency/';
    public static $adminCurrencySymbolsPage               = '/admin/admin/system_currencysymbol/';
    public static $adminProductAttributesGrid             = '/admin/catalog/product_attribute/';
    public static $adminProductAttributeForIdPage         = '/admin/catalog/product_attribute/edit/attribute_id/';
    public static $adminAddProductAttributePage           = '/admin/catalog/product_attribute/new/';
    public static $adminAttributeSetsGrid                 = '/admin/catalog/product_set/';
    public static $adminAttributeSetByIdPage              = '/admin/catalog/product_set/edit/id/';
    public static $adminAddAttributeSetPage               = '/admin/catalog/product_set/add/';
    public static $adminRatingsGrid                       = '/admin/review/rating/';
    public static $adminRatingForIdPage                   = '/admin/review/rating/edit/id/';
    public static $adminAddRatingPage                     = '/admin/review/rating/new/';
    public static $adminCustomerGroupsGrid                = '/admin/customer/group/';
    public static $adminCustomerGroupByIdPage             = '/admin/customer/group/edit/id/';
    public static $adminAddCustomerGroupPage              = '/admin/customer/group/new/';

    public static $adminImportPage                        = '/admin/admin/import/';
    public static $adminExportPage                        = '/admin/admin/export/';
    public static $adminImportAndExportTaxRatesPage       = '/admin/tax/rate/importExport/';
    public static $adminImportHistoryGrid                 = '/admin/admin/history/';
    public static $adminIntegrationsGrid                  = '/admin/admin/integration/';
    public static $adminIntegrationByIdPage               = '/admin/admin/integration/edit/id/';
    public static $adminAddIntegrationPage                = '/admin/admin/integration/new/';
    public static $adminCacheManagementGrid               = '/admin/admin/cache/';
    public static $adminBackupsGrid                       = '/admin/backup/index/';
    public static $adminIndexManagementGrid               = '/admin/indexer/indexer/list/';
    public static $adminWebSetupWizardPage                = '/setup/';
    public static $adminAllUsersGrid                      = '/admin/admin/user/';
    public static $adminUserByIdPage                      = '/admin/admin/user/edit/user_id/';
    public static $adminAddNewUserPage                    = '/admin/admin/user/new/';
    public static $adminLockedUsersGrid                   = '/admin/admin/locks/';
    public static $adminUserRolesGrid                     = '/admin/admin/user_role/';
    public static $adminUserRoleByIdPage                  = '/admin/admin/user_role/editrole/rid/';
    public static $adminAddUserRolePage                   = '/admin/admin/user_role/editrole/';
    public static $adminNotificationsGrid                 = '/admin/admin/notification/';
    public static $adminCustomVariablesGrid               = '/admin/admin/system_variable/';
    public static $adminCustomVariableByIdPage            = '/admin/admin/system_variable/edit/variable_id/';
    public static $adminAddCustomVariablePage             = '/admin/admin/system_variable/new/';
    public static $adminEncryptionKeyPage                 = '/admin/admin/crypt_key/';

    public static $adminFindPartnersAndExtensions         = '/admin/marketplace/index/';
}