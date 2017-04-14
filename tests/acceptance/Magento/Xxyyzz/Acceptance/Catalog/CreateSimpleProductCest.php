<?php
namespace Magento\Xxyyzz\Acceptance\Catalog;

use Magento\Xxyyzz\Step\Backend\AdminStep;
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
 * Class CreateSimpleProductCest
 * @Stories({"Create simple product"})
 * @Features({"Create simple product"})
 * @Title("Create simple product with required fields")
 * @Description("Create simple product with required fields")
 */
class CreateSimpleProductCest
{
    public function _before(AdminStep $I)
    {
        $I->loginAsAdmin();
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
    public function createSimpleProductTest(
        AdminStep $I
    ) {
        $I->wantTo('create simple product with required fields in admin product page.');
        $product = $I->getSimpleProductData();
        AdminProductGridPage::of($I)->amOnAdminProductGridPage();
        AdminProductGridPage::of($I)->goToAddNewProductPage();
        AdminProductPage::of($I)->amOnAdminNewProductPage();

        AdminProductPage::of($I)->fillFieldProductName($product['name']);
        AdminProductPage::of($I)->fillFieldProductSku($product['sku']);
        AdminProductPage::of($I)->fillFieldProductPrice($product['price']);
        AdminProductPage::of($I)->fillFieldProductQuantity($product['extension_attributes']['stock_item']['qty']);
        AdminProductPage::of($I)->selectProductStockStatus(
            $product['extension_attributes']['stock_item']['is_in_stock'] !== 0 ? 'In Stock' : 'Out of Stock'
        );
        AdminProductPage::of($I)->saveProduct();
        $I->seeElement(AdminProductPage::$successMessage);
    }
}
