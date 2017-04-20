<?php
namespace Magento\Xxyyzz\Acceptance\Cms;

use Magento\Xxyyzz\Page\Cms\StorefrontCmsPage;
use Magento\Xxyyzz\Page\Cms\AdminCmsGrid;
use Magento\Xxyyzz\Step\Backend\AdminStep;
use Magento\Xxyyzz\Page\Cms\AdminCmsPage;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Model\SeverityLevel;

/**
 * Class CreateContentPageCest
 *
 * Allure annotations
 * @Features({"Content"})
 * @Stories({"Exercise all Content Page fields", "Create a basic Content Page"})
 * @Title("Exercise all fields and create basic Content Page")
 * @Description("Attempt to enter Text into all fields and then create a basic Content Page.")
 *
 * Codeception annotations
 * @group cms
 * @group pages
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class CreateContentPageCest
{
    public function _before(
        AdminStep $I,
        AdminCmsPage $adminCmsPage
    )
    {
        $I->am('an Admin');
        $I->loginAsAdmin();
        $adminCmsPage->amOnAdminCmsPage();
        $adminCmsPage->clickOnAddNewPageButton();
        $I->waitForSpinnerToDisappear();
    }

    public function _after(AdminStep $I)
    {
        $I->goToTheAdminLogoutPage();
    }

    /**
     * Allure annotations
     * @Title("Enter text into every field on the ADD Content Page.")
     * @Description("Enter text into ALL fields on the ADD Content Page and verify the content of the fields.")
     * @Severity(level = SeverityLevel::NORMAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminCmsPage", value = "$adminCmsPage")
     *
     * Codeception annotations
     * @group fields
     * @param AdminStep $I
     * @param AdminCmsPage $adminCmsPage
     * @return void
     */
    public function verifyThatEachFieldOnTheContentPageWorks(
        AdminStep $I,
        AdminCmsPage $adminCmsPage
    ) 
    {
        $I->wantTo('verify that I can use all of the fields on the page.');
        $pageData = $I->getContentPage();

        $adminCmsPage->clickOnEnablePageToggle();

        $adminCmsPage->enterPageTitle($pageData['pageTitle']);

        $adminCmsPage->clickOnPageContent();
        $adminCmsPage->enterPageContentHeading($pageData['contentHeading']);
        $adminCmsPage->enterPageContentBody($pageData['contentBody']);

        $adminCmsPage->clickOnPageSearchEngineOptimisation();
        $adminCmsPage->enterUrlKey($pageData['urlKey']);
        $adminCmsPage->enterMetaTitle($pageData['metaTitle']);
        $adminCmsPage->enterMetaKeywords($pageData['metaKeywords']);
        $adminCmsPage->enterMetaDescription($pageData['metaDescription']);

        $adminCmsPage->clickOnPagePageInWebsites();
        $adminCmsPage->selectDefaultStoreView();

        $adminCmsPage->clickOnPageDesign();
        $adminCmsPage->selectLayout1Column();
        $adminCmsPage->enterLayoutUpdateXml($pageData['layoutUpdateXml']);

        $adminCmsPage->clickOnPageCustomDesignUpdate();
        $adminCmsPage->enterFrom($pageData['from']);
        $adminCmsPage->enterTo($pageData['to']);
        $adminCmsPage->selectNewThemeMagentoLuma();
        $adminCmsPage->selectNewLayout3Columns();

        $adminCmsPage->verifyPageTitle($pageData['pageTitle']);

        $adminCmsPage->verifyPageContentHeading($pageData['contentHeading']);
        $adminCmsPage->verifyPageContentBody($pageData['contentBody']);

        $adminCmsPage->verifyUrlKey($pageData['urlKey']);
        $adminCmsPage->verifyMetaTitle($pageData['metaTitle']);
        $adminCmsPage->verifyMetaKeywords($pageData['metaKeywords']);
        $adminCmsPage->verifyMetaDescription($pageData['metaDescription']);

        $adminCmsPage->verifyDefaultStoreView();

        $adminCmsPage->verifyLayout1Column();
        $adminCmsPage->verifyLayoutUpdateXml($pageData['layoutUpdateXml']);

        $adminCmsPage->verifyFrom($pageData['from']);
        $adminCmsPage->verifyTo($pageData['to']);
        $adminCmsPage->verifyNewThemeMagentoLuma();
        $adminCmsPage->verifyNewLayout3Columns();
    }

    /**
     * Allure annotations
     * @Title("Create a basic Content Page")
     * @Description("Enter text into the REQUIRED fields, SAVE the content and VERIFY it on the Storefront.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminCmsPage", value = "$adminCmsPage")
     * @Parameter(name = "StorefrontCmsPage", value = "$storefrontCmsPage")
     *
     * Codeception annotations
     * @group add
     * @param AdminStep $I
     * @param AdminCmsGrid $adminCmsGrid
     * @param AdminCmsPage $adminCmsPage
     * @param StorefrontCmsPage $storefrontCmsPage
     * @return void
     */
    public function createContentPageTest(
        AdminStep $I,
        AdminCmsGrid $adminCmsGrid,
        AdminCmsPage $adminCmsPage,
        StorefrontCmsPage $storefrontCmsPage
    )
    {
        $I->wantTo('verify content page in admin');
        $pageData = $I->getContentPage();

        $adminCmsPage->clickOnPageContent();
        $adminCmsPage->enterPageTitle($pageData['pageTitle']);
        $adminCmsPage->enterPageContentHeading($pageData['contentHeading']);
        $adminCmsPage->enterPageContentBody($pageData['contentBody']);

        $adminCmsPage->clickOnPageSearchEngineOptimisation();
        $adminCmsPage->enterUrlKey($pageData['urlKey']);

        $adminCmsPage->savePage();
        $adminCmsPage->seeSaveSuccessMessage();

        $I->openNewTabGoToVerify($pageData['urlKey']);
        $storefrontCmsPage->verifyPageContentTitle($pageData['contentHeading']);
        $storefrontCmsPage->verifyPageContentBody($pageData['contentBody']);
        $I->closeNewTab();

        $adminCmsGrid->performSearchByKeyword($pageData['urlKey']);
        $adminCmsGrid->clickOnActionEditFor($pageData['urlKey']);
        $adminCmsPage->clickOnPageContent();
        $adminCmsPage->clickOnPageSearchEngineOptimisation();

        $adminCmsPage->verifyPageTitle($pageData['pageTitle']);

        $adminCmsPage->verifyPageContentHeading($pageData['contentHeading']);
        $adminCmsPage->verifyPageContentBody($pageData['contentBody']);
        $adminCmsPage->verifyUrlKey($pageData['urlKey']);
    }
}