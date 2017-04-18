<?php
namespace Magento\Xxyyzz\Acceptance\Catalog;

use Magento\Xxyyzz\Step\Backend\AdminStep;
use Magento\Xxyyzz\Page\Catalog\Admin\AdminProductGridPage;
use Magento\Xxyyzz\Page\Catalog\Admin\AdminProductPage;
use Magento\Xxyyzz\Page\Catalog\CategoryPage;
use Magento\Xxyyzz\Page\Catalog\ProductPage;
use Magento\Xxyyzz\Step\Catalog\Api\CategoryApiStep;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Model\SeverityLevel;

/**
 * Class CreateSimpleProductCest
 * @Stories({"Create simple product"})
 * @Features({"Create simple product"})
 * @Title("Create simple product with required fields")
 * @Description("Create simple product with required fields")
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

    /**
     * @param AdminStep $I
     */
    public function _before(AdminStep $I, CategoryApiStep $api)
    {
        $I->loginAsAdmin();
        $this->category = $I->getCategoryData();
        $api->amAdminTokenAuthenticated();
        $this->category = array_merge($this->category, ['id' => $api->createCategory(['category' => $this->category])]);
        $this->category['url_key'] = $this->category['custom_attributes'][0]['value'];
        $this->product = $I->getSimpleProductData();
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
     * Create simple product in admin.
     *
     * Allure annotations
     * @Description("Method Description: Create simple product with required fields")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "Admin", value = "$I")
     *
     * Codeception annotations
     * @group catalog
     * @env chrome
     * @env firefox
     * @env phantomjs
     *
     * @param AdminStep $I
     * @return void
     */
    public function createSimpleProductTest(AdminStep $I)
    {
        $I->wantTo('create simple product with required fields in admin product page.');
        AdminProductGridPage::of($I)->amOnAdminProductGridPage();
        AdminProductGridPage::of($I)->clickAddNewProductPage();
        AdminProductPage::of($I)->amOnAdminNewProductPage();
        AdminProductPage::of($I)->fillFieldProductName($this->product['name']);
        AdminProductPage::of($I)->fillFieldProductSku($this->product['sku']);
        AdminProductPage::of($I)->fillFieldProductPrice($this->product['price']);
        if (isset($this->product['qty'])) {
            AdminProductPage::of($I)->fillFieldProductQuantity($this->product['qty']);
        }
        AdminProductPage::of($I)->selectProductStockStatus($this->product['stock_status']);
        AdminProductPage::of($I)->selectProductCategories([$this->category['name']]);
        AdminProductPage::of($I)->fillFieldProductUrlKey($this->product['url_key']);

        $I->wantTo('see simple product successfully saved message.');
        AdminProductPage::of($I)->saveProduct();
        $I->seeElement(AdminProductPage::$successMessage);

        $I->wantTo('verify simple product data in admin product page.');
        AdminProductPage::of($I)->seeProductAttributeSet('Default');
        AdminProductPage::of($I)->seeProductName($this->product['name']);
        AdminProductPage::of($I)->seeProductSku($this->product['sku']);
        AdminProductPage::of($I)->seeProductPrice($this->product['price']);
        if (isset($this->product['qty'])) {
            AdminProductPage::of($I)->seeProductQuantity($this->product['qty']);
        }
        AdminProductPage::of($I)->seeProductStockStatus($this->product['stock_status']);
        AdminProductPage::of($I)->seeProductCategories([$this->category['name']]);
        AdminProductPage::of($I)->seeProductUrlKey(str_replace('_', '-', $this->product['url_key']));

        $I->wantTo('verify simple product data in frontend category page.');
        CategoryPage::of($I)->amOnCategoryPage($this->category['url_key']);
        CategoryPage::of($I)->seeProductLinksInPage(
            $this->product['name'],
            str_replace('_', '-', $this->product['url_key'])
        );
        CategoryPage::of($I)->seeProductNameInPage($this->product['name']);
        CategoryPage::of($I)->seeProductPriceInPage($this->product['name'], $this->product['price']);

        $I->wantTo('verify simple product data in frontend product page.');
        ProductPage::of($I)->amOnProductPage(str_replace('_', '-', $this->product['url_key']));
        ProductPage::of($I)->seeProductNameInPage($this->product['name']);
        ProductPage::of($I)->seeProductPriceInPage($this->product['price']);
        ProductPage::of($I)->seeProductStockStatusInPage($this->product['stock_status']);
        ProductPage::of($I)->seeProductSkuInPage($this->product['sku']);
    }
}
