<?php
namespace Magento\Xxyyzz\Acceptance\Catalog;

use Magento\Xxyyzz\Step\Backend\Admin;
use Magento\Xxyyzz\Page\Catalog\Admin\AdminProductGridPage;
use Magento\Xxyyzz\Page\Catalog\Admin\AdminProductPage;
use Magento\Xxyyzz\Helper\DataHelper;
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
    public function _before(Admin $I)
    {
        $I->goToTheAdminLoginPage();
        $I->loginAsAdmin();
    }

    public function _after(Admin $I)
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
     * @param Admin $I
     * @param AdminProductGridPage $adminProductGridPage
     * @param AdminProductPage $adminProductPage
     * @param DataHelper $dataHelper
     * @return void
     */
    public function createSimpleProductTest(
        Admin $I,
        AdminProductGridPage $adminProductGridPage,
        AdminProductPage $adminProductPage,
        DataHelper $dataHelper
    ) {
        $I->wantTo('verify simple product creation in admin');
        $product = $dataHelper->getSimpleProductData();
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
