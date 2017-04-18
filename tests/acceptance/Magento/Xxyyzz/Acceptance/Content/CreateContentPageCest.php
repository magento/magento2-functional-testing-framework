<?php
namespace Magento\Xxyyzz\Acceptance\Content;

use Magento\Xxyyzz\Page\Content\Storefront\StorefrontCMSPage;
use Magento\Xxyyzz\Step\Backend\AdminStep;
use Magento\Xxyyzz\Page\Content\Admin\AdminCMSPage;
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
 * @group content
 * @group pages
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class CreateContentPageCest
{
    public function _before(
        AdminStep $I,
        AdminCMSPage $adminCMSPage
    )
    {
        $I->am('an Admin');
        $I->loginAsAdmin();
        $adminCMSPage->amOnAdminCMSPage();
        $adminCMSPage->clickOnAddNewPageButton();
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
     * @Parameter(name = "AdminCMSPage", value = "$adminCMSPage")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param AdminCMSPage $adminCMSPage
     * @return void
     */
    public function verifyThatEachFieldOnTheContentPageWorks(
        AdminStep $I,
        AdminCMSPage $adminCMSPage
    ) 
    {
        $I->wantTo('verify that I can use all of the fields on the page.');
        $pageData = $I->getContentPage();

        $adminCMSPage->clickOnEnablePageToggle();

        $adminCMSPage->enterPageTitle($pageData['pageTitle']);

        $adminCMSPage->clickOnPageContent();
        $adminCMSPage->enterPageContentHeading($pageData['contentHeading']);
        $adminCMSPage->enterPageContentBody($pageData['contentBody']);

        $adminCMSPage->clickOnPageSearchEngineOptimisation();
        $adminCMSPage->enterUrlKey($pageData['urlKey']);
        $adminCMSPage->enterMetaTitle($pageData['metaTitle']);
        $adminCMSPage->enterMetaKeywords($pageData['metaKeywords']);
        $adminCMSPage->enterMetaDescription($pageData['metaDescription']);

        $adminCMSPage->clickOnPagePageInWebsites();
        $adminCMSPage->selectDefaultStoreView();

        $adminCMSPage->clickOnPageDesign();
        $adminCMSPage->selectLayout1Column();
        $adminCMSPage->enterLayoutUpdateXml($pageData['layoutUpdateXml']);

        $adminCMSPage->clickOnPageCustomDesignUpdate();
        $adminCMSPage->enterFrom($pageData['from']);
        $adminCMSPage->enterTo($pageData['to']);
        $adminCMSPage->selectNewThemeMagentoLuma();
        $adminCMSPage->selectNewLayout3Columns();
        
        $adminCMSPage->verifyPageTitle($pageData['pageTitle']);

        $adminCMSPage->verifyPageContentHeading($pageData['contentHeading']);
        $adminCMSPage->verifyPageContentBody($pageData['contentBody']);

        $adminCMSPage->verifyUrlKey($pageData['urlKey']);
        $adminCMSPage->verifyMetaTitle($pageData['metaTitle']);
        $adminCMSPage->verifyMetaKeywords($pageData['metaKeywords']);
        $adminCMSPage->verifyMetaDescription($pageData['metaDescription']);

        $adminCMSPage->verifyDefaultStoreView();

        $adminCMSPage->verifyLayout1Column();
        $adminCMSPage->verifyLayoutUpdateXml($pageData['layoutUpdateXml']);

        $adminCMSPage->verifyFrom($pageData['from']);
        $adminCMSPage->verifyTo($pageData['to']);
        $adminCMSPage->verifyNewThemeMagentoLuma();
        $adminCMSPage->verifyNewLayout3Columns();
    }

    /**
     * Allure annotations
     * @Title("Create a new Content - Page using the REQUIRED fields only.")
     * @Description("Enter text into the REQUIRED fields, SAVE the content and VERIFY it on the Storefront.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminCMSPage", value = "$adminCMSPage")
     * @Parameter(name = "StorefrontCMSPage", value = "$storefrontCMSPage")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param AdminCMSPage $adminCMSPage
     * @param StorefrontCMSPage $storefrontCMSPage
     * @return void
     */
    public function createContentPageTest(
        AdminStep $I,
        AdminCMSPage $adminCMSPage,
        StorefrontCMSPage $storefrontCMSPage
    ) 
    {
        $I->wantTo('verify content page in admin');
        $page = $I->getContentPage();

        $adminCMSPage->clickOnPageContent();
        $adminCMSPage->enterPageTitle($page['pageTitle']);
        $adminCMSPage->enterPageContentHeading($page['contentHeading']);
        $adminCMSPage->enterPageContentBody($page['contentBody']);
        $adminCMSPage->clickOnPageSearchEngineOptimisation();
        $adminCMSPage->enterUrlKey($page['urlKey']);

        $adminCMSPage->savePage();
        $adminCMSPage->seeSaveSuccessMessage();
        
        $I->openNewTabGoToVerify($page['urlKey']);
        $storefrontCMSPage->verifyPageContentTitle($page['contentHeading']);
        $storefrontCMSPage->verifyPageContentBody($page['contentBody']);
    }
}