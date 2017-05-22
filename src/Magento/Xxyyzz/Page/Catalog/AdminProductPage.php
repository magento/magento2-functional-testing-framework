<?php
namespace Magento\Xxyyzz\Page\Catalog;

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
    public static $productAddProductButton      = '#add_new_product-button';
    public static $productSaveButton            = '#save-button';

    /**
     * Product data fields.
     */
    public static $productName                  = '.admin__field[data-index=name] input';
    public static $productSku                   = '.admin__field[data-index=sku] input';
    public static $productPricePrefix           = '.admin__field[data-index=price] .admin__addon-prefix>span';
    public static $productPrice                 = '.admin__field[data-index=price] input';
    public static $productQuantity              = '.admin__field[data-index=quantity_and_stock_status_qty] input';
    public static $productStockStatus           = '.admin__field[data-index=quantity_and_stock_status] select';
    public static $productTaxClass              = '.admin__field[data-index=tax_class_id] select';
    public static $producAttributeSetMultiSelect= '.admin__field[data-index=attribute_set_id]';
    public static $producAttributeMultiSelectText
        = '.admin__field[data-index=attribute_set_id] .action-select.admin__action-multiselect>div';
    public static $producCategoriesMultiSelect  = '.admin__field[data-index=category_ids]';
    public static $producCategoriesMultiSelectText
        = '.admin__field[data-index=category_ids] .admin__action-multiselect-crumb:nth-child(%s)>span';
    public static $productCountryOfManufacture  = '.admin__field[data-index=country_of_manufacture] select';

    /**
     * Product form loading spinner.
     */
    public static $productFormLoadingSpinner
        = '.admin__form-loading-mask[data-component="product_form.product_form"] .spinner';

    public static $productUrlKey                   = '.admin__field[data-index=url_key] input';

    public function amOnAdminNewProductPage()
    {
        $I = $this->acceptanceTester;
        $I->waitForPageLoad();
        $I->seeInCurrentUrl(static::$URL . 'new');
    }

    public function amOnAdminEditProductPageById($id)
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::route('edit/id/' . $id));
        $I->waitForPageLoad();
    }

    public function clickOnAddProductButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$productAddProductButton);
        $I->waitForPageLoad();
    }

    public function seeProductAttributeSet($name)
    {
        $I = $this->acceptanceTester;
        $I->assertEquals($name, $I->grabTextFrom(self::$producAttributeMultiSelectText));
    }

    public function seeProductName($name)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$productName, $name);
    }

    public function seeProductSku($sku)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$productSku, $sku);
    }

    public function seeProductPrice($price)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$productPrice, $I->formatMoney($price)['number']);
    }

    public function seeProductQuantity($qty)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$productQuantity, $qty);
    }

    public function seeProductStockStatus($status)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$productStockStatus, $status);
    }

    /**
     * @param array $names
     */
    public function seeProductCategories(array $names)
    {
        $I = $this->acceptanceTester;
        $count = 2;
        foreach ($names as $name) {
            $I->assertEquals($name, $I->grabTextFrom(sprintf(self::$producCategoriesMultiSelectText, $count)));
            $count += 1;
        }
    }

    public function seeProductUrlKey($urlKey)
    {
        $I = $this->acceptanceTester;
        $this->expandCollapsibleArea('search-engine-optimization');
        $I->seeInField(self::$productUrlKey, $urlKey);
    }

    // Fill new product
    public function selectProductAttributeSet($name)
    {
        $I = $this->acceptanceTester;
        $I->searchAndMultiSelectOption(self::$producAttributeSetMultiSelect, [$name]);
    }

    public function fillFieldProductName($name)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$productName, $name);
    }

    public function fillFieldProductSku($sku)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$productSku, $sku);
    }

    public function fillFieldProductPrice($price)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$productPrice, $price);
    }

    public function fillFieldProductQuantity($qty)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$productQuantity, $qty);
    }

    public function selectProductStockStatus($status)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$productStockStatus, $status);
    }

    public function fillFieldProductUrlKey($urlKey)
    {
        $this->expandCollapsibleArea('search-engine-optimization');
        $I = $this->acceptanceTester;
        $I->fillField(self::$productUrlKey, $urlKey);
    }

    /**
     * @param array $names
     */
    public function selectProductCategories(array $names)
    {
        $I = $this->acceptanceTester;
        $I->searchAndMultiSelectOption(self::$producCategoriesMultiSelect, $names, true);
    }

    public function saveProduct()
    {
        $I = $this->acceptanceTester;
        $I->performOn(self::$productSaveButton, ['click' => self::$productSaveButton]);
        $I->waitForPageLoad();
    }

    public function addBasicProductUnderCategory($productData, $categoryData)
    {
        self::clickOnAddProductButton();

        self::fillFieldProductName($productData['productName']);
        self::fillFieldProductSku($productData['sku']);
        self::fillFieldProductPrice($productData['price']);
        self::selectProductCategories(array($categoryData['categoryName']));

        self::saveProduct();
    }
}
