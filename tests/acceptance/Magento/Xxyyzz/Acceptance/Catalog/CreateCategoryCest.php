<?php
namespace Magento\Xxyyzz\Acceptance\Catalog;

use Magento\Xxyyzz\Step\Backend\AdminStep;
use Magento\Xxyyzz\Page\Catalog\Admin\AdminCategoryPage;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Model\SeverityLevel;

/**
 * Class CreateSubCategoryCest
 *
 * @Stories({"Create sub category"})
 * @Features({"Create sub category"})
 * @Title("Create sub category with required fields")
 * @Description("Create sub category with required fields")
 */
class CreateCategoryCest
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
     * Create sub category in admin.
     *
     * Allure annotations
     * @Description("Method Description: Create sub category with required fields")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "Admin", value = "$I")
     * @Parameter(name = "AdminCategoryPage", value = "$adminCategoryPage")
     * @Parameter(name = "DataHelper", value = "$dataHelper")
     *
     * Codeception annotations
     * @group catalog
     * @env chrome
     * @env firefox
     * @env phantomjs
     *
     * @param AdminStep $I
     * @param AdminCategoryPage $adminCategoryPage
     * @return void
     */
    public function createCategoryTest(
        AdminStep $I,
        AdminCategoryPage $adminCategoryPage
    ) {
        $I->wantTo('verify category creation in admin');
        $category = $I->getCategoryData();
        $adminCategoryPage->amOnAdminCategoryPage($I);
        $adminCategoryPage->addSubCategory($I);
        $adminCategoryPage->fillFieldCategoryName($I, $category['name']);
        $adminCategoryPage->fillFieldCategoryUrlKey($I, $category['custom_attributes'][0]['value']);
        $adminCategoryPage->saveCategory($I);
        $I->seeElement(AdminCategoryPage::$catagorySaveSuccessMessage);
    }
}
