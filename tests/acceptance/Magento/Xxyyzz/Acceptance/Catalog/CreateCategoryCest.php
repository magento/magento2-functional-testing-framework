<?php
namespace Magento\Xxyyzz\Acceptance\Catalog;

use Magento\Xxyyzz\Step\Backend\AdminStep;
use Magento\Xxyyzz\Page\Catalog\AdminCategoryPage;
use Magento\Xxyyzz\Page\Catalog\CategoryPage;
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
    public function createCategoryTest(AdminStep $I)
    {
        $I->wantTo('create sub category with required fields in admin Category page.');
        $category = $I->getCategoryApiData();
        AdminCategoryPage::of($I)->amOnAdminCategoryPage();
        AdminCategoryPage::of($I)->addSubCategory();
        AdminCategoryPage::of($I)->fillFieldCategoryName($category['name']);
        AdminCategoryPage::of($I)->fillFieldCategoryUrlKey($category['custom_attributes'][0]['value']);
        AdminCategoryPage::of($I)->saveCategory();
        $I->seeElement(AdminCategoryPage::$successMessage);

        $I->wantTo('verify created category in frontend category page.');
        CategoryPage::of($I)->amOnCategoryPage(str_replace('_', '-', $category['custom_attributes'][0]['value']));
        CategoryPage::of($I)->seeCategoryNameInTitleHeading($category['name']);
    }
}
