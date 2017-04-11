<?php
namespace Magento\Xxyyzz\Page\Catalog\Admin;

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
    public static $addRootCategoryButton            = '#add_root_category_button';
    public static $addSubCategoryButton             = '#add_subcategory_button';
    public static $scheduleNewUpdateButton          = '#staging_update_new';
    public static $saveCategoryButton               = '#save';
    public static $categoryContentToggle            =
        '.fieldset-wrapper[data-index=content] .fieldset-wrapper-title[data-state-collapsible=%s]';
    public static $categorySearchEngineOptimToggle  =
        '.fieldset-wrapper[data-index=search_engine_optimization] .fieldset-wrapper-title[data-state-collapsible=%s]';

    /**
     * Category data fields.
     */
    public static $categoryName                     = '.admin__field[data-index=name] input';
    public static $categoryUrlKey                   = '.admin__field[data-index=url_key] input';

    /**
     * Category form loading spinner.
     */
    public static $categoryFormLoadingSpinner       =
        '.admin__form-loading-mask[data-component="category_form.category_form"] .spinner';

    public function amOnAdminCategoryPage($param = '')
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::route($param));
        $I->waitForElementNotVisible(self::$categoryFormLoadingSpinner, $this->pageloadTimeout);
    }

    public function amOnAdminCategoryPageById($id)
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::$URL . 'edit/id/' . $id);
        $I->waitForElementNotVisible(self::$categoryFormLoadingSpinner, $this->pageloadTimeout);
    }

    public function addRootCategory()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$addRootCategoryButton);
    }

    public function addSubCategory()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$addSubCategoryButton);
        $I->waitForElementNotVisible(self::$categoryFormLoadingSpinner, $this->pageloadTimeout);
    }

    public function fillFieldCategoryName($name)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$categoryName, $name);
    }

    public function fillFieldCategoryUrlKey($name)
    {
        $I = $this->acceptanceTester;
        try {
            $I->click(sprintf(self::$categorySearchEngineOptimToggle, 'closed'));
        } catch (\Exception $e) {
        }
        $I->fillField(self::$categoryUrlKey, $name);
    }

    public function saveCategory()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$saveCategoryButton);
        $I->waitForElementNotVisible(self::$popupLoadingSpinner);
        $I->waitForElementNotVisible(self::$categoryFormLoadingSpinner);
        $I->waitForElementVisible(self::$successMessage);
    }
}
