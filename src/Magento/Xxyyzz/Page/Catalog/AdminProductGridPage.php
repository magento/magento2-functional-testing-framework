<?php
namespace Magento\Xxyyzz\Page\Catalog;

use Magento\Xxyyzz\Page\AdminGridPage;

class AdminProductGridPage extends AdminGridPage
{
    /**
     * Include url of current page.
     */
    public static $URL = '/admin/catalog/product';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $addNewProductButton               = '#add_new_product-button';
    public static $addNewProductDropDownButton       = '#add_new_product .add.action-toggle';

    public static $addNewSimpleProductOption         = '.item[data-ui-id="products-list-add-new-product-button-item-simple"]';
    public static $addNewConfigurableProductOption   = '.item[data-ui-id="products-list-add-new-product-button-item-configurable"]';
    public static $addNewGroupedProductOption        = '.item[data-ui-id="products-list-add-new-product-button-item-grouped"]';
    public static $addNewVirtualProductOption        = '.item[data-ui-id="products-list-add-new-product-button-item-virtual"]';
    public static $addNewBundleProductOption         = '.item[data-ui-id="products-list-add-new-product-button-item-bundle"]';
    public static $addNewDownloadableProductOption   = '.item[data-ui-id="products-list-add-new-product-button-item-downloadable"]';

    /**
     * UI map for admin data grid filter section.
     */
    public static $filterIdFromField                 = '.admin__control-text[name="entity_id[from]"]';
    public static $filterIdToField                   = '.admin__control-text[name="entity_id[to]"]';
    public static $filterPriceFromField              = '.admin__control-text[name="price[from]"]';
    public static $filterPriceToField                = '.admin__control-text[name="price[to]"]';
    public static $filterQuantityFromField           = '.admin__control-text[name="qty[from]"]';
    public static $filterQuantityToField             = '.admin__control-text[name="qty[to]"]';
    public static $filterStoreViewDropDown           = '.admin__control-select[name="store_id"]';
    public static $filterProductName                 = '.admin__form-field input[name=name]';
    public static $filterProductTypeDropDown         = '.admin__control-select[name="type_id"]';
    public static $filterProductAttributeSetDropDown = '.admin__control-select[name="attribute_set_id"]';
    public static $filterProductSkuField             = '.admin__form-field input[name=sku]';
    public static $filterProductVisibilityDropDown   = '.admin__control-select[name="visibility"]';
    public static $filterProductStatusDropDown       = '.admin__control-select[name="status"]';

    public function amOnAdminProductGridPage()
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::$URL);
        $I->waitForPageLoad();
    }

    public function clickAddNewProductButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$addNewProductButton);
        $I->waitForPageLoad();
    }

    public function clickOnAddNewProductDropDown()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$addNewProductDropDownButton);
    }

    public function clickOnAddSimpleProductOption()
    {
        $I = $this->acceptanceTester;
        self::clickOnAddNewProductDropDown();
        $I->click(self::$addNewSimpleProductOption);
    }

    public function clickOnAddConfigurableProductOption()
    {
        $I = $this->acceptanceTester;
        self::clickOnAddNewProductDropDown();
        $I->click(self::$addNewConfigurableProductOption);
    }

    public function clickOnAddGroupedProductOption()
    {
        $I = $this->acceptanceTester;
        self::clickOnAddNewProductDropDown();
        $I->click(self::$addNewGroupedProductOption);
    }

    public function clickOnAddVirtualProductOption()
    {
        $I = $this->acceptanceTester;
        self::clickOnAddNewProductDropDown();
        $I->click(self::$addNewVirtualProductOption);
    }

    public function clickOnAddBundleProductOption()
    {
        $I = $this->acceptanceTester;
        self::clickOnAddNewProductDropDown();
        $I->click(self::$addNewBundleProductOption);
    }

    public function clickOnAddDownloadableProductOption()
    {
        $I = $this->acceptanceTester;
        self::clickOnAddNewProductDropDown();
        $I->click(self::$addNewDownloadableProductOption);
    }

    public function searchBySku($sku)
    {
        $this->searchAndFiltersByValue($sku, self::$filterProductSkuField);
    }
}
