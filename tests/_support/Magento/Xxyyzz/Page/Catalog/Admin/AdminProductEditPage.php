<?php
namespace Magento\Xxyyzz\Page\Catalog\Admin;

use Magento\Xxyyzz\AcceptanceTester;

class AdminProductEditPage
{
    // include url of current page
    public static $URL = '/admin/catalog/product/edit/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $pageTitle            = '.page-title';
    public static $productFormSpinner   = './/*[@data-component=\'product_form.product_form\']';
    public static $producAttributeSet   =
        './/*[@data-index=\'attribute_set_id\']//*[@class=\'action-select admin__action-multiselect\']';
    public static $productName          = 'div[data-index="name"] input';
    public static $productSku           = './/*[@data-index=\'sku\']//input';
    public static $productPrice         = './/*[@data-index=\'price\']//input';
    public static $productQuantity      = './/*[@data-index=\'quantity_and_stock_status_qty\']//input';
    public static $productStockStatus   = './/*[@data-index=\'quantity_and_stock_status\']//select';
    public static $productCategories    =
        './/*[@data-index=\'category_ids\']//*[@class=\'action-select admin__action-multiselect\']';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL . $param;
    }

    public function amOnAdminProductPageById(AcceptanceTester $I, $id)
    {
        $I->amOnPage(self::$URL . 'id/' . $id);
        $I->waitForElementNotVisible(self::$productFormSpinner, 30); // secs

    }

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
}
