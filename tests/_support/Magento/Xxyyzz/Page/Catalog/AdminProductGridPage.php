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
    public static $addNewProductButton          = '#add_new_product-button';

    /**
     * UI map for admin data grid filter section.
     */
    public static $filterIdFrom                 = '.admin__form-field input[name="entity_id[from]"]';
    public static $filterIdTo                   = '.admin__form-field input[name="entity_id[to]"]';
    public static $filterPriceFrom              = '.admin__form-field input[name="price[from]"]';
    public static $filterPriceTo                = '.admin__form-field input[name="price[to]"]';
    public static $filterProductName            = '.admin__form-field input[name=name]';
    public static $filterProductSku             = '.admin__form-field input[name=sku]';

    /**
     * Spinners.
     */
    public static $productGridProductListingLoadingSpinner
        = '.admin__data-grid-loading-mask[data-component="product_listing.product_listing.product_columns"]>.spinner';
    public static $productGridNotificationLoadingSpinner
        = '.admin__data-grid-loading-mask[data-component="notification_area.notification_area.columns"]>.spinner';

    public function amOnAdminProductGridPage()
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::$URL);
        $I->waitForPageLoad();
    }

    public function clickAddNewProductPage()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$addNewProductButton);
    }

    public function searchBySku($sku)
    {
        $this->searchAndFiltersByValue($sku, self::$filterProductSku);
    }
}
