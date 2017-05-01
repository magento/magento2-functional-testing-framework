<?php
namespace Magento\Xxyyzz\Acceptance\Backend;

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
 * Class AccessAdminPagesDirectlyCest
 *
 * Allure annotations
 * @Features({"Login"})
 * @Stories({"Direct Admin Access"})
 *
 * Codeception annotations
 * @group skip
 * @group slow
 * @group admin-direct-access
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class AccessAdminPagesDirectlyCest
{
    public function _before(AdminStep $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * Allure annotations
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "AdminStep", value = "$I")
     * @TestCaseId("")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @return void
     */
    public function shouldBeAbleToAccessEachAdminPageDirectly(AdminStep $I)
    {
        $I->goToTheAdminOrdersGrid();
        $I->shouldBeOnTheAdminOrdersGrid();

        $I->goToTheAdminInvoicesGrid();
        $I->shouldBeOnTheAdminInvoicesGrid();

        $I->goToTheAdminShipmentsGrid();
        $I->shouldBeOnTheAdminShipmentsGrid();

        $I->goToTheAdminCreditMemosGrid();
        $I->shouldBeOnTheAdminCreditMemosGrid();

        $I->goToTheAdminBillingAgreementsGrid();
        $I->shouldBeOnTheAdminBillingAgreementsGrid();

        $I->goToTheAdminTransactionsGrid();
        $I->shouldBeOnTheAdminTransactionsGrid();

        $I->goToTheAdminCatalogPage();
        $I->shouldBeOnTheAdminCatalogGrid();

        $I->goToTheAdminCategoriesPage();
        $I->shouldBeOnTheAdminCategoryPage();

        $I->goToTheAdminAllCustomersGrid();
        $I->shouldBeOnTheAdminAllCustomersGrid();

        $I->goToTheAdminCustomersNowOnlineGrid();
        $I->shouldBeOnTheAdminCustomersNowOnlineGrid();

        $I->goToTheAdminCatalogPriceRuleGrid();
        $I->shouldBeOnTheAdminCatalogPriceRuleGrid();

        $I->goToTheAdminCartPriceRulesGrid();
        $I->shouldBeOnTheAdminCartPriceRulesGrid();

        $I->goToTheAdminEmailTemplatesGrid();
        $I->shouldBeOnTheAdminEmailTemplatesGrid();

        $I->goToTheAdminNewsletterTemplateGrid();
        $I->shouldBeOnTheAdminNewsletterTemplateGrid();

        $I->goToTheAdminNewsletterQueueGrid();
        $I->shouldBeOnTheAdminNewsletterQueueGrid();

        $I->goToTheAdminNewsletterSubscribersGrid();
        $I->shouldBeOnTheAdminNewsletterSubscribersGrid();

        $I->goToTheAdminURLRewritesGrid();
        $I->shouldBeOnTheAdminURLRewritesGrid();

        $I->goToTheAdminSearchTermsGrid();
        $I->shouldBeOnTheAdminSearchTermsGrid();

        $I->goToTheAdminSearchSynonymsGrid();
        $I->shouldBeOnTheAdminSearchSynonymsGrid();

        $I->goToTheAdminSiteMapGrid();
        $I->shouldBeOnTheAdminSiteMapGrid();

        $I->goToTheAdminReviewsGrid();
        $I->shouldBeOnTheAdminReviewsGrid();

        $I->goToTheAdminPagesGrid();
        $I->shouldBeOnTheAdminPagesGrid();

        $I->goToTheAdminBlocksGrid();
        $I->shouldBeOnTheAdminBlocksGrid();

        $I->goToTheAdminWidgetsGrid();
        $I->shouldBeOnTheAdminWidgetsGrid();

        $I->goToTheAdminDesignConfigurationGrid();
        $I->shouldBeOnTheAdminDesignConfigurationGrid();

        $I->goToTheAdminThemesGrid();
        $I->shouldBeOnTheAdminThemesGrid();

        $I->goToTheAdminStoreContentScheduleGrid();
        $I->shouldBeOnTheAdminStoreContentScheduleGrid();

        $I->goToTheAdminProductsInCartGrid();
        $I->shouldBeOnTheAdminProductsInCartGrid();

        $I->goToTheAdminSearchTermsReportGrid();
        $I->shouldBeOnTheAdminSearchTermsReportGrid();

        $I->goToTheAdminAbandonedCartsGrid();
        $I->shouldBeOnTheAdminAbandonedCartsGrid();

        $I->goToTheAdminNewsletterProblemsReportGrid();
        $I->shouldBeOnTheAdminNewsletterProblemsReportGrid();

        $I->goToTheAdminCustomerReviewsReportGrid();
        $I->shouldBeOnTheAdminCustomerReviewsReportGrid();

        $I->goToTheAdminProductReviewsReportGrid();
        $I->shouldBeOnTheAdminProductReviewsReportGrid();

        $I->goToTheAdminOrdersReportGrid();
        $I->shouldBeOnTheAdminOrdersReportGrid();

        $I->goToTheAdminTaxReportGrid();
        $I->shouldBeOnTheAdminTaxReportGrid();

        $I->goToTheAdminInvoiceReportGrid();
        $I->shouldBeOnTheAdminInvoiceReportGrid();

        $I->goToTheAdminShippingReportGrid();
        $I->shouldBeOnTheAdminShippingReportGrid();

        $I->goToTheAdminRefundsReportGrid();
        $I->shouldBeOnTheAdminRefundsReportGrid();

        $I->goToTheAdminCouponsReportGrid();
        $I->shouldBeOnTheAdminCouponsReportGrid();

        $I->goToTheAdminPayPalSettlementReportsGrid();
        $I->shouldBeOnTheAdminPayPalSettlementReportsGrid();

        $I->goToTheAdminBraintreeSettlementReportGrid();
        $I->shouldBeOnTheAdminBraintreeSettlementReportGrid();

        $I->goToTheAdminOrderTotalReportGrid();
        $I->shouldBeOnTheAdminOrderTotalReportGrid();

        $I->goToTheAdminOrderCountReportGrid();
        $I->shouldBeOnTheAdminOrderCountReportGrid();

        $I->goToTheAdminNewAccountsReportGrid();
        $I->shouldBeOnTheAdminNewAccountsReportGrid();

        $I->goToTheAdminProductViewsReportGrid();
        $I->shouldBeOnTheAdminProductViewsReportGrid();

        $I->goToTheAdminBestsellersReportGrid();
        $I->shouldBeOnTheAdminBestsellersReportGrid();

        $I->goToTheAdminLowStockReportGrid();
        $I->shouldBeOnTheAdminLowStockReportGrid();

        $I->goToTheAdminOrderedProductsReportGrid();
        $I->shouldBeOnTheAdminOrderedProductsGrid();

        $I->goToTheAdminDownloadsReportGrid();
        $I->shouldBeOnTheAdminDownloadsReportGrid();

        $I->goToTheAdminRefreshStatisticsGrid();
        $I->shouldBeOnTheAdminRefreshStatisticsGrid();

        $I->goToTheAdminAllStoresGrid();
        $I->shouldBeOnTheAdminAllStoresGrid();

        $I->goToTheAdminConfigurationGrid();
        $I->shouldBeOnTheAdminConfigurationGrid();

        $I->goToTheAdminTermsAndConditionsGrid();
        $I->shouldBeOnTheAdminTermsAndConditionsGrid();

        $I->goToTheAdminOrderStatusGrid();
        $I->shouldBeOnTheAdminOrderStatusGrid();

        $I->goToTheAdminTaxRulesGrid();
        $I->shouldBeOnTheAdminTaxRulesGrid();

        $I->goToTheAdminTaxZonesAndRatesGrid();
        $I->shouldBeOnTheAdminTaxZonesAndRatesGrid();

        $I->goToTheAdminCurrencyRatesPage();
        $I->shouldBeOnTheAdminCurrencyRatesPage();

        $I->goToTheAdminCurrencySymbolsPage();
        $I->shouldBeOnTheAdminCurrencySymbolsPage();

        $I->goToTheAdminProductAttributesGrid();
        $I->shouldBeOnTheAdminProductAttributesGrid();

        $I->goToTheAdminAttributeSetGrid();
        $I->shouldBeOnTheAdminAttributeSetsGrid();

        $I->goToTheAdminRatingGrid();
        $I->shouldBeOnTheAdminRatingsGrid();

        $I->goToTheAdminCustomerGroupsGrid();
        $I->shouldBeOnTheAdminCustomerGroupsGrid();

        $I->goToTheAdminImportPage();
        $I->shouldBeOnTheAdminImportPage();

        $I->goToTheAdminExportPage();
        $I->shouldBeOnTheAdminExportPage();

        $I->goToTheAdminImportAndExportTaxRatesPage();
        $I->shouldBeOnTheAdminImportAndExportTaxRatesPage();

        $I->goToTheAdminImportHistoryGrid();
        $I->shouldBeOnTheAdminImportHistoryGrid();

        $I->goToTheAdminIntegrationsGrid();
        $I->shouldBeOnTheAdminIntegrationsGrid();

        $I->goToTheAdminCacheManagementGrid();
        $I->shouldBeOnTheAdminCacheManagementGrid();

        $I->goToTheAdminBackupsGrid();
        $I->shouldBeOnTheAdminBackupsGrid();

        $I->goToTheAdminIndexManagementGrid();
        $I->shouldBeOnTheAdminIndexManagementGrid();

        $I->goToTheAdminAllUsersGrid();
        $I->shouldBeOnTheAdminAllUsersGrid();

        $I->goToTheAdminLockedUsersGrid();
        $I->shouldBeOnTheAdminLockedUsersGrid();

        $I->goToTheAdminUserRolesGrid();
        $I->shouldBeOnTheAdminUserRolesGrid();

        $I->goToTheAdminNotificationsGrid();
        $I->shouldBeOnTheAdminNotificationsGrid();

        $I->goToTheAdminCustomVariablesGrid();
        $I->shouldBeOnTheAdminCustomVariablesGrid();

        $I->goToTheAdminEncryptionKeyPage();
        $I->shouldBeOnTheAdminEncryptionKeyPage();
    }
}
