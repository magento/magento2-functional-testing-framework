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
    }

    public function clickOnSalesInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$salesButton);
        $I->wait(1);
    }

    public function clickOnOrdersInTheSalesNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$salesNavOrders);
    }

    public function clickOnInvoicesInTheSalesNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$salesNavInvoices);
    }

    public function clickOnShipmentsInTheSalesNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$salesNavShipments);
    }

    public function clickOnCreditMemosInTheSalesNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$salesNavCreditMemos);
    }

    public function clickOnBillingAgreementsInTheSalesNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$salesNavBillingAgreements);
    }

    public function clickOnTransactionsInTheSalesNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$salesNavTransactions);
    }

    public function clickOnProductsInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$productsButton);
        $I->wait(1);
    }

    public function clickOnCatalogInTheProductNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$productNavCatalog);
    }

    public function clickOnCategoriesInTheProductNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$productNavCategories);
    }

    public function clickOnCustomersInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$customersButton);
        $I->wait(1);
    }

    public function clickOnAllCustomersInTheCustomersNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$customersNavAllCustomers);
    }

    public function clickOnNowOnlineInTheCustomersNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$customersNavNowOnline);
    }

    public function clickOnMarketingInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingButton);
        $I->wait(1);
    }

    public function clickOnCatalogPriceRulesInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavPromotionsCatalogPriceRule);
    }

    public function clickOnCartPriceRulesInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavPromotionsCartPriceRules);
    }

    public function clickOnEmailTemplatesInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavCommunicationsEmailTemplates);
    }

    public function clickOnNewsletterTemplatesInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavCommunicationsNewsletterTemplates);
    }

    public function clickOnNewsletterQueueInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavCommunicationsNewsletterQueue);
    }

    public function clickOnNewsletterSubscribersInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavCommunicationsNewsletterSubscribers);
    }

    public function clickOnURLRewritesInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavSEOSearchURLRewrites);
    }

    public function clickOnSearchTermsInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavSEOSearchTerms);
    }

    public function clickOnSearchSynonymsInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavSEOSearchSynonyms);
    }

    public function clickOnSiteMapInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavSEOSearchSiteMap);
    }

    public function clickOnContentReviewsInTheMarketingNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$marketingNavUserContentReviews);
    }

    public function clickOnContentInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contentButton);
        $I->wait(1);
    }

    public function clickOnPagesInTheContentNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contentNavElementsPages);
    }

    public function clickOnBlocksInTheContentNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contentNavElementsBlocks);
    }

    public function clickOnWidgetsInTheContentNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contentNavElementsWidgets);
    }

    public function clickOnConfigurationInTheContentNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contentNavDesignConfiguration);
    }

    public function clickOnThemesInTheContentNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contentNavDesignThemes);
    }

    public function clickOnScheduleInTheContentNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contentNavDesignSchedule);
    }

    public function clickOnReportsInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsButton);
        $I->wait(1);
    }

    public function clickOnProductsInCartInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavMarketingProductsInCart);
    }

    public function clickOnSearchTermsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavMarketingSearchTerms);
    }

    public function clickOnAbandonedCartsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavMarketingAbandonedCarts);
    }

    public function clickOnNewsletterProblemReportsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavMarketingNewsletterProblemReports);
    }

    public function clickOnByCustomersInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavReviewsByCustomers);
    }

    public function clickOnByProductsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavReviewsByProducts);
    }

    public function clickOnOrdersInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesOrders);
    }

    public function clickOTaxInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesTax);
    }

    public function clickOnInvoicedInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesInvoiced);
    }

    public function clickOnShippingInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesShipping);
    }

    public function clickOnRefundsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesRefunds);
    }

    public function clickOnCouponsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesCoupons);
    }

    public function clickOnPayPalSettlementInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesPayPalSettlement);
    }

    public function clickOnBraintreeSettlementInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavSalesBraintreeSettlement);
    }

    public function clickOnOrderTotalInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavCustomersOrderTotal);
    }

    public function clickOnOrderCountInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavCustomersOrderCount);
    }

    public function clickOnNewInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavCustomersNew);
    }

    public function clickOnViewsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavProductsViews);
    }

    public function clickOnBestSellersInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavProductsBestsellers);
    }

    public function clickOnLowStockInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavProductsLowStock);
    }

    public function clickOnOrderedInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavProductsOrdered);
    }

    public function clickOnDownloadsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavProductsDownloads);
    }

    public function clickOnRefreshStatisticsInTheReportsNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reportsNavStatisticsRefreshStatistics);
    }

    public function clickOnStoresInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesButton);
        $I->wait(1);
    }

    public function clickOnAllStoresInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavSettingsAllStores);
    }

    public function clickOnConfigurationInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavSettingsConfiguration);
    }

    public function clickOnTermsAndConditionsInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavSettingsTermsAndConditions);
    }

    public function clickOnOrderStatusInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavSettingsOrderStatus);
    }

    public function clickOnTaxRuleInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavTaxesTaxRules);
    }

    public function clickOnTaxZonesAndRatesInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavTaxesTaxZonesAndRates);
    }

    public function clickOnTaxRatesInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavCurrencyRates);
    }

    public function clickOnTaxSymbolsInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavCurrencySymbols);
    }

    public function clickOnCurrencyRatesInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavCurrencyRates);
    }

    public function clickOnCurrencySymbolsInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavCurrencySymbols);
    }

    public function clickOnProductInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavAttributesProduct);
    }

    public function clickOnAttributesSetInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavAttributesSet);
    }

    public function clickOnRatingsInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavAttributesRating);
    }

    public function clickOnCustomerGroupInTheStoresNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$storesNavOtherSettingsCustomerGroups);
    }

    public function clickOnSystemInTheSideNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemButton);
        $I->wait(1);
    }

    public function clickOnImportInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavDataTransferImport);
    }

    public function clickOnExportInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavDataTransferExport);
    }

    public function clickOnImportExportTaxRatesInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavDataTransferImportExportTaxRates);
    }

    public function clickOnImportHistoryInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavDataTransferImportHistory);
    }

    public function clickOnIntegrationsInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavExtensionsIntegrations);
    }

    public function clickOnCacheManagementInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavToolsCacheManagement);
    }

    public function clickOnBackupsInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavToolsBackups);
    }

    public function clickOnIndexManagementInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavToolsIndexManagement);
    }

    public function clickOnWebSetupWizardInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavToolsWebSetupWizard);
        $I->wait(1);
    }

    public function clickOnAllUsersInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavPermissionsAllUsers);
    }

    public function clickOnLockedUsersInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavPermissionsLockedUsers);
    }

    public function clickOnUserRolesInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavPermissionsUserRoles);
    }

    public function clickOnNotificationsInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavOtherSettingsNotifications);
    }

    public function clickOnCustomVariablesInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavOtherSettingsCustomVariables);
    }

    public function clickOnManageEncryptionKeyInTheSystemNavMenu()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$systemNavOtherSettingsManageEncryptionKey);
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
