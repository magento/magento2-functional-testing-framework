<?php
namespace Magento\Xxyyzz\Acceptance\Catalog;

use Magento\Xxyyzz\Step\Backend\AdminStep;
use Magento\Xxyyzz\Page\Catalog\AdminCategoryPage;
use Magento\Xxyyzz\Page\Catalog\StorefrontCategoryPage;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Model\SeverityLevel;
use Yandex\Allure\Adapter\Annotation\TestCaseId;

/**
 * Class CreateSubCategoryCest
 *
 * Allure annotations
 * @Features({"Category"})
 * @Stories({"Create a sub-Category"})
 *
 * Codeception annotations
 * @group catalog
 * @group add
 * @env chrome
 * @env firefox
 * @env phantomjs
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
     * Allure annotations
     * @Title("Create sub category with required fields")
     * @Description("Create sub category with required fields")
     * @TestCaseId("")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "Admin", value = "$I")
     * @Parameter(name = "AdminCategoryPage", value = "$adminCategoryPage")
     * @Parameter(name = "StorefrontCategoryPage", value = "$storefrontCategoryPage")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param AdminCategoryPage $adminCategoryPage
     * @param StorefrontCategoryPage $storefrontCategoryPage
     * @return void
     */
    public function createCategoryTest(
        AdminStep $I,
        AdminCategoryPage $adminCategoryPage,
        StorefrontCategoryPage $storefrontCategoryPage
    ) {
        $I->wantTo('create sub category with required fields in admin Category page.');
        $category = $I->getCategoryApiData();

        $I->goToTheAdminProductsCategoriesPage();
        $adminCategoryPage->addSubCategory();
        $adminCategoryPage->fillFieldCategoryName($category['name']);

        $adminCategoryPage->clickOnSearchEngineOptimization();
        $adminCategoryPage->fillFieldCategoryUrlKey($category['custom_attributes'][0]['value']);
        $adminCategoryPage->saveCategory();
        $adminCategoryPage->seeSuccessMessage();

        $I->wantTo('verify created category in frontend category page.');
        $storefrontCategoryPage->amOnCategoryPage(str_replace('_', '-', $category['custom_attributes'][0]['value']));
        $storefrontCategoryPage->seeCategoryNameInTitleHeading($category['name']);
    }
}
