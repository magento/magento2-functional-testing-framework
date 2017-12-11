<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Magento\FunctionalTestingFramework\Step\Backend;

require_once __DIR__ . '/../../Helper/AdminUrlList.php';

/**
 * Class AdminStep
 * @SuppressWarnings(PHPMD)
 * @codingStandardsIgnoreFile
 */
class AdminStep extends \Magento\FunctionalTestingFramework\AcceptanceTester
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
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminLoginPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminLogoutPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminLogoutPage);
    }

    // Sales
    public function goToTheAdminOrdersGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminOrdersGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminOrderForIdPage($orderId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminOrderByIdPage . $orderId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddOrderPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddOrderPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddOrderForCustomerIdPage($customerId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddOrderForCustomerIdPage . $customerId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminInvoicesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminInvoicesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddInvoiceForOrderIdPage($orderId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddInvoiceForOrderIdPage . $orderId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminShipmentsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminShipmentsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminShipmentForIdPage($shipmentId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminShipmentForIdPage . $shipmentId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminCreditMemosGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCreditMemosGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCreditMemoForIdPage($creditMemoId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCreditMemoForIdPage . $creditMemoId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminBillingAgreementsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminBillingAgreementsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminTransactionsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTransactionsGrid);
        $I->waitForPageLoad();
    }

    // Products
    public function goToTheAdminCatalogPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCatalogGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminProductForIdPage($productId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminProductForIdPage . $productId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddSimpleProductPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddSimpleProductPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddConfigurableProductPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddConfigurableProductPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddGroupedProductPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddGroupedProductPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddVirtualProductPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddVirtualProductPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddBundledProductPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddBundleProductPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddDownloadableProductPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddDownloadableProductPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCategoriesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCategoriesPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCategoryForIdPage($categoryId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCategoryForIdPage . $categoryId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddRootCategoryForStoreIdPage($storeId)
    {
        $I = $this;
        $I->amOnPage(('/admin/catalog/category/add/store/' . $storeId . '/parent/1'));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddSubCategoryForStoreIdPage($storeId)
    {
        $I = $this;
        $I->amOnPage(('/admin/catalog/category/add/store/' . $storeId . '/parent/2'));
        $I->waitForPageLoad();
    }

    // Customers
    public function goToTheAdminAllCustomersGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAllCustomersGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCustomersNowOnlineGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCustomersNowOnlineGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCustomerForIdPage($customerId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCustomerForCustomerIdPage . $customerId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddCustomerPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddCustomerPage);
        $I->waitForPageLoad();
    }

    // Marketing
    public function goToTheAdminCatalogPriceRuleGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCatalogPriceRuleGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCatalogPriceRuleForIdPage($catalogPriceRuleId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCatalogPriceRuleForIdPage . $catalogPriceRuleId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddCatalogPriceRulePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddCatalogPriceRulePage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCartPriceRulesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCartPriceRulesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCartPriceRuleForIdPage($cartPriceRuleId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCartPriceRuleForIdPage . $cartPriceRuleId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddCartPriceRulePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddCartPriceRulePage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminEmailTemplatesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminEmailTemplatesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminEmailTemplateForIdPage($emailTemplateId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminEmailTemplateForIdPage . $emailTemplateId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddEmailTemplatePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddEmailTemplatePage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminNewsletterTemplateGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminNewsletterTemplateGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminNewsletterTemplateByIdPage($newsletterTemplateId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminNewsletterTemplateForIdPage . $newsletterTemplateId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddNewsletterTemplatePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddNewsletterTemplatePage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminNewsletterQueueGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminNewsletterQueueGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminNewsletterSubscribersGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminNewsletterSubscribersGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminURLRewritesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminURLRewritesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminURLRewriteForId($urlRewriteId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminURLRewriteForIdPage . $urlRewriteId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddURLRewritePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddURLRewritePage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminSearchTermsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminSearchTermsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminSearchTermForIdPage($searchTermId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminSearchTermForIdPage . $searchTermId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddSearchTermPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddSearchTermPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminSearchSynonymsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminSearchSynonymsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminSearchSynonymGroupByIdPage($searchSynonymId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminSearchSynonymGroupForIdPage . $searchSynonymId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddSearchSynonymGroupPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddSearchSynonymGroupPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminSiteMapGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminSiteMapGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminSiteMapForIdPage($siteMapId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminSiteMapForIdPage . $siteMapId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddSiteMapPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddSiteMapPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminReviewsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminReviewsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminReviewForIdPage($reviewId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminReviewByIdPage . $reviewId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddReviewPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddReviewPage);
        $I->waitForPageLoad();
    }

    // Content
    public function goToTheAdminPagesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminPagesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminPageForIdPage($pageId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminPageForIdPage . $pageId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddPagePage()
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddPagePage));
        $I->waitForPageLoad();
    }

    public function goToTheAdminBlocksGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminBlocksGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminBlockForIdPage($blockId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminBlockForIdPage . $blockId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddBlockPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddBlockPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminWidgetsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminWidgetsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddWidgetPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddWidgetPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminDesignConfigurationGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminDesignConfigurationGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminThemesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminThemesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminThemeByIdPage($themeId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminThemeByIdPage . $themeId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminStoreContentScheduleGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminStoreContentScheduleGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminStoreContentScheduleForIdPage($storeContentScheduleId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminStoreContentScheduleForIdPage . $storeContentScheduleId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddStoreDesignChangePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddStoreDesignChangePage);
        $I->waitForPageLoad();
    }

    // Reports
    public function goToTheAdminProductsInCartGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminProductsInCartGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminSearchTermsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminSearchTermsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAbandonedCartsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAbandonedCartsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminNewsletterProblemsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminNewsletterProblemsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCustomerReviewsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCustomerReviewsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminProductReviewsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminProductReviewsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminProductReviewsForProductIdPage($productId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminProductReviewsForProductIdPage . $productId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminProductReviewIdForProductIdPage($productReviewId, $productId)
    {
        $I = $this;
        $I->amOnPage(('/admin/review/product/edit/id/' . $productReviewId . '/productId/' . $productId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminOrdersReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminOrdersReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminTaxReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTaxReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminInvoiceReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminInvoiceReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminShippingReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminShippingReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminRefundsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminRefundsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCouponsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCouponsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminPayPalSettlementReportsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminPayPalSettlementReportsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminBraintreeSettlementReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminBraintreeSettlementReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminOrderTotalReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminOrderTotalReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminOrderCountReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminOrderCountReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminNewAccountsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminNewAccountsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminProductViewsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminProductViewsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminBestsellersReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminBestsellersReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminLowStockReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminLowStockReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminOrderedProductsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminOrderedProductsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminDownloadsReportGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminDownloadsReportGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminRefreshStatisticsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminRefreshStatisticsGrid);
        $I->waitForPageLoad();
    }

    // Stores
    public function goToTheAdminAllStoresGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAllStoresGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCreateStoreViewPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCreateStoreViewPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCreateStorePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCreateStorePage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCreateWebsitePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCreateWebsitePage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminWebsiteForIdPage($websiteId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminWebsiteByIdPage . $websiteId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminStoreViewForIdPage($storeViewId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminStoreViewByIdPage . $storeViewId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminStoreForIdPage($storeId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminStoreByIdPage . $storeId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminConfigurationGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminConfigurationGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminTermsAndConditionsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTermsAndConditionsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminTermsAndConditionForIdPage($termsAndConditionsId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTermsAndConditionByIdPage . $termsAndConditionsId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddNewTermsAndConditionsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddNewTermsAndConditionPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminOrderStatusGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminOrderStatusGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddOrderStatusPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddOrderStatusPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminTaxRulesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTaxRulesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminTaxRuleForIdPage($taxRuleId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTaxRuleByIdPage . $taxRuleId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddTaxRulePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddTaxRulePage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminTaxZonesAndRatesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTaxZonesAndRatesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminTaxZoneAndRateForIdPage($taxZoneAndRateId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTaxZoneAndRateByIdPage . $taxZoneAndRateId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddTaxZoneAndRatePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddTaxZoneAndRatePage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCurrencyRatesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCurrencyRatesPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCurrencySymbolsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCurrencySymbolsPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminProductAttributesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminProductAttributesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminProductAttributeForIdPage($productAttributeId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminProductAttributeForIdPage . $productAttributeId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddProductAttributePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddProductAttributePage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAttributeSetGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAttributeSetsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAttributeSetByIdPage($attributeSetId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAttributeSetByIdPage . $attributeSetId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddAttributeSetPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddAttributeSetPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminRatingGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminRatingsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminRatingForIdPage($ratingId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminRatingForIdPage . $ratingId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddRatingPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddRatingPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCustomerGroupsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCustomerGroupsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCustomerGroupForIdPage($customerGroupId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCustomerGroupByIdPage . $customerGroupId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddCustomerGroupPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddCustomerGroupPage);
        $I->waitForPageLoad();
    }

    // System
    public function goToTheAdminImportPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminImportPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminExportPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminExportPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminImportAndExportTaxRatesPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminImportAndExportTaxRatesPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminImportHistoryGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminImportHistoryGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminIntegrationsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminIntegrationsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminIntegrationForIdPage($integrationId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminIntegrationByIdPage . $integrationId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddIntegrationPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddIntegrationPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCacheManagementGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCacheManagementGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminBackupsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminBackupsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminIndexManagementGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminIndexManagementGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminWebSetupWizardPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminWebSetupWizardPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminAllUsersGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAllUsersGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminUserForIdPage($userId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminUserByIdPage . $userId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddUserPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddNewUserPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminLockedUsersGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminLockedUsersGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminUserRolesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminUserRolesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminUserRoleForIdPage($userRoleId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminUserRoleByIdPage . $userRoleId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddUserRolePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddUserRolePage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminNotificationsGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminNotificationsGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCustomVariablesGrid()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCustomVariablesGrid);
        $I->waitForPageLoad();
    }

    public function goToTheAdminCustomVariableForId($customVariableId)
    {
        $I = $this;
        $I->amOnPage((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCustomVariableByIdPage . $customVariableId));
        $I->waitForPageLoad();
    }

    public function goToTheAdminAddCustomVariablePage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddCustomVariablePage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminEncryptionKeyPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminEncryptionKeyPage);
        $I->waitForPageLoad();
    }

    public function goToTheAdminFindPartnersAndExtensionsPage()
    {
        $I = $this;
        $I->amOnPage(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminFindPartnersAndExtensions);
        $I->waitForPageLoad();
    }

    // Key Admin Pages
    public function shouldBeOnTheAdminLoginPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminLoginPage);
    }

    public function shouldBeOnTheAdminDashboardPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminDashboardPage);
        $I->see('Dashboard', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminForgotYourPasswordPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminForgotYourPasswordPage);
    }

    // Sales
    public function shouldBeOnTheAdminOrdersGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminOrdersGrid);
        $I->see('Orders', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminOrderForIdPage($orderId)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminOrderByIdPage . $orderId));
        $I->see($orderId, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddOrderPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddOrderPage);
        $I->see('Create New Order', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddOrderForCustomerIdPage($customerId)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddOrderForCustomerIdPage . $customerId));
        $I->see('Create New Order', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminInvoicesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminInvoicesGrid);
        $I->see('Invoices', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddInvoiceForOrderIdPage($orderId)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddInvoiceForOrderIdPage . $orderId));
        $I->see('New Invoice', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminShipmentsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminShipmentsGrid);
        $I->see('Shipments', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminShipmentForIdPage($shipmentId)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminShipmentForIdPage . $shipmentId));
        $I->see('New Shipment');
    }

    public function shouldBeOnTheAdminCreditMemosGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCreditMemosGrid);
        $I->see('Credit Memos', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCreditMemoForIdPage($creditMemoId)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCreditMemoForIdPage . $creditMemoId));
        $I->see('View Memo', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminBillingAgreementsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminBillingAgreementsGrid);
        $I->see('Billing Agreements', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminTransactionsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTransactionsGrid);
        $I->see('Transactions', self::$adminPageTitle);
    }

    // Products
    public function shouldBeOnTheAdminCatalogGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCatalogGrid);
        $I->see('Catalog', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminProductForIdPage($productId, $productName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminProductForIdPage . $productId));
        $I->see($productName, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddSimpleProductPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddSimpleProductPage);
        $I->see('New Product', self::$adminPageTitle);
    }
    
    public function shouldBeOnTheAdminAddConfigurableProductPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddConfigurableProductPage);
        $I->see('New Product', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddGroupedProductPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddGroupedProductPage);
        $I->see('New Product', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddVirtualProductPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddVirtualProductPage);
        $I->see('New Product', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddBundledProductPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddBundleProductPage);
        $I->see('New Product', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddDownloadableProductPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddDownloadableProductPage);
        $I->see('New Product', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCategoriesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCategoriesPage);
        $I->see('Default Category', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCategoryForIdPage($categoryId, $categoryName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCategoryForIdPage . $categoryId));
        $I->see($categoryName, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddRootCategoryForStoreIdPage($storeId)
    {
        $I = $this;
        $I->seeInCurrentUrl(('/admin/catalog/category/add/store/' . $storeId . '/parent/1'));
        $I->see('New Category', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddSubCategoryForStoreIdPage($storeId)
    {
        $I = $this;
        $I->seeInCurrentUrl(('/admin/catalog/category/add/store/' . $storeId . '/parent/2'));
        $I->see('New Category', self::$adminPageTitle);
    }

    // Customers
    public function shouldBeOnTheAdminAllCustomersGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAllCustomersGrid);
        $I->see('Customers', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCustomersNowOnlineGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCustomersNowOnlineGrid);
        $I->see('Customers Now Online', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCustomerForIdPage($customerId, $customerName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCustomerForCustomerIdPage . $customerId));
        $I->see($customerName, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddCustomerPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddCustomerPage);
        $I->see('New Customer', self::$adminPageTitle);
    }

    // Marketing
    public function shouldBeOnTheAdminCatalogPriceRuleGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCatalogPriceRuleGrid);
        $I->see('Catalog Price Rule', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCatalogPriceRuleForIdPage($catalogPriceRuleId, $catalogPriceRuleName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCatalogPriceRuleForIdPage . $catalogPriceRuleId));
        $I->see($catalogPriceRuleName, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddCatalogPriceRulePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddCatalogPriceRulePage);
        $I->see('New Catalog Price Rule', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCartPriceRulesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCartPriceRulesGrid);
        $I->see('Cart Price Rules', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCartPriceRuleForIdPage($cartPriceRuleId, $cartPriceRuleName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCartPriceRuleForIdPage . $cartPriceRuleId));
        $I->see($cartPriceRuleName, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddCartPriceRulePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddCartPriceRulePage);
        $I->see('New Cart Price Rule', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminEmailTemplatesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminEmailTemplatesGrid);
        $I->see('Email Templates', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminEmailTemplateForIdPage($emailTemplateId, $templateName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminEmailTemplateForIdPage . $emailTemplateId));
        $I->see($templateName, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddEmailTemplatePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddEmailTemplatePage);
        $I->see('New Template', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminNewsletterTemplateGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminNewsletterTemplateGrid);
        $I->see('Newsletter Templates', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminNewsletterTemplateByIdPage($newsletterTemplateId, $templateName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminNewsletterTemplateForIdPage . $newsletterTemplateId));
        $I->see($templateName, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddNewsletterTemplatePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddNewsletterTemplatePage);
        $I->see('New Template', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminNewsletterQueueGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminNewsletterQueueGrid);
        $I->see('Newsletter Queue', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminNewsletterSubscribersGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminNewsletterSubscribersGrid);
        $I->see('Newsletter Subscribers', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminURLRewritesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminURLRewritesGrid);
        $I->see('URL Rewrites', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminURLRewriteForId($urlRewriteId)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminURLRewriteForIdPage . $urlRewriteId));
        $I->see('Edit URL Rewrite for a', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddURLRewritePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddURLRewritePage);
        $I->see('Add New URL Rewrite', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminSearchTermsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminSearchTermsGrid);
        $I->see('Search Terms', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminSearchTermForIdPage($searchTermId, $searchQuery)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminSearchTermForIdPage . $searchTermId));
        $I->see($searchQuery, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddSearchTermPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddSearchTermPage);
        $I->see('New Search', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminSearchSynonymsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminSearchSynonymsGrid);
        $I->see('Search Synonyms', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminSearchSynonymGroupByIdPage($searchSynonymId, $synonyms)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminSearchSynonymGroupForIdPage . $searchSynonymId));
        $I->see($synonyms, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddSearchSynonymGroupPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddSearchSynonymGroupPage);
        $I->see('New Synonym Group', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminSiteMapGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminSiteMapGrid);
        $I->see('Site Map', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminSiteMapForIdPage($siteMapId, $fileName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminSiteMapForIdPage . $siteMapId));
        $I->see($fileName, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddSiteMapPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddSiteMapPage);
        $I->see('New Site Map', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminReviewsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminReviewsGrid);
        $I->see('Reviews', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminReviewForIdPage($reviewId)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminReviewByIdPage . $reviewId));
        $I->see('Edit Review', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddReviewPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddReviewPage);
        $I->see('New Review', self::$adminPageTitle);
    }

    // Content
    public function shouldBeOnTheAdminPagesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminPagesGrid);
        $I->see('Pages', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminPageForIdPage($pageId, $pageTitle)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminPageForIdPage . $pageId));
        $I->see($pageTitle, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddPagePage()
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddPagePage));
        $I->see('New Page', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminBlocksGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminBlocksGrid);
        $I->see('Blocks', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminBlockForIdPage($blockId, $blockTitle)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminBlockForIdPage . $blockId));
        $I->see($blockTitle, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddBlockPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddBlockPage);
        $I->see('New Block', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminWidgetsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminWidgetsGrid);
        $I->see('Widgets', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddWidgetPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddWidgetPage);
        $I->see('Widgets', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminDesignConfigurationGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminDesignConfigurationGrid);
        $I->see('Design Configuration', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminThemesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminThemesGrid);
        $I->see('Themes', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminThemeByIdPage($themeId, $themeTitle)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminThemeByIdPage . $themeId));
        $I->see($themeTitle);
    }

    public function shouldBeOnTheAdminStoreContentScheduleGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminStoreContentScheduleGrid);
        $I->see('Store Design Schedule', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminStoreContentScheduleForIdPage($storeContentScheduleId)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminStoreContentScheduleForIdPage . $storeContentScheduleId));
        $I->see('Edit Store Design Change', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddStoreDesignChangePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddStoreDesignChangePage);
        $I->see('New Store Design Change');
    }

    // Reports
    public function shouldBeOnTheAdminProductsInCartGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminProductsInCartGrid);
        $I->see('Products in Carts', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminSearchTermsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminSearchTermsReportGrid);
        $I->see('Search Terms Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAbandonedCartsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAbandonedCartsGrid);
        $I->see('Abandoned Carts', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminNewsletterProblemsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminNewsletterProblemsReportGrid);
        $I->see('Newsletter Problems Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCustomerReviewsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCustomerReviewsReportGrid);
        $I->see('Customer Reviews Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminProductReviewsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminProductReviewsReportGrid);
        $I->see('Product Reviews Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminProductReviewsForProductIdPage($productId)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminProductReviewsForProductIdPage . $productId));
        $I->see('Reviews', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminProductReviewIdForProductIdPage($productReviewId, $productId)
    {
        $I = $this;
        $I->seeInCurrentUrl(('/admin/review/product/edit/id/' . $productReviewId . '/productId/' . $productId));
        $I->see('Edit Review', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminOrdersReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminOrdersReportGrid);
        $I->see('Orders Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminTaxReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTaxReportGrid);
        $I->see('Tax Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminInvoiceReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminInvoiceReportGrid);
        $I->see('Invoice Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminShippingReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminShippingReportGrid);
        $I->see('Shipping Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminRefundsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminRefundsReportGrid);
        $I->see('Refunds Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCouponsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCouponsReportGrid);
        $I->see('Coupons Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminPayPalSettlementReportsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminPayPalSettlementReportsGrid);
        $I->see('PayPal Settlement Reports', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminBraintreeSettlementReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminBraintreeSettlementReportGrid);
        $I->see('Braintree Settlement Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminOrderTotalReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminOrderTotalReportGrid);
        $I->see('Order Total Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminOrderCountReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminOrderCountReportGrid);
        $I->see('Order Count Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminNewAccountsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminNewAccountsReportGrid);
        $I->see('New Accounts Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminProductViewsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminProductViewsReportGrid);
        $I->see('Product Views Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminBestsellersReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminBestsellersReportGrid);
        $I->see('Bestsellers Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminLowStockReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminLowStockReportGrid);
        $I->see('Low Stock Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminOrderedProductsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminOrderedProductsReportGrid);
        $I->see('Ordered Products Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminDownloadsReportGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminDownloadsReportGrid);
        $I->see('Downloads Report', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminRefreshStatisticsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminRefreshStatisticsGrid);
        $I->see('Refresh Statistics', self::$adminPageTitle);
    }

    // Stores
    public function shouldBeOnTheAdminAllStoresGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAllStoresGrid);
        $I->see('Stores', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCreateStoreViewPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCreateStoreViewPage);
        $I->see('Stores', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCreateStorePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCreateStorePage);
        $I->see('Stores', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCreateWebsitePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCreateWebsitePage);
        $I->see('Stores');
    }

    public function shouldBeOnTheAdminWebsiteForIdPage($websiteId)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminWebsiteByIdPage . $websiteId));
    }

    public function shouldBeOnTheAdminStoreViewForIdPage($storeViewId)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminStoreViewByIdPage . $storeViewId));
    }

    public function shouldBeOnTheAdminStoreForIdPage($storeId)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminStoreByIdPage . $storeId));
    }

    public function shouldBeOnTheAdminConfigurationGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminConfigurationGrid);
        $I->see('Configuration', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminTermsAndConditionsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTermsAndConditionsGrid);
        $I->see('Terms and Conditions', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminTermsAndConditionForIdPage($termsAndConditionsId, $conditionName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTermsAndConditionByIdPage . $termsAndConditionsId));
        $I->see($conditionName, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddNewTermsAndConditionsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddNewTermsAndConditionPage);
        $I->see('New Condition', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminOrderStatusGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminOrderStatusGrid);
        $I->see('Order Status', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddOrderStatusPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddOrderStatusPage);
        $I->see('Create New Order Status', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminTaxRulesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTaxRulesGrid);
        $I->see('Tax Rules', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminTaxRuleForIdPage($taxRuleId, $taxRuleName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTaxRuleByIdPage . $taxRuleId));
        $I->see($taxRuleName, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddTaxRulePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddTaxRulePage);
        $I->see('New Tax Rule', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminTaxZonesAndRatesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTaxZonesAndRatesGrid);
        $I->see('Tax Zones and Rates', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminTaxZoneAndRateForIdPage($taxZoneAndRateId, $taxIdentifier)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminTaxZoneAndRateByIdPage . $taxZoneAndRateId));
        $I->see($taxIdentifier, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddTaxZoneAndRatePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddTaxZoneAndRatePage);
        $I->see('New Tax Rate', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCurrencyRatesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCurrencyRatesPage);
        $I->see('Currency Rates', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCurrencySymbolsPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCurrencySymbolsPage);
        $I->see('Currency Symbols', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminProductAttributesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminProductAttributesGrid);
        $I->see('Product Attributes', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminProductAttributeForIdPage($productAttributeId, $productAttributeDefaultLabel)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminProductAttributeForIdPage . $productAttributeId));
        $I->see($productAttributeDefaultLabel, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddProductAttributePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddProductAttributePage);
        $I->see('New Product Attribute', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAttributeSetsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAttributeSetsGrid);
        $I->see('Attribute Sets', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAttributeSetByIdPage($attributeSetId, $attributeSetName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAttributeSetByIdPage . $attributeSetId));
        $I->see($attributeSetName, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddAttributeSetPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddAttributeSetPage);
        $I->see('New Attribute Set', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminRatingsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminRatingsGrid);
        $I->see('Ratings', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminRatingForIdPage($ratingId, $ratingDefaultValue)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminRatingForIdPage . $ratingId));
        $I->see($ratingDefaultValue, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddRatingPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddRatingPage);
        $I->see('New Rating', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCustomerGroupsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCustomerGroupsGrid);
        $I->see('Customer Groups', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCustomerGroupForIdPage($customerGroupId, $customerGroupName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCustomerGroupByIdPage . $customerGroupId));
        $I->see($customerGroupName, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddCustomerGroupPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddCustomerGroupPage);
        $I->see('New Customer Group', self::$adminPageTitle);
    }

    // System
    public function shouldBeOnTheAdminImportPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminImportPage);
        $I->see('Import', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminExportPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminExportPage);
        $I->see('Export', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminImportAndExportTaxRatesPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminImportAndExportTaxRatesPage);
        $I->see('Import and Export Tax Rates', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminImportHistoryGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminImportHistoryGrid);
        $I->see('Import History', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminIntegrationsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminIntegrationsGrid);
        $I->see('Integrations', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminIntegrationForIdPage($integrationId, $integrationName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminIntegrationByIdPage . $integrationId));
        $I->see('Edit', self::$adminPageTitle);
        $I->see($integrationName, self::$adminPageTitle);
        $I->see('Integration', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddIntegrationPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddIntegrationPage);
        $I->see('New Integration', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCacheManagementGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCacheManagementGrid);
        $I->see('Cache Management', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminBackupsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminBackupsGrid);
        $I->see('Backups', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminIndexManagementGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminIndexManagementGrid);
        $I->see('Index Management', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminWebSetupWizardPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminWebSetupWizardPage);
        $I->see('Setup Wizard', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAllUsersGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAllUsersGrid);
        $I->see('Users', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminUserForIdPage($userId, $userFirstAndLastName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminUserByIdPage . $userId));
        $I->see($userFirstAndLastName, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddUserPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddNewUserPage);
        $I->see('New User', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminLockedUsersGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminLockedUsersGrid);
        $I->see('Locked Users', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminUserRolesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminUserRolesGrid);
        $I->see('Roles', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminUserRoleForIdPage($userRoleId, $userRoleName)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminUserRoleByIdPage . $userRoleId));
        $I->see($userRoleName, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddUserRolePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddUserRolePage);
        $I->see('New Role', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminNotificationsGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminNotificationsGrid);
        $I->see('Notifications', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCustomVariablesGrid()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCustomVariablesGrid);
        $I->see('Custom Variables', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminCustomVariableForId($customVariableId, $customVariableCode)
    {
        $I = $this;
        $I->seeInCurrentUrl((\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminCustomVariableByIdPage . $customVariableId));
        $I->see($customVariableCode, self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminAddCustomVariablePage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminAddCustomVariablePage);
        $I->see('New Custom Variable', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminEncryptionKeyPage()
    {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminEncryptionKeyPage);
        $I->see('Encryption Key', self::$adminPageTitle);
    }

    public function shouldBeOnTheAdminFindPartnersAndExtensionsPage() {
        $I = $this;
        $I->seeInCurrentUrl(\Magento\FunctionalTestingFramework\Helper\AdminUrlList::$adminFindPartnersAndExtensions);
        $I->see('Magento Marketplace', self::$adminPageTitle);
    }
}
