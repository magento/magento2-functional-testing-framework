<?php
namespace Magento\Xxyyzz\Page\Cms;

use Magento\Xxyyzz\Page\AbstractAdminPage;

class AdminCmsPage extends AbstractAdminPage
{
    /**
     * Include url of current page.
     */
    public static $URL = '/admin/cms/page/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $pageTitle                = '.page-title';
    public static $cmsFormLoadingSpinner    = 'div[data-bind="scope: \'cms_page_listing.cms_page_listing\'"] .spinner';
    public static $cmsBackButton            = '#back';
    public static $cmsResetButton           = '#reset-button';
    public static $cmsSaveAndContinueButton = '#save_and_continue';
    public static $cmsAddNewPageButton      = '#add';
    public static $savePageButton           = '#save';
    public static $pageSavedSpinner         = '.popup.popup-loading';
    public static $pageSaveSuccessMessage   = '.messages .success';

    public static $cmsEnablePageToggle      = 'div[data-index="is_active"] .admin__actions-switch-label';
    public static $pageTitleField           = '.admin__control-text[name="title"]';
    public static $contentHeadingField      = '.admin__control-text[name="content_heading"]';
    public static $insertWidgetButton       = '.action-add-widget';

    // TODO: Add Selectors for the "Insert Widget" section and controls

    public static $insertImageButton        = '.action-add-image';

    // TODO: Add Selectors for the "Insert Image" section and controls

    public static $insertVariableButton     = '.add-variable';

    // TODO: Add Selectors for the "Insert Variable" section and controls

    public static $contentBodyField         = '#cms_page_form_content';

    public static $urlKeyField              = '.admin__control-text[name="identifier"]';
    public static $metaTitleField           = '.admin__control-text[name="meta_title"]';
    public static $metaKeywordsField        = '.admin__control-textarea[name="meta_keywords"]';
    public static $metaDescriptionField     = '.admin__control-textarea[name="meta_description"]';

    public static $storeViewMainArea        = '.admin__control-multiselect[name="store_id"]';
    public static $storeViewMenuItems       = '.admin__control-multiselect[name="store_id"] option';

    public static $layoutButton             = '.admin__control-select[name="page_layout"]';
    public static $layoutUpdateXmlField     = '.admin__control-textarea[name="layout_update_xml"]';

    public static $fromField                = '.admin__control-text[name="custom_theme_from"]';
    public static $toField                  = '.admin__control-text[name="custom_theme_to"]';
    public static $newThemeButton           = '.admin__control-select[name="custom_theme"]';
    public static $newLayoutButton          = '.admin__control-select[name="custom_root_template"]';

    public function amOnAdminCmsPage($param = '')
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::route($param));
        $I->waitForPageLoad();
    }

    public function amOnAdminCmsPageById($id)
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::$URL . 'edit/page_id/' . $id);
        $I->waitForPageLoad();
    }

    public function seePageNameInPageTitle($name)
    {
        $I = $this->acceptanceTester;
        $I->see($name, self::$pageTitle);
    }

    public function clickOnAddNewPageButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$cmsAddNewPageButton);
        $I->waitForPageLoad();
    }

    public function clickOnEnablePageToggle()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$cmsEnablePageToggle);
    }

    public function seeEnablePageToggleIs($expectedState)
    {
        $I = $this->acceptanceTester;
        $I->seeCheckboxIsChecked(self::$cmsEnablePageToggle, $expectedState);
    }

    public function enterPageTitle($pageTitle)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$pageTitleField, $pageTitle);
    }

    public function verifyPageTitle($pageTitle)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$pageTitleField, $pageTitle);
    }

    public function enterPageContentHeading($pageContentTitle)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$contentHeadingField, $pageContentTitle);
    }

    public function verifyPageContentHeading($pageContentTitle)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$contentHeadingField, $pageContentTitle);
    }

    public function clickOnInsertWidgetButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$insertWidgetButton);
    }

    // TODO: Add Methods for the "Insert Widget" section and controls

    public function clickOnInsertImageButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$insertImageButton);
    }

    // TODO: Add Methods for the "Insert Image" section and controls

    public function clickOnInsertVariableButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$insertVariableButton);
    }

    // TODO: Add Methods for the "Insert Variable" section and controls

    public function enterPageContentBody($pageContentBody)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$contentBodyField, $pageContentBody);
    }

    public function verifyPageContentBody($pageContentBody)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$contentBodyField, $pageContentBody);
    }

    public function enterUrlKey($urlKey)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$urlKeyField, $urlKey);
    }

    public function verifyUrlKey($urlKey)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$urlKeyField, $urlKey);
    }

    public function enterMetaTitle($metaTitle)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$metaTitleField, $metaTitle);
    }

    public function verifyMetaTitle($metaTitle)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$metaTitleField, $metaTitle);
    }

    public function enterMetaKeywords($metaKeywords)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$metaKeywordsField, $metaKeywords);
    }

    public function verifyMetaKeywords($metaKeywords)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$metaKeywordsField, $metaKeywords);
    }

    public function enterMetaDescription($metaDescription)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$metaDescriptionField, $metaDescription);
    }

    public function verifyMetaDescription($metaDescription)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$metaDescriptionField, $metaDescription);
    }

    public function selectStoreView($storeView)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$storeViewMainArea, $storeView);
    }

    public function selectDefaultStoreView()
    {
        self::selectStoreView('Default Store View');
    }

    public function verifyStoreView($storeView)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$storeViewMainArea, $storeView);
    }

    public function verifyDefaultStoreView()
    {
        self::verifyStoreView('Default Store View');
    }

    public function pressShiftOnTheKeyboard()
    {
        $I = $this->acceptanceTester;
        $I->pressKey(self::$storeViewMainArea, \WebDriverKeys::SHIFT);
    }

    public function selectMultipleStoreViews($storeViews)
    {
        // TODO: Add multi-select support for the "Store View" setting
    }

    public function selectLayout($layout)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$layoutButton, $layout);
    }

    public function selectLayout1Column()
    {
        self::selectLayout('1 column');
    }

    public function selectLayout2ColumnsWithLeftBar()
    {
        self::selectLayout('2 columns with left bar');
    }

    public function selectLayout2ColumnsWithRightBar()
    {
        self::selectLayout('2 columns with right bar');
    }

    public function selectLayout3Column()
    {
        self::selectLayout('3 columns');
    }

    public function verifyLayout($layout)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$layoutButton, $layout);
    }

    public function verifyLayout1Column()
    {
        self::verifyLayout('1 column');
    }

    public function verifyLayout2ColumnsWithLeftBar()
    {
        self::verifyLayout('2 columns with left bar');
    }

    public function verifyLayout2ColumnsWithRightBar()
    {
        self::verifyLayout('2 columns with right bar');
    }

    public function verifyLayout3Column()
    {
        self::verifyLayout('3 columns');
    }

    public function enterLayoutUpdateXml($layoutXml)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$layoutUpdateXmlField, $layoutXml);
    }

    public function verifyLayoutUpdateXml($layoutXml)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$layoutUpdateXmlField, $layoutXml);
    }

    // TODO: Add calendar modal support

    public function enterFrom($fromDate)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$fromField, $fromDate);
    }

    public function verifyFrom($fromDate)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$fromField, $fromDate);
    }

    public function enterTo($toDate)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$toField, $toDate);
    }

    public function verifyTo($toDate)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$toField, $toDate);
    }

    public function selectNewTheme($newTheme)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$newThemeButton, $newTheme);
    }

    public function selectNewThemeMagentoBlank()
    {
        self::selectNewTheme('Magento Blank');
    }

    public function selectNewThemeMagentoLuma()
    {
        self::selectNewTheme('Magento Luma');
    }

    public function verifyNewTheme($newTheme)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$newThemeButton, $newTheme);
    }

    public function verifyNewThemeMagentoBlank()
    {
        self::verifyNewTheme('Magento Blank');
    }

    public function verifyNewThemeMagentoLuma()
    {
        self::verifyNewTheme('Magento Luma');
    }

    public function selectNewLayout($newLayout)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$newLayoutButton, $newLayout);
    }

    public function selectNewLayoutEmpty()
    {
        self::selectNewLayout('Empty');
    }

    public function selectNewLayout1Column()
    {
        self::selectNewLayout('1 column');
    }

    public function selectNewLayout2ColumnsWithLeftBar()
    {
        self::selectNewLayout('2 columns with left bar');
    }

    public function selectNewLayout2ColumnsWithRightBar()
    {
        self::selectNewLayout('2 columns with right bar');
    }

    public function selectNewLayout3Columns()
    {
        self::selectNewLayout('3 columns');
    }

    public function verifyNewLayout($newLayout)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$newLayoutButton, $newLayout);
    }

    public function verifyNewLayoutEmpty()
    {
        self::verifyNewLayout('Empty');
    }

    public function verifyNewLayout1Column()
    {
        self::verifyNewLayout('1 column');
    }

    public function verifyNewLayout2ColumnsWithLeftBar()
    {
        self::verifyNewLayout('2 columns with left bar');
    }

    public function verifyNewLayout2ColumnsWithRightBar()
    {
        self::verifyNewLayout('2 columns with right bar');
    }

    public function verifyNewLayout3Columns()
    {
        self::verifyNewLayout('3 columns');
    }

    public function clickOnSavePageButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$savePageButton);
        $I->waitForPageLoad();
    }

    public function seeSaveSuccessMessage()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$pageSaveSuccessMessage);
    }

    public function clickOnPageContent()
    {
        self::clickOnCollapsibleArea('Content');
    }

    public function clickOnPageSearchEngineOptimisation()
    {
        self::clickOnCollapsibleArea('Search Engine Optimisation');
    }

    public function clickOnPagePageInWebsites()
    {
        self::clickOnCollapsibleArea('Page in Websites');
    }

    public function clickOnPageDesign()
    {
        self::clickOnCollapsibleArea('Design');
    }

    public function clickOnPageCustomDesignUpdate()
    {
        self::clickOnCollapsibleArea('Custom Design Update');
    }

    public function addBasicPage($pageDetails)
    {
        self::clickOnAddNewPageButton();

        self::clickOnPageContent();

        self::enterPageTitle($pageDetails['pageTitle']);
        self::enterPageContentHeading($pageDetails['contentHeading']);
        self::enterPageContentBody($pageDetails['contentBody']);

        self::clickOnPageSearchEngineOptimisation();
        self::enterUrlKey($pageDetails['urlKey']);

        self::clickOnSavePageButton();
    }
}