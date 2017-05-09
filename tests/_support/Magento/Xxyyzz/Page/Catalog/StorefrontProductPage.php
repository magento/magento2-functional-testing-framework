<?php
namespace Magento\Xxyyzz\Page\Catalog;

use Magento\Xxyyzz\Page\AbstractFrontendPage;

class StorefrontProductPage extends AbstractFrontendPage
{
    public static $addToCartButton              = '#product-addtocart-button';

    /**
     * Product data.
     */
    public static $productName                  = '.base';
    public static $productPrice                 = '.price';
    public static $productStockStatus           = '.stock[title=Availability]>span';
    public static $productSku                   = '.product.attribute.sku>.value';

    /**
     * Product options data.
     */
    public static $productOptionsDropDown       = '#product-options-wrapper .super-attribute-select';

    public function amOnProductPage($productUrlKey)
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::route($productUrlKey . '.html'));
        $I->waitForPageLoad();
    }

    public function seeProductNameInPage($name)
    {
        $I = $this->acceptanceTester;
        $I->seeElement(sprintf(self::$productName, $name));
    }

    public function seeProductPriceInPage($price)
    {
        $I = $this->acceptanceTester;
        $actualPrice = $I->grabTextFrom(sprintf(self::$productPrice));
        $actualPrice = $I->parseFloat(substr($actualPrice, 1));
        $price = $I->parseFloat($price);
        $pos = strpos($actualPrice, '.');
        if ( $pos !== false) {
            $actualPrice = substr($actualPrice, 0, $pos+3);
        }
        $I->assertEquals($price, $actualPrice);
    }

    public function seeProductStockStatusInPage($status)
    {
        $I = $this->acceptanceTester;
        $I->seeElement(sprintf(self::$productStockStatus, $status));
    }

    public function seeProductSkuInPage($sku)
    {
        $I = $this->acceptanceTester;
        $I->seeElement(sprintf(self::$productSku, $sku));
    }

    public function selectProductOption($option)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$productOptionsDropDown, $option);
    }

    /**
     * @param array $options
     */
    public function seeProductOptions(array $options)
    {
        $I = $this->acceptanceTester;
        foreach ($options as $option) {
            for($c = 2; $c < count($options)+2; $c++) {
                try {
                    $I->see($option, self::$productOptionsDropDown . ' option:nth-child('. strval($c). ')');
                    break;
                } catch (\PHPUnit_Framework_AssertionFailedError $e) {
                    continue;
                }
            }
        }
    }
}
