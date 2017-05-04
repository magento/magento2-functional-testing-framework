<?php
namespace Magento\Xxyyzz\Acceptance\Backend;

use Magento\Xxyyzz\Page\Backend\AdminSideNavigation as SideNav;
use Magento\Xxyyzz\Step\Backend\AdminStep;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Model\SeverityLevel;
use Yandex\Allure\Adapter\Annotation\TestCaseId;

/**
 * Class AccessAdminPagesViaNavMenuCest
 * 
 * Allure annotations
 * @Features({"Admin Nav Menu"})
 * @Stories({"Access Admin pages via the Admin Nav Menu"})
 *
 * Codeception annotations
 * @group skip
 * @group slow
 * @group nav-menu-access
 * @env chrome
 * @env firefox
 */
class AccessAdminPagesViaNavMenuCest
{
    public function _before(AdminStep $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * Allure annotations
     * @Title("DASHBOARD Menu Test")
     * @Description("Attempt to access the DASHBOARD via the Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnTheDashboardPage(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access the DASHBOARD Page using the Side Nav Menu.');
        $I->goToRandomAdminPage();
        $sideNavMenu->clickOnDashboardInTheSideNavMenu();
        $I->shouldBeOnTheAdminDashboardPage();
    }

    /**
     * Allure annotations
     * @Title("SALES Menu Tests")
     * @Description("Attempt to access all of the SALES pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnEachOfTheSalesPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the SALES Admin Pages using the Side Nav Menus');
        $sideNavMenu->clickOnSalesInTheSideNavMenu();
        $sideNavMenu->clickOnOrdersInTheSalesNavMenu();
        $I->shouldBeOnTheAdminOrdersGrid();

        $sideNavMenu->clickOnSalesInTheSideNavMenu();
        $sideNavMenu->clickOnInvoicesInTheSalesNavMenu();
        $I->shouldBeOnTheAdminInvoicesGrid();

        $sideNavMenu->clickOnSalesInTheSideNavMenu();
        $sideNavMenu->clickOnShipmentsInTheSalesNavMenu();
        $I->shouldBeOnTheAdminShipmentsGrid();

        $sideNavMenu->clickOnSalesInTheSideNavMenu();
        $sideNavMenu->clickOnCreditMemosInTheSalesNavMenu();
        $I->shouldBeOnTheAdminCreditMemosGrid();

        $sideNavMenu->clickOnSalesInTheSideNavMenu();
        $sideNavMenu->clickOnBillingAgreementsInTheSalesNavMenu();
        $I->shouldBeOnTheAdminBillingAgreementsGrid();

        $sideNavMenu->clickOnSalesInTheSideNavMenu();
        $sideNavMenu->clickOnTransactionsInTheSalesNavMenu();
        $I->shouldBeOnTheAdminTransactionsGrid();
    }

    /**
     * Allure annotations
     * @Title("PRODUCT Menu Tests")
     * @Description("Attempt to access all of the PRODUCT pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnEachOfTheProductsPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the PRODUCT Admin Pages using the Side Nav Menus');
        $sideNavMenu->clickOnProductsInTheSideNavMenu();
        $sideNavMenu->clickOnCatalogInTheProductNavMenu();
        $I->shouldBeOnTheAdminCatalogGrid();

        $sideNavMenu->clickOnProductsInTheSideNavMenu();
        $sideNavMenu->clickOnCategoriesInTheProductNavMenu();
        $I->shouldBeOnTheAdminCategoriesPage();
    }

    /**
     * Allure annotations
     * @Title("CUSTOMER Menu Tests")
     * @Description("Attempt to access all of the CUSTOMER pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLanOnEachOfTheCustomersPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the CUSTOMER Admin Pages using the Side Nav Menus');
        $sideNavMenu->clickOnCustomersInTheSideNavMenu();
        $sideNavMenu->clickOnAllCustomersInTheCustomersNavMenu();
        $I->shouldBeOnTheAdminAllCustomersGrid();

        $sideNavMenu->clickOnCustomersInTheSideNavMenu();
        $sideNavMenu->clickOnNowOnlineInTheCustomersNavMenu();
        $I->shouldBeOnTheAdminCustomersNowOnlineGrid();
    }

    // Marketing Menu Tests
    /**
     * Allure annotations
     * @Title("MARKETING Menu Tests")
     * @Description("Attempt to access all of the MARKETING pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnEachOfTheMarketingPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the MARKETING Admin Pages using the Side Nav Menus');
        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnCatalogPriceRulesInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminCatalogPriceRuleGrid();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnCartPriceRulesInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminCartPriceRulesGrid();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnEmailTemplatesInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminEmailTemplatesGrid();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnNewsletterTemplatesInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminNewsletterTemplateGrid();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnNewsletterQueueInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminNewsletterQueueGrid();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnNewsletterSubscribersInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminNewsletterSubscribersGrid();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnURLRewritesInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminURLRewritesGrid();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnSearchTermsInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminSearchTermsGrid();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnSearchSynonymsInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminSearchSynonymsGrid();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnSiteMapInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminSiteMapGrid();

        $sideNavMenu->clickOnMarketingInTheSideNavMenu();
        $sideNavMenu->clickOnContentReviewsInTheMarketingNavMenu();
        $I->shouldBeOnTheAdminReviewsGrid();
    }

    // Content Menu Tests
    /**
     * Allure annotations
     * @Title("CONTENT Menu Tests")
     * @Description("Attempt to access all of the CONTENT pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnEachOfTheContentPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the CONTENT Admin Pages using the Side Nav Menus');
        $sideNavMenu->clickOnContentInTheSideNavMenu();
        $sideNavMenu->clickOnPagesInTheContentNavMenu();
        $I->shouldBeOnTheAdminPagesGrid();

        $sideNavMenu->clickOnContentInTheSideNavMenu();
        $sideNavMenu->clickOnBlocksInTheContentNavMenu();
        $I->shouldBeOnTheAdminBlocksGrid();

        $sideNavMenu->clickOnContentInTheSideNavMenu();
        $sideNavMenu->clickOnWidgetsInTheContentNavMenu();
        $I->shouldBeOnTheAdminWidgetsGrid();

        $sideNavMenu->clickOnContentInTheSideNavMenu();
        $sideNavMenu->clickOnConfigurationInTheContentNavMenu();
        $I->shouldBeOnTheAdminDesignConfigurationGrid();

        $sideNavMenu->clickOnContentInTheSideNavMenu();
        $sideNavMenu->clickOnThemesInTheContentNavMenu();
        $I->shouldBeOnTheAdminThemesGrid();

        $sideNavMenu->clickOnContentInTheSideNavMenu();
        $sideNavMenu->clickOnScheduleInTheContentNavMenu();
        $I->shouldBeOnTheAdminStoreContentScheduleGrid();
    }

    /**
     * Allure annotations
     * @Title("REPORT Menu Tests")
     * @Description("Attempt to access all of the REPORT pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnEachOfTheReportsPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the REPORT Admin Pages using the Side Nav Menu');
        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnProductsInCartInTheReportsNavMenu();
        $I->shouldBeOnTheAdminProductsInCartGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnSearchTermsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminSearchTermsReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnAbandonedCartsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminAbandonedCartsGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnNewsletterProblemReportsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminNewsletterProblemsReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnByCustomersInTheReportsNavMenu();
        $I->shouldBeOnTheAdminCustomerReviewsReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnByProductsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminProductReviewsReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnOrdersInTheReportsNavMenu();
        $I->shouldBeOnTheAdminOrdersReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOTaxInTheReportsNavMenu();
        $I->shouldBeOnTheAdminTaxReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnInvoicedInTheReportsNavMenu();
        $I->shouldBeOnTheAdminInvoiceReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnShippingInTheReportsNavMenu();
        $I->shouldBeOnTheAdminShippingReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnRefundsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminRefundsReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnCouponsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminCouponsReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnPayPalSettlementInTheReportsNavMenu();
        $I->shouldBeOnTheAdminPayPalSettlementReportsGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnBraintreeSettlementInTheReportsNavMenu();
        $I->shouldBeOnTheAdminBraintreeSettlementReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnOrderTotalInTheReportsNavMenu();
        $I->shouldBeOnTheAdminOrderTotalReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnOrderCountInTheReportsNavMenu();
        $I->shouldBeOnTheAdminOrderCountReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnNewInTheReportsNavMenu();
        $I->shouldBeOnTheAdminNewAccountsReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnViewsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminProductViewsReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnBestSellersInTheReportsNavMenu();
        $I->shouldBeOnTheAdminBestsellersReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnLowStockInTheReportsNavMenu();
        $I->shouldBeOnTheAdminLowStockReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnOrderedInTheReportsNavMenu();
        $I->shouldBeOnTheAdminOrderedProductsGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnDownloadsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminDownloadsReportGrid();

        $sideNavMenu->clickOnReportsInTheSideNavMenu();
        $sideNavMenu->clickOnRefreshStatisticsInTheReportsNavMenu();
        $I->shouldBeOnTheAdminRefreshStatisticsGrid();
    }

    /**
     * Allure annotations
     * @Title("STORE Menu Tests")
     * @Description("Attempt to access all of the STORE pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnEachOfTheStoresPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the STORE Admin Pages using the Side Nav Menu');
        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnAllStoresInTheStoresNavMenu();
        $I->shouldBeOnTheAdminAllStoresGrid();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnConfigurationInTheStoresNavMenu();
        $I->shouldBeOnTheAdminConfigurationGrid();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnTermsAndConditionsInTheStoresNavMenu();
        $I->shouldBeOnTheAdminTermsAndConditionsGrid();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnOrderStatusInTheStoresNavMenu();
        $I->shouldBeOnTheAdminOrderStatusGrid();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnTaxRuleInTheStoresNavMenu();
        $I->shouldBeOnTheAdminTaxRulesGrid();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnTaxZonesAndRatesInTheStoresNavMenu();
        $I->shouldBeOnTheAdminTaxZonesAndRatesGrid();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnCurrencyRatesInTheStoresNavMenu();
        $I->shouldBeOnTheAdminCurrencyRatesPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnCurrencySymbolsInTheStoresNavMenu();
        $I->shouldBeOnTheAdminCurrencySymbolsPage();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnProductInTheStoresNavMenu();
        $I->shouldBeOnTheAdminProductAttributesGrid();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnAttributesSetInTheStoresNavMenu();
        $I->shouldBeOnTheAdminAttributeSetsGrid();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnRatingsInTheStoresNavMenu();
        $I->shouldBeOnTheAdminRatingsGrid();

        $sideNavMenu->clickOnStoresInTheSideNavMenu();
        $sideNavMenu->clickOnCustomerGroupInTheStoresNavMenu();
        $I->shouldBeOnTheAdminCustomerGroupsGrid();
    }

    // System Menu Tests
    /**
     * Allure annotations
     * @Title("SYSTEM Menu Tests")
     * @Description("Attempt to access all of the SYSTEM pages using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnEachOfTheSystemPages(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access each of the SYSTEM Admin Pages using the Side Nav Menu');
        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnImportInTheSystemNavMenu();
        $I->shouldBeOnTheAdminImportPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnExportInTheSystemNavMenu();
        $I->shouldBeOnTheAdminExportPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnImportExportTaxRatesInTheSystemNavMenu();
        $I->shouldBeOnTheAdminImportAndExportTaxRatesPage();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnImportHistoryInTheSystemNavMenu();
        $I->shouldBeOnTheAdminImportHistoryGrid();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnIntegrationsInTheSystemNavMenu();
        $I->shouldBeOnTheAdminIntegrationsGrid();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnCacheManagementInTheSystemNavMenu();
        $I->shouldBeOnTheAdminCacheManagementGrid();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnBackupsInTheSystemNavMenu();
        $I->shouldBeOnTheAdminBackupsGrid();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnIndexManagementInTheSystemNavMenu();
        $I->shouldBeOnTheAdminIndexManagementGrid();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnAllUsersInTheSystemNavMenu();
        $I->shouldBeOnTheAdminAllUsersGrid();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnLockedUsersInTheSystemNavMenu();
        $I->shouldBeOnTheAdminLockedUsersGrid();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnUserRolesInTheSystemNavMenu();
        $I->shouldBeOnTheAdminUserRolesGrid();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnNotificationsInTheSystemNavMenu();
        $I->shouldBeOnTheAdminNotificationsGrid();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnCustomVariablesInTheSystemNavMenu();
        $I->shouldBeOnTheAdminCustomVariablesGrid();

        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnManageEncryptionKeyInTheSystemNavMenu();
        $I->shouldBeOnTheAdminEncryptionKeyPage();
    }

    /**
     * Allure annotations
     * @Title("WEB SETUP WIZARD Menu Test")
     * @Description("Attempt to access all of the WEB SETUP WIZARD page using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnTheWebSetupWizardPage(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access the WEB SETUP WIZARD Admin Page using the Side Nav Menu');
        $sideNavMenu->clickOnSystemInTheSideNavMenu();
        $sideNavMenu->clickOnWebSetupWizardInTheSystemNavMenu();
        $I->shouldBeOnTheAdminWebSetupWizardPage();
        $I->goToTheAdminLogoutPage();
    }

    /**
     * Allure annotations
     * @Title("PARTNERS & EXTENSIONS Menu Test")
     * @Description("Attempt to access all of the PARTNERS & EXTENSIONS page using the Side Nav Menu.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "SideNav", value = "$sideNavMenu")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param SideNav $sideNavMenu
     * @return void
     */
    public function shouldLandOnThePartnersAndExtensionsPage(AdminStep $I, SideNav $sideNavMenu)
    {
        $I->wantTo('see if I can access the Partners and Extensions Admin Page using the Side Nav Menu');
        $sideNavMenu->clickOnFindPartnersAndExtensionsInTheSideNavMenu();
        $I->shouldBeOnTheAdminFindPartnersAndExtensionsPage();
        $I->goToTheAdminLogoutPage();
    }
}
