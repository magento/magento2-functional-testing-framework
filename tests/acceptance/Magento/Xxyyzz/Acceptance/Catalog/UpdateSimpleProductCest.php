<?php
namespace Magento\Xxyyzz\Acceptance\Catalog;

use Magento\Xxyyzz\Step\Backend\AdminStep;
use Magento\Xxyyzz\Step\Catalog\Api\CategoryApiStep;
use Magento\Xxyyzz\Step\Catalog\Api\ProductApiStep;
use Magento\Xxyyzz\Page\Catalog\AdminProductGridPage;
use Magento\Xxyyzz\Page\Catalog\AdminProductPage;
use Magento\Xxyyzz\Page\Catalog\StorefrontCategoryPage;
use Magento\Xxyyzz\Page\Catalog\StorefrontProductPage;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Model\SeverityLevel;
use Yandex\Allure\Adapter\Annotation\TestCaseId;

/**
 * Class UpdateSimpleProductCest
 *
 * Allure annotations
 * @Features({"Catalog"})
 * @Stories({"Update simple product"})
 *
 * Codeception Annotations
 * @group catalog
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class UpdateSimpleProductCest
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
     * @param CategoryApiStep $categoryApi
     * @param ProductApiStep $productApi
     */
    public function _before(AdminStep $I, CategoryApiStep $categoryApi, ProductApiStep $productApi)
    {
        $I->loginAsAdmin();
        $this->category = $I->getCategoryApiData();
        $categoryApi->amAdminTokenAuthenticated();
        $this->category = array_merge(
            $this->category,
            ['id' => $categoryApi->createCategory(['category' => $this->category])]
        );
        $this->category['url_key'] = $this->category['custom_attributes'][0]['value'];
        $this->product = $I->getSimpleProductApiData();
        $this->product['custom_attributes'][2]['value'] = $this->category['id'];
        $productApi->amAdminTokenAuthenticated();
        $this->product = array_merge(
            $this->product,
            ['id' => $productApi->createProduct(['product' => $this->product])]
        );
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
     * Update simple product in admin.
     *
     * Allure annotations
     * @Title("Update simple product with required fields")
     * @Description("Update simple product with required fields")
     * @TestCaseId("")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "AdminStep", value = "$I")
     *
     * @param AdminStep $I
     * @return void
     */
    public function updateSimpleProductTest(AdminStep $I)
    {
        $I->wantTo('update simple product in admin.');
        AdminProductGridPage::of($I)->amOnAdminProductGridPage();
        AdminProductGridPage::of($I)->searchBySku($this->product['sku']);

        $I->wantTo('open product created from precondition.');
        AdminProductPage::of($I)->amOnAdminEditProductPageById($this->product['id']);

        $I->wantTo('update product data fields.');
        AdminProductPage::of($I)->fillFieldProductName($this->product['name'] . '-updated');
        AdminProductPage::of($I)->fillFieldProductSku($this->product['sku'] . '-updated');
        AdminProductPage::of($I)->fillFieldProductPrice($this->product['price']+10);
        AdminProductPage::of($I)->fillFieldProductQuantity(
            $this->product['extension_attributes']['stock_item']['qty']+100
        );
        $I->wantTo('save product data change.');
        AdminProductPage::of($I)->saveProduct();
        $I->seeElement(AdminProductPage::$successMessage);

        $I->wantTo('see updated product data.');
        AdminProductPage::of($I)->amOnAdminEditProductPageById($this->product['id']);
        AdminProductPage::of($I)->seeInPageTitle($this->product['name'] . '-updated');
        AdminProductPage::of($I)->seeProductAttributeSet('Default');
        AdminProductPage::of($I)->seeProductName($this->product['name'] . '-updated');
        AdminProductPage::of($I)->seeProductSku($this->product['sku'] . '-updated');
        AdminProductPage::of($I)->seeProductPrice($this->product['price']+10);
        AdminProductPage::of($I)->seeProductQuantity($this->product['extension_attributes']['stock_item']['qty']+100);
        AdminProductPage::of($I)->seeProductStockStatus(
            $this->product['extension_attributes']['stock_item']['is_in_stock'] !== 0 ? 'In Stock' : 'Out of Stock'
        );

        $I->wantTo('verify simple product data in frontend category page.');
        StorefrontCategoryPage::of($I)->amOnCategoryPage($this->category['url_key']);
        StorefrontCategoryPage::of($I)->seeProductNameInPage($this->product['name'] . '-updated');
        StorefrontCategoryPage::of($I)->seeProductPriceInPage($this->product['name'] . '-updated', $this->product['price'] + 10);

        $I->wantTo('verify simple product data in frontend product page.');
        StorefrontProductPage::of($I)->amOnProductPage(str_replace('_', '-', $this->product['url_key']));
        StorefrontProductPage::of($I)->seeProductNameInPage($this->product['name'] . '-updated');
        StorefrontProductPage::of($I)->seeProductPriceInPage($this->product['price'] + 10);
        StorefrontProductPage::of($I)->seeProductSkuInPage($this->product['sku'] . '-updated');
    }
}
