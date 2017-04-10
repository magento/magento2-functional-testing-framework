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
        $I->goToTheAdminLoginPage();
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
    public function createSimpleProductTest(
        AdminStep $I,
        AdminProductGridPage $adminProductGridPage,
        AdminProductPage $adminProductPage
    ) {
        $I->wantTo('verify simple product creation in admin');
        $product = $I->getSimpleProductData();
        $adminProductGridPage->amOnAdminProductGridPage($I);
        $adminProductGridPage->goToAddNewProductPage($I);
        $adminProductPage->amOnAdminNewProductPage($I);

        $adminProductPage->fillFieldProductName($I, $product['name']);
        $adminProductPage->fillFieldProductSku($I, $product['sku']);
        $adminProductPage->fillFieldProductPrice($I, $product['price']);
        $adminProductPage->fillFieldProductQuantity($I, $product['extension_attributes']['stock_item']['qty']);
        $adminProductPage->selectProductStockStatus(
            $I,
            $product['extension_attributes']['stock_item']['is_in_stock'] !== 0 ? 'In Stock' : 'Out of Stock'
        );
        $adminProductPage->saveProduct($I);
        $I->seeElement(AdminProductPage::$productSaveSuccessMessage);
    }
}
