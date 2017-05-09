<?php
namespace Magento\Xxyyzz\Page\Catalog;

use Magento\Xxyyzz\Page\AbstractFrontendPage;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Exception\WebDriverException;
use Codeception\Exception\ElementNotFound;

class StorefrontCategoryPage extends AbstractFrontendPage
{
    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $pageTitleHeading             = '#page-title-heading>span';

    public static $compareBlock                 = '.block.block-compare';
    public static $wishlistBlock                = '.block.block-wishlist';

    public static $viewAsGridButton             = '.modes>strong[title=Grid]';
    public static $activeGridMode               = '.modes-mode.active.mode-grid';
    public static $viewAsListButton             = '.modes>strong[title=List]';
    public static $activeListMode               = '.modes-mode.active.mode-list';
    public static $sortByButton                 = '#sorter';
    public static $totalNumberOfProductsText    = '#toolbar-amount>span:last-child';

    public static $pageNextButton               = './/*[contains(@class,"toolbar-products")][2]//a[@title="Next"]';
    public static $pagePreviousButton           = './/*[contains(@class,"toolbar-products")][2]//a[@title="Previous"]';
    public static $activePage                   = '//li[@class="item current"]//span[text()=%s]';

    public static $productListItem              = '.products.list.items.product-items>li';
    public static $productLinks                  = '//a[contains(@href, "%s.html")]';
    public static $productName                  = '//a[text()[contains(.," %s ")]]';
    public static $productPrice
        = '//a[text()[contains(.," %s ")]]/parent::strong/following-sibling::div[1]//*[@class="price"]';

    public function amOnCategoryPage($categoryUrlKey, $params = '')
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::route($categoryUrlKey . '.html' . $params));
        $I->waitForPageLoad();
    }

    public function seeCategoryNameInTitleHeading($name)
    {
        $I = $this->acceptanceTester;
        $I->see($name, self::$pageTitleHeading);
    }

    public function seeProductLinksInPage($name, $link)
    {
        $this->goToPageForProduct($name);
        $I = $this->acceptanceTester;
        $I->canSeeElement(sprintf(self::$productLinks, $link));
    }

    public function seeProductNameInPage($name)
    {
        $this->goToPageForProduct($name);
        $I = $this->acceptanceTester;
        $I->canSeeElement(sprintf(self::$productName, $name));
    }

    public function seeProductPriceInPage($name, $price)
    {
        $this->goToPageForProduct($name);
        $I = $this->acceptanceTester;
        $actualPrice = $I->parseFloat(substr($I->grabTextFrom(sprintf(self::$productPrice, $name)), 1));
        $price = $I->parseFloat($price);
        $I->assertEquals($price, $actualPrice, '', 0.01);
    }

    public function goToPageForProduct($name)
    {
        $I = $this->acceptanceTester;
        $name = sprintf(self::$productName, $name);
        do {
            try {
                $I->executeInSelenium(function(RemoteWebDriver $webDriver) use ($name) {
                    $webDriver->findElement(WebDriverBy::xpath($name));
                });
                break;
            } catch (WebDriverException $e) {
                try {
                    $I->click(self::$pageNextButton);
                    $I->waitForPageLoad();
                } catch (ElementNotFound $e) {
                    break;
                }
            }
        } while (true);
    }
}
