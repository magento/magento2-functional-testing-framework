<?php
namespace Magento\Xxyyzz\Acceptance\Catalog;

use Magento\Xxyyzz\Step\Backend\AdminStep;
use Magento\Xxyyzz\Page\Catalog\AdminProductGridPage;
use Magento\Xxyyzz\Page\Catalog\AdminProductPage;
use Magento\Xxyyzz\Page\Catalog\StorefrontCategoryPage;
use Magento\Xxyyzz\Page\Catalog\StorefrontProductPage;
use Magento\Xxyyzz\Step\Catalog\Api\CategoryApiStep;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Model\SeverityLevel;
use Yandex\Allure\Adapter\Annotation\TestCaseId;

/**
 * Class CreateSimpleProductCest
 *
 * Allure annotations
 * @Features({"Catalog"})
 * @Stories({"Create simple product"})
 *
 * Codeception annotations
 * @group catalog
 * @group add
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class CreateSimpleProductCest
{
    /**
     * @var array
     */
    protected $category;

    /**
     * @var array
     */
    protected $product;

    public function _before(AdminStep $I, CategoryApiStep $api)
    {
        $I->loginAsAdmin();
        $I->goToTheAdminProductsCatalogPage();
        
        $this->category = $I->getCategoryApiData();
        $api->amAdminTokenAuthenticated();
        $this->category = array_merge($this->category, ['id' => $api->createCategory(['category' => $this->category])]);
        $this->category['url_key'] = $this->category['custom_attributes'][0]['value'];
        $this->product = $I->getProductApiData();
        if ($this->product['extension_attributes']['stock_item']['is_in_stock'] !== 0) {
            $this->product['stock_status'] = 'In Stock';
            $this->product['qty'] = $this->product['extension_attributes']['stock_item']['qty'];
        } else {
            $this->product['stock_status'] = 'Out of Stock';
        }
        $this->product['url_key'] = $this->product['custom_attributes'][0]['value'];
    }

    public function _after(AdminStep $I)
    {
        $I->goToTheAdminLogoutPage();
    }

    /**
     * Allure annotations
     * @Title("Create a simple product and verify on the Storefront")
     * @Description("Create a simple product in the Admin and verify the content on the Storefront.")
     * @TestCaseId("")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminProductGridPage", value = "$adminProductGridPage")
     * @Parameter(name = "AdminProductPage", value = "$adminProductPage")
     * @Parameter(name = "StorefrontCategoryPage", value = "$storefrontCategoryPage")
     * @Parameter(name = "StorefrontProductPage", value = "$storefrontProductPage")
     *
     * @param AdminStep $I
     * @param AdminProductGridPage $adminProductGridPage
     * @param AdminProductPage $adminProductPage
     * @param StorefrontCategoryPage $storefrontCategoryPage
     * @param StorefrontProductPage $storefrontProductPage
     * @return void
     */
    public function createSimpleProductTest(
        AdminStep $I,
        AdminProductGridPage $adminProductGridPage,
        AdminProductPage $adminProductPage,
        StorefrontCategoryPage $storefrontCategoryPage,
        StorefrontProductPage $storefrontProductPage
    ) {
        $I->wantTo('create simple product with required fields in admin product page.');
        $adminProductGridPage->clickAddNewProductButton();
        $adminProductPage->amOnAdminNewProductPage();
        $adminProductPage->fillFieldProductName($this->product['name']);
        $adminProductPage->fillFieldProductSku($this->product['sku']);
        $adminProductPage->fillFieldProductPrice($this->product['price']);
        if (isset($this->product['qty'])) {
            $adminProductPage->fillFieldProductQuantity($this->product['qty']);
        }
        $adminProductPage->selectProductStockStatus($this->product['stock_status']);
        $adminProductPage->selectProductCategories([$this->category['name']]);
        $adminProductPage->fillFieldProductUrlKey($this->product['url_key']);

        $I->wantTo('see simple product successfully saved message.');
        $adminProductPage->saveProduct();
        $adminProductPage->seeSuccessMessage();

        $I->wantTo('verify simple product data in admin product page.');
        $adminProductPage->seeProductAttributeSet('Default');
        $adminProductPage->seeProductName($this->product['name']);
        $adminProductPage->seeProductSku($this->product['sku']);
        $adminProductPage->seeProductPrice($this->product['price']);
        if (isset($this->product['qty'])) {
            $adminProductPage->seeProductQuantity($this->product['qty']);
        }
        $adminProductPage->seeProductStockStatus($this->product['stock_status']);
        $adminProductPage->seeProductCategories([$this->category['name']]);
        $adminProductPage->seeProductUrlKey(str_replace('_', '-', $this->product['url_key']));

        $I->wantTo('verify simple product data in frontend category page.');
        $storefrontCategoryPage->amOnCategoryPage($this->category['url_key']);
        $storefrontCategoryPage->seeProductLinksInPage(
            $this->product['name'],
            str_replace('_', '-', $this->product['url_key'])
        );
        $storefrontCategoryPage->seeProductNameInPage($this->product['name']);
        $storefrontCategoryPage->seeProductPriceInPage($this->product['name'], $this->product['price']);

        $I->wantTo('verify simple product data in frontend product page.');
        $storefrontProductPage->amOnProductPage(str_replace('_', '-', $this->product['url_key']));
        $storefrontProductPage->seeProductNameInPage($this->product['name']);
        $storefrontProductPage->seeProductPriceInPage($this->product['price']);
        $storefrontProductPage->seeProductStockStatusInPage($this->product['stock_status']);
        $storefrontProductPage->seeProductSkuInPage($this->product['sku']);
    }
}
