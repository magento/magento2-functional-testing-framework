<?php
namespace Magento\Xxyyzz\Page\Catalog;

use Magento\Xxyyzz\Page\AbstractAdminPage;

class AdminCategoryPage extends AbstractAdminPage
{
    /**
     * Include url of current page.
     */
    public static $URL = '/admin/catalog/category/';

    /**
     * Buttons in category page.
     */
    public static $addRootCategoryButton    = '#add_root_category_button';
    public static $addSubCategoryButton     = '#add_subcategory_button';

    public static $scheduleNewUpdateButton  = '#staging_update_new';
    public static $saveCategoryButton       = '#save';

    public static $categoryContentToggle
        = '.fieldset-wrapper[data-index=content] .fieldset-wrapper-title[data-state-collapsible=%s]';
    public static $categorySearchEngineOptimToggle
        = '.fieldset-wrapper[data-index=search_engine_optimization] .fieldset-wrapper-title[data-state-collapsible=%s]';

    /**
     * Category data fields.
     */
    public static $categoryName             = '.admin__control-text[name=name]';
    public static $categoryUrlKey           = '.admin__control-text[name=url_key]';

    public function amOnAdminCategoryPage($param = '')
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::route($param));
        $I->waitForPageLoad();
    }

    public function amOnAdminCategoryPageById($id)
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::$URL . 'edit/id/' . $id);
        $I->waitForPageLoad();
    }

    public function addRootCategory()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$addRootCategoryButton);
        $I->waitForPageLoad();
    }

    public function addSubCategory()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$addSubCategoryButton);
        $I->waitForPageLoad();
    }

    public function clickOnContent()
    {
        self::clickOnCollapsibleArea('Content');
    }

    public function clickOnDisplaySettings()
    {
        self::clickOnCollapsibleArea('Display Settings');
    }

    public function clickOnSearchEngineOptimization()
    {
        self::clickOnCollapsibleArea('Search Engine Optimization');
    }

    public function clickOnProductsInCategory()
    {
        self::clickOnCollapsibleArea('Products in Category');
    }

    public function clickOnDesign()
    {
        self::clickOnCollapsibleArea('Design');
    }

    public function clickOnScheduleDesignUpdate()
    {
        self::clickOnCollapsibleArea('Schedule Design Update');
    }

    public function fillFieldCategoryName($name)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$categoryName, $name);
    }

    public function fillFieldCategoryUrlKey($name)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$categoryUrlKey, $name);
    }

    public function saveCategory()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$saveCategoryButton);
        $I->waitForPageLoad();
    }

    public function addBasicCategory($categoryDetails)
    {
        self::addRootCategory();

        self::fillFieldCategoryName($categoryDetails['categoryName']);

        self::clickOnSearchEngineOptimization();
        self::fillFieldCategoryUrlKey($categoryDetails['urlKey']);

        self::saveCategory();
    }
}
