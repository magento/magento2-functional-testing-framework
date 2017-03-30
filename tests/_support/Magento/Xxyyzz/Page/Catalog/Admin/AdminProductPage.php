<?php
namespace Magento\Xxyyzz\Page\Catalog\Admin;

use Magento\Xxyyzz\AcceptanceTester;

class AdminProductPage
{
    // include url of current page
    public static $URL = '/admin/catalog/product/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $pageTitle                    = '.page-title';
    public static $productFormLoadingSpinner    =
        '.admin__form-loading-mask[data-component="product_form.product_form"] .spinner';
    public static $producAttributeSet           =
        '.admin__field[data-index=attribute_set_id] .admin__action-multiselect-text';
    public static $productName                  = '.admin__field[data-index=name] input';
    public static $productSku                   = '.admin__field[data-index=sku] input';
    public static $productPrice                 = '.admin__field[data-index=price] input';
    public static $productQuantity              = '.admin__field[data-index=quantity_and_stock_status_qty] input';
    public static $productStockStatus           = '.admin__field[data-index=quantity_and_stock_status] select';
    public static $productCategories            =
        '.admin__field[data-index=category_ids] .admin__action-multiselect-text';
    public static $productTaxClass              = '.admin__field[data-index=tax_class_id] select';
    public static $productSaveButton            = '#save-button';
    public static $productSavedSpinner          = '.popup.popup-loading';
    public static $productSaveSuccessMessage    = '.message.message-success.success';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL . $param;
    }

    public function amOnAdminNewProductPage(AcceptanceTester $I)
    {
        $I->seeInCurrentUrl(static::$URL . 'new');
        $I->waitForElementNotVisible(self::$productFormLoadingSpinner, 30); // secs
    }

    public function amOnAdminProductPageById(AcceptanceTester $I, $id)
    {
        $I->amOnPage(self::route('edit/id/' . $id));
        $I->waitForElementNotVisible(self::$productFormLoadingSpinner, 30); // secs
    }

    // Assert existing product
    public function seeProductNameInPageTitle(AcceptanceTester $I, $name)
    {
        $I->see($name, self::$pageTitle);
    }

    public function seeProductAttributeSet(AcceptanceTester $I, $name)
    {
        $I->seeOptionIsSelected(self::$producAttributeSet, $name);
    }

    public function seeProductName(AcceptanceTester $I, $name)
    {
        $I->seeInField(self::$productName, $name);
    }

    public function seeProductSku(AcceptanceTester $I, $name)
    {
        $I->seeInField(self::$productSku, $name);
    }

    public function seeProductPrice(AcceptanceTester $I, $name)
    {
        $I->seeInField(self::$productPrice, $name);
    }

    public function seeProductQuantity(AcceptanceTester $I, $name)
    {
        $I->seeInField(self::$productQuantity, $name);
    }

    public function seeProductStockStatus(AcceptanceTester $I, $name)
    {
        $I->seeOptionIsSelected(self::$productStockStatus, $name);
    }

    public function seeProductCategories(AcceptanceTester $I, $name)
    {
        $I->seeInFormFields(self::$productCategories, ['multiselect' => [$name]]);
    }

    // Fill new product
    public function fillFieldProductName(AcceptanceTester $I, $name)
    {
        $I->fillField(self::$productName, $name);
    }

    public function fillFieldProductSku(AcceptanceTester $I, $name)
    {
        $I->fillField(self::$productSku, $name);
    }

    public function fillFieldProductPrice(AcceptanceTester $I, $name)
    {
        $I->fillField(self::$productPrice, $name);
    }

    public function fillFieldProductQuantity(AcceptanceTester $I, $name)
    {
        $I->fillField(self::$productQuantity, $name);
    }

    public function selectProductStockStatus(AcceptanceTester $I, $name)
    {
        $I->selectOption(self::$productStockStatus, $name);
    }

    public function fillFieldProductCategories(AcceptanceTester $I, $name)
    {
        $I->seeInFormFields(self::$productCategories, ['multiselect' => [$name]]);
    }

    public function saveProduct(AcceptanceTester $I)
    {
        $I->click(self::$productSaveButton);
        $I->waitForElementNotVisible(self::$productSavedSpinner);
        $I->waitForElementVisible(self::$productSaveSuccessMessage);
    }
}
