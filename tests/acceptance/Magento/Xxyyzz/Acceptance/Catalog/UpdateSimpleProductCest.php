<?php
namespace Magento\Xxyyzz\Acceptance\Catalog;

use Codeception\Scenario;
use Magento\Xxyyzz\Step\Backend\AdminStep;
use Magento\Xxyyzz\Step\Catalog\Api\ProductApiStep;
use Magento\Xxyyzz\Page\Catalog\Admin\AdminProductGridPage;
use Magento\Xxyyzz\Page\Catalog\Admin\AdminProductPage;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Model\SeverityLevel;

/**
 * Class UpdateSimpleProductCest
 *
 * @Stories({"Update simple product"})
 * @Features({"Update simple product"})
 * @Title("Update simple product with required fields")
 * @Description("Update simple product with required fields")
 */
class UpdateSimpleProductCest
{
    /**
     * @var \Magento\Xxyyzz\Step\Backend\AdminStep
     */
    protected $admin;

    /**
     * @var array
     */
    protected $product;

    /**
     * @param AdminStep $I
     */

    public function _before(AdminStep $I, ProductApiStep $api)
    {
        $I->goToTheAdminLoginPage();
        $I->loginAsAdmin();
        $this->product = $I->getSimpleProductData();
        $api->amAdminTokenAuthenticated();
        $this->product = array_merge($this->product, ['id' => $api->createProduct(['product' => $this->product])]);
    }

    public function _after(AdminStep $I)
    {
        $I->goToTheAdminLogoutPage();
    }

    /**
     * Update simple product in admin.
     *
     * Allure annotations
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "Admin", value = "$I")
     * @Parameter(name = "AdminProductGridPage", value = "$adminProductGridPage")
     * @Parameter(name = "AdminProductPage", value = "$adminProductPage")
     *
     * Codeception annotations
     * @group catalog
     * @env chrome
     * @env firefox
     * @env phantomjs
     *
     * @param AdminStep $I
     * @param AdminProductGridPage $adminProductGridPage
     * @param AdminProductPage $adminProductPage
     * @return void
     */
    public function updateSimpleProductTest(
        AdminStep $I,
        AdminProductGridPage $adminProductGridPage,
        AdminProductPage $adminProductPage
    ) {
        $I->wantTo('update simple product in admin');
        AdminProductGridPage::of($I)->amOnAdminProductGridPage();
        //$adminProductGridPage->searchBySku($I, $this->product['sku']);
        $I->wantTo('verify product created visible in product form in admin');
        AdminProductPage::of($I)->amOnAdminEditProductPageById($this->product['id']);
        AdminProductPage::of($I)->seeInPageTitle($this->product['name']);
        AdminProductPage::of($I)->seeProductAttributeSet('Default');
        AdminProductPage::of($I)->seeProductName($this->product['name']);
        AdminProductPage::of($I)->seeProductSku($this->product['sku']);
        AdminProductPage::of($I)->seeProductPrice($this->product['price']);
        AdminProductPage::of($I)->seeProductQuantity($this->product['extension_attributes']['stock_item']['qty']);
        AdminProductPage::of($I)->seeProductStockStatus(
            $this->product['extension_attributes']['stock_item']['is_in_stock'] !== 0 ? 'In Stock' : 'Out of Stock'
        );
        $I->wantTo('verify product updated in admin');
        AdminProductPage::of($I)->fillFieldProductName($this->product['name'] . '-updated');
        AdminProductPage::of($I)->fillFieldProductSku($this->product['sku'] . '-updated');
        AdminProductPage::of($I)->fillFieldProductPrice($this->product['price']+10);
        AdminProductPage::of($I)->fillFieldProductQuantity(
            $this->product['extension_attributes']['stock_item']['qty']+100
        );
        AdminProductPage::of($I)->saveProduct();
        $I->wantTo('verify product created visible in product form in admin');
        AdminProductPage::of($I)->amOnAdminEditProductPageById($this->product['id']);
        AdminProductPage::of($I)->seeInPageTitle($this->product['name']. '-updated');
        AdminProductPage::of($I)->seeProductAttributeSet('Default');
        AdminProductPage::of($I)->seeProductName($this->product['name']. '-updated');
        AdminProductPage::of($I)->seeProductSku($this->product['sku']. '-updated');
        AdminProductPage::of($I)->seeProductPrice($this->product['price']+10);
        AdminProductPage::of($I)->seeProductQuantity($this->product['extension_attributes']['stock_item']['qty']+100);
        AdminProductPage::of($I)->seeProductStockStatus(
            $this->product['extension_attributes']['stock_item']['is_in_stock'] !== 0 ? 'In Stock' : 'Out of Stock'
        );
    }
}
