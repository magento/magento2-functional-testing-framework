<?php
namespace Magento\Xxyyzz\Page\Catalog;

class ProductPage
{
    // include url of current page
    public static $URL = '/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $pageTitle                    = '.page-title>span';
    public static $productFormLoadingSpinner    = './/*[@data-component=\'product_form.product_form\']';
    public static $productName                  = '.page-title>span';
    public static $productSku                   = '."product attribute sku" div';
    public static $productPrice                 = '.price';
    public static $productStockStatus           = '.product-info-stock-sku span';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL . $param;
    }
}
