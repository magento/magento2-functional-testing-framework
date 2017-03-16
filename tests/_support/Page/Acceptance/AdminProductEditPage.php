<?php
namespace Page\Acceptance;

class AdminProductEditPage
{
    // include url of current page
    public static $URL = '/admin/catalog/product/edit/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    public static $pageTitle = '.page-title';
    public static $producAttributeSet = './/*[@data-index=\'attribute_set_id\']//input';
    public static $productName = './/*[@data-index=\'name\']//input';
    public static $productSku = './/*[@data-index=\'sku\']//input';
    public static $productPrice = './/*[@data-index=\'container_price\']//input';
    public static $productQuantity = './/*[@data-index=\'quantity_and_stock_status_qty\']//input';
    public static $productStockStatus = './/*[@data-index=\'quantity_and_stock_status\']//select';
    public static $productCategories = './/*[@data-index=\'container_category_ids\']//*[@data-index=\'category_ids\']';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL . $param;
    }

    public function amOnAdminProductPageById(\AcceptanceTester $I, $id)
    {
        $I->amOnPage(self::$URL . 'id/' . $id);
    }

    public function seeProductNameInPageTitle(\AcceptanceTester $I, $name)
    {
        $I->see($name, self::$pageTitle);
    }

    public function seeProductAttributeSet(\AcceptanceTester $I, $name)
    {
        $I->see($name, self::$producAttributeSet);
    }

    public function seeProductName(\AcceptanceTester $I, $name)
    {
        $I->see($name, self::$productName);
    }

    public function seeProductSku(\AcceptanceTester $I, $name)
    {
        $I->see($name, self::$productSku);
    }

    public function seeProductPrice(\AcceptanceTester $I, $name)
    {
        $I->see($name, self::$productPrice);
    }

    public function seeProductQuantity(\AcceptanceTester $I, $name)
    {
        $I->see($name, self::$productQuantity);
    }

    public function seeProductStockStatus(\AcceptanceTester $I, $name)
    {
        $I->see($name, self::$productStockStatus);
    }

    public function seeProductCategories(\AcceptanceTester $I, $name)
    {
        $I->see($name, self::$productCategories);
    }
}
