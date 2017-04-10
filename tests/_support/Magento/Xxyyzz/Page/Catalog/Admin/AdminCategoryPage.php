<?php
namespace Magento\Xxyyzz\Page\Catalog\Admin;

use Magento\Xxyyzz\AcceptanceTester;

class AdminCategoryPage
{
    // include url of current page
    public static $URL = '/admin/catalog/category/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $pageTitle                        = '.page-title';
    public static $categoryFormLoadingSpinner       =
        '.admin__form-loading-mask[data-component="category_form.category_form"] .spinner';
    public static $scheduleNewUpdateButton          = '#staging_update_new';
    public static $addRootCategoryButton            = '#add_root_category_button';
    public static $addSubCategoryButton             = '#add_subcategory_button';
    public static $categoryName                     = '.admin__field[data-index=name] input';
    public static $categoryUrlKey                   = '.admin__field[data-index=url_key] input';
    public static $categoryContentToggle            =
        '.fieldset-wrapper[data-index=content] .fieldset-wrapper-title[data-state-collapsible=%s]';
    public static $categorySearchEngineOptimToggle  =
        '.fieldset-wrapper[data-index=search_engine_optimization] .fieldset-wrapper-title[data-state-collapsible=%s]';
    public static $saveCategoryButton               = '#save';
    public static $catagorySavedSpinner          = '.popup.popup-loading';
    public static $catagorySaveSuccessMessage    = '.message.message-success.success';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

    public function amOnAdminCategoryPage(AcceptanceTester $I, $param = '')
    {
        $I->amOnPage(self::route($param));
        $I->waitForElementNotVisible(self::$categoryFormLoadingSpinner, 30); // secs
    }

    public function amOnAdminCategoryPageById(AcceptanceTester $I, $id)
    {
        $I->amOnPage(self::$URL . 'edit/id/' . $id);
        $I->waitForElementNotVisible(self::$categoryFormLoadingSpinner, 30); // secs
    }

    public function seeCategoryNameInPageTitle(AcceptanceTester $I, $name)
    {
        $I->see($name, self::$pageTitle);
    }

    public function addRootCategory(AcceptanceTester $I)
    {
        $I->click(self::$addRootCategoryButton);
    }

    public function addSubCategory(AcceptanceTester $I)
    {
        $I->click(self::$addSubCategoryButton);
        $I->waitForElementNotVisible(self::$categoryFormLoadingSpinner, 30); // secs
    }

    public function fillFieldCategoryName(AcceptanceTester $I, $name)
    {
        $I->fillField(self::$categoryName, $name);
    }

    public function fillFieldCategoryUrlKey(AcceptanceTester $I, $name)
    {
        try {
            $I->click(sprintf(self::$categorySearchEngineOptimToggle, 'closed'));
        } catch (\Exception $e) {
        }
        $I->fillField(self::$categoryUrlKey, $name);
    }

    public function saveCategory(AcceptanceTester $I)
    {
        $I->click(self::$saveCategoryButton);
        $I->waitForElementNotVisible(self::$catagorySavedSpinner);
        $I->waitForElementVisible(self::$catagorySaveSuccessMessage);
    }
}
