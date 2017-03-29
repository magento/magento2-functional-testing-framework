<?php
namespace Magento\Xxyyzz\Catalog;

use Codeception\Scenario;
use Magento\Xxyyzz\Helper\DataHelper;
use Magento\Xxyyzz\Step\Backend\Admin;
use Magento\Xxyyzz\Step\Catalog\Api\Product;
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
     * @var \Magento\Xxyyzz\Step\Backend\Admin
     */
    protected $admin;

    /**
     * @var array
     */
    protected $product;

    /**
     * @param Scenario $scenario
     * @param DataHelper $dataHelper
     */

    public function _before(Scenario $scenario, DataHelper $dataHelper)
    {
        $this->admin = new Admin($scenario);
        $I = $this->admin;
        $I->goToTheAdminLoginPage();
        $I->loginAsAdmin();
        $this->product = $dataHelper->getSimpleProductData();
        $apiHandler = new Product($scenario);
        $this->product = array_merge($this->product, ['id' => $apiHandler->createProduct(['product' => $this->product])]);
    }

    public function _after()
    {
        $I = $this->admin;
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
     * @param Admin $I
     * @param AdminProductGridPage $adminProductGridPage
     * @param AdminProductPage $adminProductPage
     * @return void
     */
    public function updateSimpleProductTest(
        Admin $I,
        AdminProductGridPage $adminProductGridPage,
        AdminProductPage $adminProductPage
    ) {
        $I->wantTo('verify product updated in admin');
        $adminProductGridPage->amOnAdminProductGridPage($I);
        //$adminProductGridPage->searchBySku($I, $this->product['sku']);
        $I->wantTo('verify product created visible in product form in admin');
        $adminProductPage->amOnAdminProductPageById($I, $this->product['id']);
        $adminProductPage->seeProductNameInPageTitle($I, $this->product['name']);
        $adminProductPage->seeProductAttributeSet($I, 'Default');
        $adminProductPage->seeProductName($I, $this->product['name']);
        $adminProductPage->seeProductSku($I, $this->product['sku']);
        $adminProductPage->seeProductPrice($I, $this->product['price']);
        $adminProductPage->seeProductQuantity($I, $this->product['extension_attributes']['stock_item']['qty']);
        $adminProductPage->seeProductStockStatus(
            $I,
            $this->product['extension_attributes']['stock_item']['is_in_stock'] !== 0 ? 'In Stock' : 'Out of Stock'
        );
        $I->wantTo('verify product updated in admin');
        $adminProductPage->fillFieldProductName($I, $this->product['name'] . '-updated');
        $adminProductPage->fillFieldProductSku($I, $this->product['sku'] . '-updated');
        $adminProductPage->fillFieldProductPrice($I, $this->product['price']+10);
        $adminProductPage->fillFieldProductQuantity($I, $this->product['extension_attributes']['stock_item']['qty']+100);
        $adminProductPage->saveProduct($I);
        $I->wantTo('verify product created visible in product form in admin');
        $adminProductPage->amOnAdminProductPageById($I, $this->product['id']);
        $adminProductPage->seeProductNameInPageTitle($I, $this->product['name']);
        $adminProductPage->seeProductAttributeSet($I, 'Default');
        $adminProductPage->seeProductName($I, $this->product['name']);
        $adminProductPage->seeProductSku($I, $this->product['sku']);
        $adminProductPage->seeProductPrice($I, $this->product['price']);
        $adminProductPage->seeProductQuantity($I, $this->product['extension_attributes']['stock_item']['qty']);
        $adminProductPage->seeProductStockStatus(
            $I,
            $this->product['extension_attributes']['stock_item']['is_in_stock'] !== 0 ? 'In Stock' : 'Out of Stock'
        );
    }
}
