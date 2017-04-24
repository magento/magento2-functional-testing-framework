<?php
namespace Magento\Xxyyzz\Page\Backend;

use Magento\Xxyyzz\Page\AbstractAdminPage;

class AdminSideNavigation extends AbstractAdminPage
{
    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $dashboardButton                                 = '#menu-magento-backend-dashboard';
    public static $salesButton                                     = '#menu-magento-sales-sales';
    public static $productsButton                                  = '#menu-magento-catalog-catalog';
    public static $customersButton                                 = '#menu-magento-customer-customer';
    public static $marketingButton                                 = '#menu-magento-backend-marketing';
    public static $contentButton                                   = '#menu-magento-backend-content';
    public static $reportsButton                                   = '#menu-magento-reports-report';
    public static $storesButton                                    = '#menu-magento-backend-stores';
    public static $systemButton                                    = '#menu-magento-backend-system';
    public static $findPartnersExtensionsButton                    = '#menu-magento-marketplace-partners';

    public static $salesNavMainArea                                = '.item-sales-operation.parent';
    public static $salesNavTitle                                   = '.item-sales-operation .submenu-group-title';
    public static $salesNavOrders                                  = '.item-sales-order';
    public static $salesNavInvoices                                = '.item-sales-invoice';
    public static $salesNavShipments                               = '.item-sales-shipment';
    public static $salesNavCreditMemos                             = '.item-sales-creditmemo';
    public static $salesNavBillingAgreements                       = '.item-paypal-billing-agreement';
    public static $salesNavTransactions                            = '.item-sales-transactions';

    public static $productNavMainArea                              = '.item-inventory.parent';
    public static $productNavTitle                                 = '.item-inventory .submenu-group-title';
    public static $productNavCatalog                               = '.item-catalog-products';
    public static $productNavCategories                            = '.item-catalog-categories';

    public static $customersNavAllCustomers                        = '.item-customer-manage';
    public static $customersNavNowOnline                           = '.item-customer-online';

    public static $marketingNavPromotionsMainArea                  = '.item-promo.parent';
    public static $marketingNavPromotionsTitle                     = '.item-promo .submenu-group-title';
    public static $marketingNavPromotionsCatalogPriceRule          = '.item-promo-catalog';
    public static $marketingNavPromotionsCartPriceRules            = '.item-promo-quote';

    public static $marketingNavCommunicationsMainArea              = '.item-marketing-communications.parent';
    public static $marketingNavCommunicationsTitle                 = '.item-marketing-communications .submenu-group-title';
    public static $marketingNavCommunicationsEmailTemplates        = '.item-template';
    public static $marketingNavCommunicationsNewsletterTemplates   = '.item-newsletter-template';
    public static $marketingNavCommunicationsNewsletterQueue       = '.item-newsletter-queue';
    public static $marketingNavCommunicationsNewsletterSubscribers = '.item-newsletter-subscriber';

    public static $marketingNavSEOSearchMainArea                   = '.item-marketing-seo.parent';
    public static $marketingNavSEOSearchTitle                      = '.item-marketing-seo .submenu-group-title';
    public static $marketingNavSEOSearchURLRewrites                = '.item-urlrewrite';
    public static $marketingNavSEOSearchTerms                      = '.item-search-terms';
    public static $marketingNavSEOSearchSynonyms                   = '.item-search-synonyms';
    public static $marketingNavSEOSearchSiteMap                    = '.item-catalog-sitemap';

    public static $marketingNavUserContentMainArea                 = '.item-marketing-user-content.parent';
    public static $marketingNavUserContentTitle                    = '.item-marketing-user-content .submenu-group-title';
    public static $marketingNavUserContentReviews                  = '.item-catalog-reviews-ratings-reviews-all';

    public static $contentNavElementsMainArea                      = '.item-content-elements.parent';
    public static $contentNavElementsTitle                         = '.item-content-elements .submenu-group-title';
    public static $contentNavElementsPages                         = '.item-cms-page';
    public static $contentNavElementsBlocks                        = '.item-cms-block';
    public static $contentNavElementsWidgets                       = '.item-cms-widget-instance';

    public static $contentNavDesignMainArea                        = '.item-system-design.parent';
    public static $contentNavDesignTitle                           = '.item-system-design .submenu-group-title';
    public static $contentNavDesignConfiguration                   = '.item-design-config';
    public static $contentNavDesignThemes                          = '.item-system-design-theme';
    public static $contentNavDesignSchedule                        = '.item-system-design-schedule';

    public static $reportsNavMarketingMainArea                     = '.item-report-marketing.parent';
    public static $reportsNavMarketingTitle                        = '.item-report-marketing .submenu-group-title';
    public static $reportsNavMarketingProductsInCart               = '.item-report-shopcart-product';
    public static $reportsNavMarketingSearchTerms                  = '.item-report-search-term';
    public static $reportsNavMarketingAbandonedCarts               = '.item-report-shopcart-abandoned';
    public static $reportsNavMarketingNewsletterProblemReports     = '.item-newsletter-problem';

    public static $reportsNavReviewsMainArea                       = '.item-report-review.parent';
    public static $reportsNavReviewsTitle                          = '.item-report-review .submenu-group-title';
    public static $reportsNavReviewsByCustomers                    = '.item-report-review-customer';
    public static $reportsNavReviewsByProducts                     = '.item-report-review-product';

    public static $reportsNavSalesMainArea                         = '.item-report-salesroot.parent';
    public static $reportsNavSalesTitle                            = '.item-report-salesroot .submenu-group-title';
    public static $reportsNavSalesOrders                           = '.item-report-salesroot-sales';
    public static $reportsNavSalesTax                              = '.item-report-salesroot-tax';
    public static $reportsNavSalesInvoiced                         = '.item-report-salesroot-invoiced';
    public static $reportsNavSalesShipping                         = '.item-report-salesroot-shipping';
    public static $reportsNavSalesRefunds                          = '.item-report-salesroot-refunded';
    public static $reportsNavSalesCoupons                          = '.item-report-salesroot-coupons';
    public static $reportsNavSalesPayPalSettlement                 = '.item-report-salesroot-paypal-settlement-reports';
    public static $reportsNavSalesBraintreeSettlement              = '.item-settlement-report';

    public static $reportsNavCustomersMainArea                     = '.item-report-customers.parent';
    public static $reportsNavCustomersTitle                        = '.item-report-customers .submenu-group-title';
    public static $reportsNavCustomersOrderTotal                   = '.item-report-customers-totals';
    public static $reportsNavCustomersOrderCount                   = '.item-report-customers-orders';
    public static $reportsNavCustomersNew                          = '.item-report-customers-accounts';

    public static $reportsNavProductsMainArea                      = '.item-report-products.parent';
    public static $reportsNavProductsTitle                         = '.item-report-products .submenu-group-title';
    public static $reportsNavProductsViews                         = '.item-report-products-viewed';
    public static $reportsNavProductsBestsellers                   = '.item-report-products-bestsellers';
    public static $reportsNavProductsLowStock                      = '.item-report-products-lowstock';
    public static $reportsNavProductsOrdered                       = '.item-report-products-sold';
    public static $reportsNavProductsDownloads                     = '.item-report-products-downloads';

    public static $reportsNavStatisticsMainArea                    = '.item-report-statistics.parent';
    public static $reportsNavStatisticsTitle                       = '.item-report-statistics .submenu-group-title';
    public static $reportsNavStatisticsRefreshStatistics           = '.item-report-statistics-refresh';

    public static $storesNavSettingsMainArea                       = '.item-stores-settings.parent';
    public static $storesNavSettingsTitle                          = '.item-stores-settings .submenu-group-title';
    public static $storesNavSettingsAllStores                      = '.item-system-store';
    public static $storesNavSettingsConfiguration                  = '.item-system-config';
    public static $storesNavSettingsTermsAndConditions             = '.item-sales-checkoutagreement';
    public static $storesNavSettingsOrderStatus                    = '.item-system-order-statuses';

    public static $storesNavTaxesMainArea                          = '.item-sales-tax.parent';
    public static $storesNavTaxesTitle                             = '.item-sales-tax .submenu-group-title';
    public static $storesNavTaxesTaxRules                          = '.item-sales-tax-rules';
    public static $storesNavTaxesTaxZonesAndRates                  = '.item-sales-tax-rates';

    public static $storesNavCurrencyMainArea                       = '.item-system-currency.parent';
    public static $storesNavCurrencyTitle                          = '.item-system-currency .submenu-group-title';
    public static $storesNavCurrencyRates                          = '.item-system-currency-rates';
    public static $storesNavCurrencySymbols                        = '.item-system-currency-symbols';

    public static $storesNavAttributesMainArea                     = '.item-stores-attributes.parent';
    public static $storesNavAttributesTitle                        = '.item-stores-attributes .submenu-group-title';
    public static $storesNavAttributesProduct                      = '.item-catalog-attributes-attributes';
    public static $storesNavAttributesSet                          = '.item-catalog-attributes-sets';
    public static $storesNavAttributesRating                       = '.item-catalog-reviews-ratings-ratings';

    public static $storesNavOtherSettingsMainArea                  = '.item-other-settings.parent';
    public static $storesNavOtherSettingsTitle                     = '.item-other-settings .submenu-group-title';
    public static $storesNavOtherSettingsCustomerGroups            = '.item-customer-group';

    public static $systemNavDataTransferMainArea                   = '.item-system-convert.parent';
    public static $systemNavDataTransferTitle                      = '.item-system-convert .submenu-group-title';
    public static $systemNavDataTransferImport                     = '.item-system-convert-import';
    public static $systemNavDataTransferExport                     = '.item-system-convert-export';
    public static $systemNavDataTransferImportExportTaxRates       = '.item-system-convert-tax';
    public static $systemNavDataTransferImportHistory              = '.item-system-convert-history';

    public static $systemNavExtensionsMainArea                     = '.item-system-extensions';
    public static $systemNavExtensionsTitle                        = '.item-system-extensions .submenu-group-title';
    public static $systemNavExtensionsIntegrations                 = '.item-system-integrations';

    public static $systemNavToolsMainArea                          = '.item-system-tools.parent';
    public static $systemNavToolsTitle                             = '.item-system-tools .submenu-group-title';
    public static $systemNavToolsCacheManagement                   = '.item-system-cache';
    public static $systemNavToolsBackups                           = '.item-system-tools-backup';
    public static $systemNavToolsIndexManagement                   = '.item-system-index';
    public static $systemNavToolsWebSetupWizard                    = '.item-setup-wizard';

    public static $systemNavPermissionsMainArea                    = '.item-system-acl.parent';
    public static $systemNavPermissionsTitle                       = '.item-system-acl .submenu-group-title';
    public static $systemNavPermissionsAllUsers                    = '.item-system-acl-users';
    public static $systemNavPermissionsLockedUsers                 = '.item-system-acl-locks';
    public static $systemNavPermissionsUserRoles                   = '.item-system-acl-roles';

    public static $systemNavOtherSettingsMainArea                  = '.item-system-other-settings.parent';
    public static $systemNavOtherSettingsTitle                     = '.item-system-other-settings .submenu-group-title';
    public static $systemNavOtherSettingsNotifications             = '.item-system-adminnotification';
    public static $systemNavOtherSettingsCustomVariables           = '.item-system-variable';
    public static $systemNavOtherSettingsManageEncryptionKey       = '.item-system-crypt-key';
    
    public function clickOnDashboardInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$dashboardButton);
        $I->waitForPageLoad();
    }

    public function clickOnSalesInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$salesButton);
        $I->waitForPageLoad();
    }

    public function clickOnOrdersInTheSalesNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$salesNavOrders);
        $I->waitForPageLoad();
    }

    public function clickOnInvoicesInTheSalesNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$salesNavInvoices);
        $I->waitForPageLoad();
    }

    public function clickOnShipmentsInTheSalesNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$salesNavShipments);
        $I->waitForPageLoad();
    }

    public function clickOnCreditMemosInTheSalesNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$salesNavCreditMemos);
        $I->waitForPageLoad();
    }

    public function clickOnBillingAgreementsInTheSalesNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$salesNavBillingAgreements);
        $I->waitForPageLoad();
    }

    public function clickOnTransactionsInTheSalesNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$salesNavTransactions);
        $I->waitForPageLoad();
    }

    public function clickOnProductsInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$productsButton);
        $I->waitForPageLoad();
    }

    public function clickOnCatalogInTheProductNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$productNavCatalog);
        $I->waitForPageLoad();
    }

    public function clickOnCategoriesInTheProductNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$productNavCategories);
        $I->waitForPageLoad();
    }

    public function clickOnCustomersInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$customersButton);
        $I->waitForPageLoad();
    }

    public function clickOnAllCustomersInTheCustomersNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$customersNavAllCustomers);
        $I->waitForPageLoad();
    }

    public function clickOnNowOnlineInTheCustomersNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$customersNavNowOnline);
        $I->waitForPageLoad();
    }

    public function clickOnMarketingInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingButton);
        $I->waitForPageLoad();
    }

    public function clickOnCatalogPriceRulesInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavPromotionsCatalogPriceRule);
        $I->waitForPageLoad();
    }

    public function clickOnCartPriceRulesInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavPromotionsCartPriceRules);
        $I->waitForPageLoad();
    }

    public function clickOnEmailTemplatesInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavCommunicationsEmailTemplates);
        $I->waitForPageLoad();
    }

    public function clickOnNewsletterTemplatesInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavCommunicationsNewsletterTemplates);
        $I->waitForPageLoad();
    }

    public function clickOnNewsletterQueueInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavCommunicationsNewsletterQueue);
        $I->waitForPageLoad();
    }

    public function clickOnNewsletterSubscribersInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavCommunicationsNewsletterSubscribers);
        $I->waitForPageLoad();
    }

    public function clickOnURLRewritesInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavSEOSearchURLRewrites);
        $I->waitForPageLoad();
    }

    public function clickOnSearchTermsInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavSEOSearchTerms);
        $I->waitForPageLoad();
    }

    public function clickOnSearchSynonymsInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavSEOSearchSynonyms);
        $I->waitForPageLoad();
    }

    public function clickOnSiteMapInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavSEOSearchSiteMap);
        $I->waitForPageLoad();
    }

    public function clickOnContentReviewsInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavUserContentReviews);
        $I->waitForPageLoad();
    }

    public function clickOnContentInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contentButton);
        $I->waitForPageLoad();
    }

    public function clickOnPagesInTheContentNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contentNavElementsPages);
        $I->waitForPageLoad();
    }

    public function clickOnBlocksInTheContentNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contentNavElementsBlocks);
        $I->waitForPageLoad();
    }

    public function clickOnWidgetsInTheContentNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contentNavElementsWidgets);
        $I->waitForPageLoad();
    }

    public function clickOnConfigurationInTheContentNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contentNavDesignConfiguration);
        $I->waitForPageLoad();
    }

    public function clickOnThemesInTheContentNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contentNavDesignThemes);
        $I->waitForPageLoad();
    }

    public function clickOnScheduleInTheContentNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contentNavDesignSchedule);
        $I->waitForPageLoad();
    }

    public function clickOnReportsInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsButton);
        $I->waitForPageLoad();
    }

    public function clickOnProductsInCartInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavMarketingProductsInCart);
        $I->waitForPageLoad();
    }

    public function clickOnSearchTermsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavMarketingSearchTerms);
        $I->waitForPageLoad();
    }

    public function clickOnAbandonedCartsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavMarketingAbandonedCarts);
        $I->waitForPageLoad();
    }

    public function clickOnNewsletterProblemReportsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavMarketingNewsletterProblemReports);
        $I->waitForPageLoad();
    }

    public function clickOnByCustomersInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavReviewsByCustomers);
        $I->waitForPageLoad();
    }

    public function clickOnByProductsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavReviewsByProducts);
        $I->waitForPageLoad();
    }

    public function clickOnOrdersInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesOrders);
        $I->waitForPageLoad();
    }

    public function clickOTaxInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesTax);
        $I->waitForPageLoad();
    }

    public function clickOnInvoicedInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesInvoiced);
        $I->waitForPageLoad();
    }

    public function clickOnShippingInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesShipping);
        $I->waitForPageLoad();
    }

    public function clickOnRefundsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesRefunds);
        $I->waitForPageLoad();
    }

    public function clickOnCouponsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesCoupons);
        $I->waitForPageLoad();
    }

    public function clickOnPayPalSettlementInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesPayPalSettlement);
        $I->waitForPageLoad();
    }

    public function clickOnBraintreeSettlementInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesBraintreeSettlement);
        $I->waitForPageLoad();
    }

    public function clickOnOrderTotalInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavCustomersOrderTotal);
        $I->waitForPageLoad();
    }

    public function clickOnOrderCountInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavCustomersOrderCount);
        $I->waitForPageLoad();
    }

    public function clickOnNewInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavCustomersNew);
        $I->waitForPageLoad();
    }

    public function clickOnViewsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavProductsViews);
        $I->waitForPageLoad();
    }

    public function clickOnBestSellersInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavProductsBestsellers);
        $I->waitForPageLoad();
    }

    public function clickOnLowStockInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavProductsLowStock);
        $I->waitForPageLoad();
    }

    public function clickOnOrderedInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavProductsOrdered);
        $I->waitForPageLoad();
    }

    public function clickOnDownloadsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavProductsDownloads);
        $I->waitForPageLoad();
    }

    public function clickOnRefreshStatisticsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavStatisticsRefreshStatistics);
        $I->waitForPageLoad();
    }

    public function clickOnStoresInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesButton);
        $I->waitForPageLoad();
    }

    public function clickOnAllStoresInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavSettingsAllStores);
        $I->waitForPageLoad();
    }

    public function clickOnConfigurationInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavSettingsConfiguration);
        $I->waitForPageLoad();
    }

    public function clickOnTermsAndConditionsInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavSettingsTermsAndConditions);
        $I->waitForPageLoad();
    }

    public function clickOnOrderStatusInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavSettingsOrderStatus);
        $I->waitForPageLoad();
    }

    public function clickOnTaxRuleInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavTaxesTaxRules);
        $I->waitForPageLoad();
    }

    public function clickOnTaxZonesAndRatesInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavTaxesTaxZonesAndRates);
        $I->waitForPageLoad();
    }

    public function clickOnTaxRatesInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavCurrencyRates);
        $I->waitForPageLoad();
    }

    public function clickOnTaxSymbolsInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavCurrencySymbols);
        $I->waitForPageLoad();
    }

    public function clickOnCurrencyRatesInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavCurrencyRates);
        $I->waitForPageLoad();
    }

    public function clickOnCurrencySymbolsInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavCurrencySymbols);
        $I->waitForPageLoad();
    }

    public function clickOnProductInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavAttributesProduct);
        $I->waitForPageLoad();
    }

    public function clickOnAttributesSetInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavAttributesSet);
        $I->waitForPageLoad();
    }

    public function clickOnRatingsInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavAttributesRating);
        $I->waitForPageLoad();
    }

    public function clickOnCustomerGroupInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavOtherSettingsCustomerGroups);
        $I->waitForPageLoad();
    }

    public function clickOnSystemInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemButton);
        $I->waitForPageLoad();
    }

    public function clickOnImportInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavDataTransferImport);
        $I->waitForPageLoad();
    }

    public function clickOnExportInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavDataTransferExport);
        $I->waitForPageLoad();
    }

    public function clickOnImportExportTaxRatesInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavDataTransferImportExportTaxRates);
        $I->waitForPageLoad();
    }

    public function clickOnImportHistoryInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavDataTransferImportHistory);
        $I->waitForPageLoad();
    }

    public function clickOnIntegrationsInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavExtensionsIntegrations);
        $I->waitForPageLoad();
    }

    public function clickOnCacheManagementInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavToolsCacheManagement);
        $I->waitForPageLoad();
    }

    public function clickOnBackupsInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavToolsBackups);
        $I->waitForPageLoad();
    }

    public function clickOnIndexManagementInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavToolsIndexManagement);
        $I->waitForPageLoad();
    }

    public function clickOnWebSetupWizardInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavToolsWebSetupWizard);
        $I->waitForLoadingMaskToDisappear();
    }

    public function clickOnAllUsersInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavPermissionsAllUsers);
        $I->waitForPageLoad();
    }

    public function clickOnLockedUsersInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavPermissionsLockedUsers);
        $I->waitForPageLoad();
    }

    public function clickOnUserRolesInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavPermissionsUserRoles);
        $I->waitForPageLoad();
    }

    public function clickOnNotificationsInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavOtherSettingsNotifications);
        $I->waitForPageLoad();
    }

    public function clickOnCustomVariablesInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavOtherSettingsCustomVariables);
        $I->waitForPageLoad();
    }

    public function clickOnManageEncryptionKeyInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavOtherSettingsManageEncryptionKey);
        $I->waitForPageLoad();
    }

    public function clickOnFindPartnersAndExtensionsInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$findPartnersExtensionsButton);
        $I->wait(1);
    }

    public function shouldSeeTheSalesNavMainArea()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$salesNavMainArea);
    }

    public function shouldSeeTheSalesNavTitle()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$salesNavTitle);
    }

    public function shouldSeeTheSalesNavOrders()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$salesNavOrders);
    }

    public function shouldSeeTheSalesNavInvoices()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$salesNavInvoices);
    }

    public function shouldSeeTheSalesNavShipments()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$salesNavShipments);
    }

    public function shouldSeeTheSalesNavCreditMemos()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$salesNavCreditMemos);
    }

    public function shouldSeeTheSalesNavBillingAgreements()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$salesNavBillingAgreements);
    }

    public function shouldSeeTheSalesNavTransactions()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$salesNavTransactions);
    }

    public function shouldSeeTheSalesNavMenu()
    {
        $this->shouldSeeTheSalesNavMainArea();
        $this->shouldSeeTheSalesNavTitle();
        $this->shouldSeeTheSalesNavOrders();
        $this->shouldSeeTheSalesNavInvoices();
        $this->shouldSeeTheSalesNavShipments();
        $this->shouldSeeTheSalesNavCreditMemos();
        $this->shouldSeeTheSalesNavBillingAgreements();
        $this->shouldSeeTheSalesNavTransactions();
    }

    public function shouldSeeTheProductNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$productNavMainArea);
        $I->seeElement(self::$productNavTitle);
        $I->seeElement(self::$productNavCatalog);
        $I->seeElement(self::$productNavCategories);
    }

    public function shouldSeeTheCustomersNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$customersNavAllCustomers);
        $I->seeElement(self::$customersNavNowOnline);
    }

    public function shouldSeeTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$marketingNavPromotionsMainArea);
        $I->seeElement(self::$marketingNavPromotionsTitle);
        $I->seeElement(self::$marketingNavPromotionsCatalogPriceRule);
        $I->seeElement(self::$marketingNavPromotionsCartPriceRules);

        $I->seeElement(self::$marketingNavCommunicationsMainArea);
        $I->seeElement(self::$marketingNavCommunicationsTitle);
        $I->seeElement(self::$marketingNavCommunicationsEmailTemplates);
        $I->seeElement(self::$marketingNavCommunicationsNewsletterTemplates);
        $I->seeElement(self::$marketingNavCommunicationsNewsletterQueue);
        $I->seeElement(self::$marketingNavCommunicationsNewsletterSubscribers);

        $I->seeElement(self::$marketingNavSEOSearchMainArea);
        $I->seeElement(self::$marketingNavSEOSearchTitle);
        $I->seeElement(self::$marketingNavSEOSearchURLRewrites);
        $I->seeElement(self::$marketingNavSEOSearchTerms);
        $I->seeElement(self::$marketingNavSEOSearchSynonyms);
        $I->seeElement(self::$marketingNavSEOSearchSiteMap);

        $I->seeElement(self::$marketingNavUserContentMainArea);
        $I->seeElement(self::$marketingNavUserContentTitle);
        $I->seeElement(self::$marketingNavUserContentReviews);
    }

    public function shouldSeeTheContentNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$contentNavElementsMainArea);
        $I->seeElement(self::$contentNavElementsTitle);
        $I->seeElement(self::$contentNavElementsPages);
        $I->seeElement(self::$contentNavElementsBlocks);
        $I->seeElement(self::$contentNavElementsWidgets);

        $I->seeElement(self::$contentNavDesignMainArea);
        $I->seeElement(self::$contentNavDesignTitle);
        $I->seeElement(self::$contentNavDesignConfiguration);
        $I->seeElement(self::$contentNavDesignThemes);
        $I->seeElement(self::$contentNavDesignSchedule);
    }

    public function shouldSeeTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$reportsNavMarketingMainArea);
        $I->seeElement(self::$reportsNavMarketingTitle);
        $I->seeElement(self::$reportsNavMarketingProductsInCart);
        $I->seeElement(self::$reportsNavMarketingSearchTerms);
        $I->seeElement(self::$reportsNavMarketingAbandonedCarts);
        $I->seeElement(self::$reportsNavMarketingNewsletterProblemReports);

        $I->seeElement(self::$reportsNavReviewsMainArea);
        $I->seeElement(self::$reportsNavReviewsTitle);
        $I->seeElement(self::$reportsNavReviewsByCustomers);
        $I->seeElement(self::$reportsNavReviewsByProducts);

        $I->seeElement(self::$reportsNavSalesMainArea);
        $I->seeElement(self::$reportsNavSalesTitle);
        $I->seeElement(self::$reportsNavSalesOrders);
        $I->seeElement(self::$reportsNavSalesTax);
        $I->seeElement(self::$reportsNavSalesInvoiced);
        $I->seeElement(self::$reportsNavSalesShipping);
        $I->seeElement(self::$reportsNavSalesRefunds);
        $I->seeElement(self::$reportsNavSalesCoupons);
        $I->seeElement(self::$reportsNavSalesPayPalSettlement);
        $I->seeElement(self::$reportsNavSalesBraintreeSettlement);

        $I->seeElement(self::$reportsNavCustomersMainArea);
        $I->seeElement(self::$reportsNavCustomersTitle);
        $I->seeElement(self::$reportsNavCustomersOrderTotal);
        $I->seeElement(self::$reportsNavCustomersOrderCount);
        $I->seeElement(self::$reportsNavCustomersNew);

        $I->seeElement(self::$reportsNavProductsMainArea);
        $I->seeElement(self::$reportsNavProductsTitle);
        $I->seeElement(self::$reportsNavProductsViews);
        $I->seeElement(self::$reportsNavProductsBestsellers);
        $I->seeElement(self::$reportsNavProductsLowStock);
        $I->seeElement(self::$reportsNavProductsOrdered);
        $I->seeElement(self::$reportsNavProductsDownloads);

        $I->seeElement(self::$reportsNavStatisticsMainArea);
        $I->seeElement(self::$reportsNavStatisticsTitle);
        $I->seeElement(self::$reportsNavStatisticsRefreshStatistics);
    }

    public function shouldSeeTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$storesNavSettingsMainArea);
        $I->seeElement(self::$storesNavSettingsTitle);
        $I->seeElement(self::$storesNavSettingsAllStores);
        $I->seeElement(self::$storesNavSettingsConfiguration);
        $I->seeElement(self::$storesNavSettingsTermsAndConditions );
        $I->seeElement(self::$storesNavSettingsOrderStatus);

        $I->seeElement(self::$storesNavTaxesMainArea);
        $I->seeElement(self::$storesNavTaxesTitle);
        $I->seeElement(self::$storesNavTaxesTaxRules);
        $I->seeElement(self::$storesNavTaxesTaxZonesAndRates);

        $I->seeElement(self::$storesNavCurrencyMainArea);
        $I->seeElement(self::$storesNavCurrencyTitle);
        $I->seeElement(self::$storesNavCurrencyRates);
        $I->seeElement(self::$storesNavCurrencySymbols);

        $I->seeElement(self::$storesNavAttributesMainArea);
        $I->seeElement(self::$storesNavAttributesTitle);
        $I->seeElement(self::$storesNavAttributesProduct);
        $I->seeElement(self::$storesNavAttributesSet);
        $I->seeElement(self::$storesNavAttributesRating);

        $I->seeElement(self::$storesNavOtherSettingsMainArea);
        $I->seeElement(self::$storesNavOtherSettingsTitle);
        $I->seeElement(self::$storesNavOtherSettingsCustomerGroups);
    }

    public function shouldSeeTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$systemNavDataTransferMainArea);
        $I->seeElement(self::$systemNavDataTransferTitle);
        $I->seeElement(self::$systemNavDataTransferImport);
        $I->seeElement(self::$systemNavDataTransferExport);
        $I->seeElement(self::$systemNavDataTransferImportExportTaxRates);
        $I->seeElement(self::$systemNavDataTransferImportHistory);

        $I->seeElement(self::$systemNavExtensionsMainArea);
        $I->seeElement(self::$systemNavExtensionsTitle);
        $I->seeElement(self::$systemNavExtensionsIntegrations);

        $I->seeElement(self::$systemNavToolsMainArea);
        $I->seeElement(self::$systemNavToolsTitle);
        $I->seeElement(self::$systemNavToolsCacheManagement);
        $I->seeElement(self::$systemNavToolsBackups);
        $I->seeElement(self::$systemNavToolsIndexManagement);
        $I->seeElement(self::$systemNavToolsWebSetupWizard);

        $I->seeElement(self::$systemNavPermissionsMainArea);
        $I->seeElement(self::$systemNavPermissionsTitle);
        $I->seeElement(self::$systemNavPermissionsAllUsers);
        $I->seeElement(self::$systemNavPermissionsLockedUsers);
        $I->seeElement(self::$systemNavPermissionsUserRoles);

        $I->seeElement(self::$systemNavOtherSettingsMainArea);
        $I->seeElement(self::$systemNavOtherSettingsTitle);
        $I->seeElement(self::$systemNavOtherSettingsNotifications);
        $I->seeElement(self::$systemNavOtherSettingsCustomVariables);
        $I->seeElement(self::$systemNavOtherSettingsManageEncryptionKey);
    }
}
