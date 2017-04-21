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
 * @Stories({"Create a basic Product"})
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
        $this->category = $I->getCategoryApiData();
        $api->amAdminTokenAuthenticated();
        $this->category = array_merge($this->category, ['id' => $api->createCategory(['category' => $this->category])]);
        $this->category['url_key'] = $this->category['custom_attributes'][0]['value'];
        $this->product = $I->getSimpleProductApiData();
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
     * @Title("Create a basic Product and verify on the Storefront")
     * @Description("Create a basic Product in the Admin and verify the content on the Storefront.")
     * @TestCaseId("")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "Admin", value = "$I")
     *
     * Codeception annotations
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
        StorefrontCategoryPage::of($I)->amOnCategoryPage($this->category['url_key']);
        StorefrontCategoryPage::of($I)->seeProductLinksInPage(
            $this->product['name'],
            str_replace('_', '-', $this->product['url_key'])
        );
        StorefrontCategoryPage::of($I)->seeProductNameInPage($this->product['name']);
        StorefrontCategoryPage::of($I)->seeProductPriceInPage($this->product['name'], $this->product['price']);

        $I->wantTo('verify simple product data in frontend product page.');
        StorefrontProductPage::of($I)->amOnProductPage(str_replace('_', '-', $this->product['url_key']));
        StorefrontProductPage::of($I)->seeProductNameInPage($this->product['name']);
        StorefrontProductPage::of($I)->seeProductPriceInPage($this->product['price']);
        StorefrontProductPage::of($I)->seeProductStockStatusInPage($this->product['stock_status']);
        StorefrontProductPage::of($I)->seeProductSkuInPage($this->product['sku']);
    }
}
