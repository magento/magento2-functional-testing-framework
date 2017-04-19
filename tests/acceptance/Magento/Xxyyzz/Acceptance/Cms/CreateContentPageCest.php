<?php
namespace Magento\Xxyyzz\Acceptance\Cms;

use Magento\Xxyyzz\Page\Cms\StorefrontCmsPage;
use Magento\Xxyyzz\Step\Backend\AdminStep;
use Magento\Xxyyzz\Page\Cms\AdminCmsPage;
use Magento\Xxyyzz\Page\AbstractFrontendPage;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Model\SeverityLevel;

/**
 * Class CreateContentPageCest
 *
 * Allure annotations
 * @Stories({"Content - Page"})
 * @Features({"Pages"})
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
     * @Title("Enter text into every field on the Content - Page.")
     * @Description("Enter text into ALL fields and verify the contents of the fields.")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminCmsPage", value = "$adminCmsPage")
     *
     * Codeception annotations
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
     * @Title("Create a new Content - Page using the REQUIRED fields only.")
     * @Description("Enter text into the REQUIRED fields, SAVE the content and VERIFY it on the Storefront.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminCmsPage", value = "$adminCmsPage")
     * @Parameter(name = "StorefrontCmsPage", value = "$storefrontCmsPage")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param AdminCmsPage $adminCmsPage
     * @param StorefrontCmsPage $storefrontCmsPage
     * @return void
     */
    public function createContentPageTest(
        AdminStep $I,
        AdminCmsPage $adminCmsPage,
        StorefrontCmsPage $storefrontCmsPage
    ) 
    {
        $I->wantTo('verify content page in admin');
        $page = $I->getContentPage();

        $adminCmsPage->clickOnPageContent();
        $adminCmsPage->enterPageTitle($page['pageTitle']);
        $adminCmsPage->enterPageContentHeading($page['contentHeading']);
        $adminCmsPage->enterPageContentBody($page['contentBody']);
        $adminCmsPage->clickOnPageSearchEngineOptimisation();
        $adminCmsPage->enterUrlKey($page['urlKey']);

        $adminCmsPage->savePage();
        $adminCmsPage->seeSaveSuccessMessage();
        
        $I->openNewTabGoToVerify($page['urlKey']);
        $storefrontCmsPage->verifyPageContentTitle($page['contentHeading']);
        $storefrontCmsPage->verifyPageContentBody($page['contentBody']);
    }
}