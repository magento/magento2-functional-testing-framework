<?php
namespace Magento\Xxyyzz\Page\Catalog\Admin;

use Magento\Xxyyzz\Page\AbstractAdminPage;

class AdminProductGridPage extends AbstractAdminPage
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
    public static $filterClearAllButton         = '.admin__data-grid-header button[data-action=grid-filter-reset]';
    public static $filterExpanded               = '.admin__data-grid-filters-wrap._show';
    public static $filterExpandButton
        = '.admin__data-grid-outer-wrap>.admin__data-grid-header button[data-action=grid-filter-expand]';
    public static $filterApplyButton
        = '.admin__data-grid-filters-footer button[data-action=grid-filter-apply]';
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

    /**
     * UI map for admin product grid.
     */
    public static $gridNthRow
        = '.admin__data-grid-outer-wrap>.admin__data-grid-wrap tbody tr:nth-child(%s)';

    public function amOnAdminProductGridPage()
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::$URL);
        $I->waitForElementNotVisible(self::$productGridProductListingLoadingSpinner, $this->pageLoadTimeout);
    }

    public function clickAddNewProductPage()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$addNewProductButton);
    }

    public function searchBySku($sku)
    {
        $I = $this->acceptanceTester;
        try {
            $I->waitForElementNotVisible(self::$productGridProductListingLoadingSpinner, $this->pageLoadTimeout);
            $I->click(self::$filterClearAllButton);
        } catch (\Codeception\Exception\ElementNotFound $e) {
        }
        try {
            $I->waitForElementNotVisible(self::$productGridNotificationLoadingSpinner, $this->pageLoadTimeout);
            $I->waitForElementNotVisible(self::$productGridProductListingLoadingSpinner, $this->pageLoadTimeout);
            $I->click(self::$filterExpandButton);
            $I->waitForElementNotVisible(self::$productGridProductListingLoadingSpinner, $this->pageLoadTimeout);
        } catch (\Codeception\Exception\ElementNotFound $e) {
        }

        $I->fillField(self::$filterProductSku, $sku);
        $I->click(self::$filterApplyButton);
        $I->waitForElementNotVisible(self::$productGridProductListingLoadingSpinner, $this->pageLoadTimeout);
    }

    public function containsInNthRow($n, $text)
    {
        $I = $this->acceptanceTester;
        return $I->see($text, sprintf(self::$gridNthRow, $n));
    }
}
