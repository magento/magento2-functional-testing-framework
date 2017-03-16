<?php
namespace Page\Acceptance;

class SideNavigation
{
    // include url of current page
    public static $URL = '/admin/admin/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
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

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

    /**
     * @var \AcceptanceTester;
     */
    protected $acceptanceTester;

    public function __construct(\AcceptanceTester $I)
    {
        $this->acceptanceTester = $I;
    }

    public function clickOnDashboardInTheSideNavMenu(\AcceptanceTester $I) {
        $I->click(self::$dashboardButton);
    }

    public function clickOnSalesInTheSideNavMenu(\AcceptanceTester $I) {
        $I->click(self::$salesButton);
        $I->wait(1);
    }

        public function clickOnOrdersInTheSalesNavMenu(\AcceptanceTester $I) {
            $I->click(self::$salesNavOrders);
        }

        public function clickOnInvoicesInTheSalesNavMenu(\AcceptanceTester $I) {
            $I->click(self::$salesNavInvoices);
        }

        public function clickOnShipmentsInTheSalesNavMenu(\AcceptanceTester $I) {
            $I->click(self::$salesNavShipments);
        }

        public function clickOnCreditMemosInTheSalesNavMenu(\AcceptanceTester $I) {
            $I->click(self::$salesNavCreditMemos);
        }

        public function clickOnBillingAgreementsInTheSalesNavMenu(\AcceptanceTester $I) {
            $I->click(self::$salesNavBillingAgreements);
        }

        public function clickOnTransactionsInTheSalesNavMenu(\AcceptanceTester $I) {
            $I->click(self::$salesNavTransactions);
        }

    public function clickOnProductsInTheSideNavMenu(\AcceptanceTester $I) {
        $I->click(self::$productsButton);
        $I->wait(1);
    }

        public function clickOnCatalogInTheProductNavMenu(\AcceptanceTester $I) {
            $I->click(self::$productNavCatalog);
        }

        public function clickOnCategoriesInTheProductNavMenu(\AcceptanceTester $I) {
            $I->click(self::$productNavCategories);
        }

    public function clickOnCustomersInTheSideNavMenu(\AcceptanceTester $I) {
        $I->click(self::$customersButton);
        $I->wait(1);
    }

        public function clickOnAllCustomersInTheCustomersNavMenu(\AcceptanceTester $I) {
            $I->click(self::$customersNavAllCustomers);
        }

        public function clickOnNowOnlineInTheCustomersNavMenu(\AcceptanceTester $I) {
            $I->click(self::$customersNavNowOnline);
        }

    public function clickOnMarketingInTheSideNavMenu(\AcceptanceTester $I) {
        $I->click(self::$marketingButton);
        $I->wait(1);
    }

        public function clickOnCatalogPriceRulesInTheMarketingNavMenu(\AcceptanceTester $I) {
            $I->click(self::$marketingNavPromotionsCatalogPriceRule);
        }

        public function clickOnCartPriceRulesInTheMarketingNavMenu(\AcceptanceTester $I) {
            $I->click(self::$marketingNavPromotionsCartPriceRules);
        }

        public function clickOnEmailTemplatesInTheMarketingNavMenu(\AcceptanceTester $I) {
            $I->click(self::$marketingNavCommunicationsEmailTemplates);
        }

        public function clickOnNewsletterTemplatesInTheMarketingNavMenu(\AcceptanceTester $I) {
            $I->click(self::$marketingNavCommunicationsNewsletterTemplates);
        }

        public function clickOnNewsletterQueueInTheMarketingNavMenu(\AcceptanceTester $I) {
            $I->click(self::$marketingNavCommunicationsNewsletterQueue);
        }

        public function clickOnNewsletterSubscribersInTheMarketingNavMenu(\AcceptanceTester $I) {
            $I->click(self::$marketingNavCommunicationsNewsletterSubscribers);
        }

        public function clickOnURLRewritesInTheMarketingNavMenu(\AcceptanceTester $I) {
            $I->click(self::$marketingNavSEOSearchURLRewrites);
        }

        public function clickOnSearchTermsInTheMarketingNavMenu(\AcceptanceTester $I) {
            $I->click(self::$marketingNavSEOSearchTerms);
        }

        public function clickOnSearchSynonymsInTheMarketingNavMenu(\AcceptanceTester $I) {
            $I->click(self::$marketingNavSEOSearchSynonyms);
        }

        public function clickOnSiteMapInTheMarketingNavMenu(\AcceptanceTester $I) {
            $I->click(self::$marketingNavSEOSearchSiteMap);
        }

        public function clickOnContentReviewsInTheMarketingNavMenu(\AcceptanceTester $I) {
            $I->click(self::$marketingNavUserContentReviews);
        }

    public function clickOnContentInTheSideNavMenu(\AcceptanceTester $I) {
        $I->click(self::$contentButton);
        $I->wait(1);
    }

        public function clickOnPagesInTheContentNavMenu(\AcceptanceTester $I) {
            $I->click(self::$contentNavElementsPages);
        }

        public function clickOnBlocksInTheContentNavMenu(\AcceptanceTester $I) {
            $I->click(self::$contentNavElementsBlocks);
        }

        public function clickOnWidgetsInTheContentNavMenu(\AcceptanceTester $I) {
            $I->click(self::$contentNavElementsWidgets);
        }

        public function clickOnConfigurationInTheContentNavMenu(\AcceptanceTester $I) {
            $I->click(self::$contentNavDesignConfiguration);
        }

        public function clickOnThemesInTheContentNavMenu(\AcceptanceTester $I) {
            $I->click(self::$contentNavDesignThemes);
        }

        public function clickOnScheduleInTheContentNavMenu(\AcceptanceTester $I) {
            $I->click(self::$contentNavDesignSchedule);
        }

    public function clickOnReportsInTheSideNavMenu(\AcceptanceTester $I) {
        $I->click(self::$reportsButton);
        $I->wait(1);
    }

        public function clickOnProductsInCartInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavMarketingProductsInCart);
        }

        public function clickOnSearchTermsInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavMarketingSearchTerms);
        }

        public function clickOnAbandonedCartsInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavMarketingAbandonedCarts);
        }

        public function clickOnNewsletterProblemReportsInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavMarketingNewsletterProblemReports);
        }

        public function clickOnByCustomersInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavReviewsByCustomers);
        }

        public function clickOnByProductsInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavReviewsByProducts);
        }

        public function clickOnOrdersInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavSalesOrders);
        }

        public function clickOTaxInTheReportsNavMenu(\AcceptanceTester $I) {
            $I = $this->acceptanceTester;
            $I->click(self::$reportsNavSalesTax);
        }

        public function clickOnInvoicedInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavSalesInvoiced);
        }

        public function clickOnShippingInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavSalesShipping);
        }

        public function clickOnRefundsInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavSalesRefunds);
        }

        public function clickOnCouponsInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavSalesCoupons);
        }

        public function clickOnPayPalSettlementInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavSalesPayPalSettlement);
        }

        public function clickOnBraintreeSettlementInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavSalesBraintreeSettlement);
        }

        public function clickOnOrderTotalInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavCustomersOrderTotal);
        }

        public function clickOnOrderCountInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavCustomersOrderCount);
        }

        public function clickOnNewInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavCustomersNew);
        }

        public function clickOnViewsInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavProductsViews);
        }

        public function clickOnBestSellersInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavProductsBestsellers);
        }

        public function clickOnLowStockInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavProductsLowStock);
        }

        public function clickOnOrderedInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavProductsOrdered);
        }

        public function clickOnDownloadsInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavProductsDownloads);
        }

        public function clickOnRefreshStatisticsInTheReportsNavMenu(\AcceptanceTester $I) {
            $I->click(self::$reportsNavStatisticsRefreshStatistics);
        }

    public function clickOnStoresInTheSideNavMenu(\AcceptanceTester $I) {
        $I->click(self::$storesButton);
        $I->wait(1);
    }

        public function clickOnAllStoresInTheStoresNavMenu(\AcceptanceTester $I) {
            $I->click(self::$storesNavSettingsAllStores);
        }

        public function clickOnConfigurationInTheStoresNavMenu(\AcceptanceTester $I) {
            $I->click(self::$storesNavSettingsConfiguration);
        }

        public function clickOnTermsAndConditionsInTheStoresNavMenu(\AcceptanceTester $I) {
            $I->click(self::$storesNavSettingsTermsAndConditions);
        }

        public function clickOnOrderStatusInTheStoresNavMenu(\AcceptanceTester $I) {
            $I->click(self::$storesNavSettingsOrderStatus);
        }

        public function clickOnTaxRuleInTheStoresNavMenu(\AcceptanceTester $I) {
            $I->click(self::$storesNavTaxesTaxRules);
        }

        public function clickOnTaxZonesAndRatesInTheStoresNavMenu(\AcceptanceTester $I) {
            $I->click(self::$storesNavTaxesTaxZonesAndRates);
        }

        public function clickOnTaxRatesInTheStoresNavMenu(\AcceptanceTester $I) {
            $I->click(self::$storesNavCurrencyRates);
        }

        public function clickOnTaxSymbolsInTheStoresNavMenu(\AcceptanceTester $I) {
            $I->click(self::$storesNavCurrencySymbols);
        }

        public function clickOnCurrencyRatesInTheStoresNavMenu(\AcceptanceTester $I) {
            $I->click(self::$storesNavCurrencyRates);
        }

        public function clickOnCurrencySymbolsInTheStoresNavMenu(\AcceptanceTester $I) {
            $I->click(self::$storesNavCurrencySymbols);
        }

        public function clickOnProductInTheStoresNavMenu(\AcceptanceTester $I) {
            $I->click(self::$storesNavAttributesProduct);
        }

        public function clickOnAttributesSetInTheStoresNavMenu(\AcceptanceTester $I) {
            $I->click(self::$storesNavAttributesSet);
        }

        public function clickOnRatingsInTheStoresNavMenu(\AcceptanceTester $I) {
            $I->click(self::$storesNavAttributesRating);
        }

        public function clickOnCustomerGroupInTheStoresNavMenu(\AcceptanceTester $I) {
            $I->click(self::$storesNavOtherSettingsCustomerGroups);
        }

    public function clickOnSystemInTheSideNavMenu(\AcceptanceTester $I) {
        $I->click(self::$systemButton);
        $I->wait(1);
    }

        public function clickOnImportInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavDataTransferImport);
        }

        public function clickOnExportInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavDataTransferExport);
        }

        public function clickOnImportExportTaxRatesInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavDataTransferImportExportTaxRates);
        }

        public function clickOnImportHistoryInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavDataTransferImportHistory);
        }

        public function clickOnIntegrationsInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavExtensionsIntegrations);
        }

        public function clickOnCacheManagementInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavToolsCacheManagement);
        }

        public function clickOnBackupsInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavToolsBackups);
        }

        public function clickOnIndexManagementInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavToolsIndexManagement);
        }

        public function clickOnWebSetupWizardInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavToolsWebSetupWizard);
            $I->wait(1);
        }

        public function clickOnAllUsersInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavPermissionsAllUsers);
        }

        public function clickOnLockedUsersInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavPermissionsLockedUsers);
        }

        public function clickOnUserRolesInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavPermissionsUserRoles);
        }

        public function clickOnNotificationsInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavOtherSettingsNotifications);
        }

        public function clickOnCustomVariablesInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavOtherSettingsCustomVariables);
        }

        public function clickOnManageEncryptionKeyInTheSystemNavMenu(\AcceptanceTester $I) {
            $I->click(self::$systemNavOtherSettingsManageEncryptionKey);
        }

    public function clickOnFindPartnersAndExtensionsInTheSideNavMenu(\AcceptanceTester $I) {
        $I->click(self::$findPartnersExtensionsButton);
        $I->wait(1);
    }

    public function shouldSeeTheSalesNavMainArea(\AcceptanceTester $I) {
        $I->seeElement(self::$salesNavMainArea);
    }

    public function shouldSeeTheSalesNavTitle(\AcceptanceTester $I) {
        $I->seeElement(self::$salesNavTitle);
    }

    public function shouldSeeTheSalesNavOrders(\AcceptanceTester $I) {
        $I->seeElement(self::$salesNavOrders);
    }

    public function shouldSeeTheSalesNavInvoices(\AcceptanceTester $I) {
        $I->seeElement(self::$salesNavInvoices);
    }

    public function shouldSeeTheSalesNavShipments(\AcceptanceTester $I) {
        $I->seeElement(self::$salesNavShipments);
    }

    public function shouldSeeTheSalesNavCreditMemos(\AcceptanceTester $I) {
        $I->seeElement(self::$salesNavCreditMemos);
    }

    public function shouldSeeTheSalesNavBillingAgreements(\AcceptanceTester $I) {
        $I->seeElement(self::$salesNavBillingAgreements);
    }

    public function shouldSeeTheSalesNavTransactions(\AcceptanceTester $I) {
        $I->seeElement(self::$salesNavTransactions);
    }

    public function shouldSeeTheSalesNavMenu(\AcceptanceTester $I) {
        $this->shouldSeeTheSalesNavMainArea($I);
        $this->shouldSeeTheSalesNavTitle($I);
        $this->shouldSeeTheSalesNavOrders($I);
        $this->shouldSeeTheSalesNavInvoices($I);
        $this->shouldSeeTheSalesNavShipments($I);
        $this->shouldSeeTheSalesNavCreditMemos($I);
        $this->shouldSeeTheSalesNavBillingAgreements($I);
        $this->shouldSeeTheSalesNavTransactions($I);
    }

    public function shouldSeeTheProductNavMenu(\AcceptanceTester $I) {
        $I->seeElement(self::$productNavMainArea);
        $I->seeElement(self::$productNavTitle);
        $I->seeElement(self::$productNavCatalog);
        $I->seeElement(self::$productNavCategories);
    }

    public function shouldSeeTheCustomersNavMenu(\AcceptanceTester $I) {
        $I->seeElement(self::$customersNavAllCustomers);
        $I->seeElement(self::$customersNavNowOnline);
    }

    public function shouldSeeTheMarketingNavMenu(\AcceptanceTester $I) {
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

    public function shouldSeeTheContentNavMenu(\AcceptanceTester $I) {
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

    public function shouldSeeTheReportsNavMenu(\AcceptanceTester $I) {
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

    public function shouldSeeTheStoresNavMenu(\AcceptanceTester $I) {
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

    public function shouldSeeTheSystemNavMenu(\AcceptanceTester $I) {
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