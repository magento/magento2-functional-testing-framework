<?php
namespace Magento\Xxyyzz\Page\Catalog;

use Magento\Xxyyzz\Page\AbstractFrontendPage;

class ProductPage extends AbstractFrontendPage
{
    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $pageTitle                    = '.page-title>span';
    public static $productFormLoadingSpinner    = './/*[@data-component=\'product_form.product_form\']';
    public static $productName                  = '.page-title>span';
    public static $productSku                   = '."product attribute sku" div';
    public static $productPrice                 = '.price';
    public static $productStockStatus           = '.product-info-stock-sku span';
}
