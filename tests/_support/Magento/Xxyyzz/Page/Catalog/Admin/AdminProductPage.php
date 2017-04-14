<?php
namespace Magento\Xxyyzz\Page\Catalog\Admin;

use Magento\Xxyyzz\Page\AbstractAdminPage;

class AdminProductPage extends AbstractAdminPage
{
    /**
     * Include url of current page.
     */
    public static $URL = '/admin/catalog/product/';

    /**
     * Buttons in product page.
     */
    public static $productSaveButton            = '#save-button';

    /**
     * Product data fields.
     */
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

    /**
     * Product form loading spinner.
     */
    public static $productFormLoadingSpinner    =
        '.admin__form-loading-mask[data-component="product_form.product_form"] .spinner';

    public function amOnAdminNewProductPage()
    {
        $I = $this->acceptanceTester;
        $I->seeInCurrentUrl(static::$URL . 'new');
        $I->waitForElementNotVisible(self::$productFormLoadingSpinner, $this->pageLoadTimeout);
    }

    public function amOnAdminEditProductPageById($id)
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::route('edit/id/' . $id));
        $I->waitForElementNotVisible(self::$productFormLoadingSpinner, $this->pageLoadTimeout);
    }

    public function seeProductAttributeSet($name)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$producAttributeSet, $name);
    }

    public function seeProductName($name)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$productName, $name);
    }

    public function seeProductSku($name)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$productSku, $name);
    }

    public function seeProductPrice($name)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$productPrice, $name);
    }

    public function seeProductQuantity($name)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$productQuantity, $name);
    }

    public function seeProductStockStatus($name)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$productStockStatus, $name);
    }

    public function seeProductCategories($name)
    {
        $I = $this->acceptanceTester;
        $I->seeInFormFields(self::$productCategories, ['multiselect' => [$name]]);
    }

    // Fill new product
    public function fillFieldProductName($name)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$productName, $name);
    }

    public function fillFieldProductSku($name)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$productSku, $name);
    }

    public function fillFieldProductPrice($name)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$productPrice, $name);
    }

    public function fillFieldProductQuantity($name)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$productQuantity, $name);
    }

    public function selectProductStockStatus($name)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$productStockStatus, $name);
    }

    public function fillFieldProductCategories($name)
    {
        $I = $this->acceptanceTester;
        $I->seeInFormFields(self::$productCategories, ['multiselect' => [$name]]);
    }

    public function saveProduct()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$productSaveButton);
        $I->waitForElementNotVisible(self::$popupLoadingSpinner);
        $I->waitForElementNotVisible(self::$productFormLoadingSpinner);
        $I->waitForElementVisible(self::$successMessage);
    }
}
