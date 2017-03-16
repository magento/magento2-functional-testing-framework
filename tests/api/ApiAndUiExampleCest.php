<?php
use Step\Api\Category;
use Step\Api\Product;
use Step\Api\Customer;
use \Codeception\Scenario;
use Step\Acceptance\Admin;
use Page\Acceptance\AdminCategoryPage;
use Page\Acceptance\AdminProductEditPage;

class ApiAndUiExampleCest
{
    protected $categoryId;
    protected $categoryName;
    protected $productId;
    protected $productName;
    protected $customerId;

    public function _before(Scenario $scenario)
    {
        $I = new Category($scenario);
        $categoryData = $this->getCategoryData();
        $this->categoryName = $categoryData['name'];
        $this->categoryId = $I->createCategory(['category' => $categoryData]);

        $I = new Product($scenario);
        $productData = $this->getSimpleProductData();
        $this->productName = $productData['name'];
        $this->productId = $I->createProduct(['product' => $productData]);

        $I = new Customer($scenario);
        $this->customerId = $I->createCustomer($this->getCustomerDataWithPassword());

        $I = new Admin($scenario);
        $I->goToTheAdminLoginPage();
        $I->loginAsAnExistingAdmin();
    }

    public function _after(Scenario $scenario)
    {
        $I = new Admin($scenario);
        $I->goToTheAdminLogoutPage();
    }

    /**
     * Test Category Visible in Backend.
     *
     * @param Scenario $scenario
     * @param AdminCategoryPage $adminCategoryPage
     * @return void
     */
    public function CheckCategoryInAdminTest(Scenario $scenario, AdminCategoryPage $adminCategoryPage)
    {
        $I = new Admin($scenario);
        $I->wantTo('verify category created visible in admin');
        $adminCategoryPage->amOnAdminCategoryPageById($I, $this->categoryId);
        $adminCategoryPage->seeCategoryNameInPageTitle($I, $this->categoryName);
    }

    /**
     * Test Product Visible in Backend.
     *
     * @param Scenario $scenario
     * @param AdminProductEditPage $adminProductEditPage
     * @return void
     */
    public function checkProductBackendTest(Scenario $scenario, AdminProductEditPage $adminProductEditPage)
    {
        $I = new Admin($scenario);
        $I->wantTo('verify product created visible in admin');
        $adminProductEditPage->amOnAdminProductPageById($I, $this->productId);
        $adminProductEditPage->seeProductNameInPageTitle($I, $this->productName);
    }

    /**
     * Get Category Data.
     *
     * @param array $categoryData
     * @return array
     */
    protected function getCategoryData($categoryData = [])
    {
        $sq = sq(1);
        return [
            'parent_id' => '2',
            'name' => isset($categoryData['name'])
                ? $categoryData['name'] : 'category'.$sq,
            'is_active' => '1',
            'include_in_menu' => '1',
            'available_sort_by' => ['position', 'name'],
            'custom_attributes' => [
                ['attribute_code' => 'url_key', 'value' => 'category'.$sq],
                ['attribute_code' => 'description', 'value' => 'Custom description'],
                ['attribute_code' => 'meta_title', 'value' => ''],
                ['attribute_code' => 'meta_keywords', 'value' => ''],
                ['attribute_code' => 'meta_description', 'value' => ''],
                ['attribute_code' => 'display_mode', 'value' => 'PRODUCTS'],
                ['attribute_code' => 'landing_page', 'value' => ''],
                ['attribute_code' => 'is_anchor', 'value' => '0'],
                ['attribute_code' => 'custom_use_parent_settings', 'value' => '0'],
                ['attribute_code' => 'custom_apply_to_products', 'value' => '0'],
                ['attribute_code' => 'custom_design', 'value' => ''],
                ['attribute_code' => 'custom_design_from', 'value' => ''],
                ['attribute_code' => 'custom_design_to', 'value' => ''],
                ['attribute_code' => 'page_layout', 'value' => ''],
            ]
        ];
    }

    /**
     * Get Simple Product Data.
     *
     * @param array $productData
     * @return array
     */
    protected function getSimpleProductData($productData = [])
    {
        $sq = sq(1);
        return [
            'sku' => isset($productData['sku'])
                ? $productData['sku'] : 'sku'.$sq,
            'name' => isset($productData['name'])
                ? $productData['name'] : 'sku'.$sq,
            'visibility' => 4,
            'type_id' => 'simple',
            'price' => 17.71,
            'status' => 1,
            'attribute_set_id' => 4,
            'custom_attributes' => [
                ['attribute_code' => 'url_key', 'value' => 'sku'.$sq],
                ['attribute_code' => 'cost', 'value' => ''],
                ['attribute_code' => 'description', 'value' => 'Description'],
                ['attribute_code' => 'category_ids', 'value' => $this->categoryId],
            ]
        ];
    }

    /**
     * Get Customer Data.
     *
     * @param array $additional
     * @return array
     */
    protected function getCustomerData(array $additional = [])
    {
        $sq = sq(1);
        $customerData = [
            'firstname' => 'firstname'.$sq,
            'lastname' => 'lastname'.$sq,
            'email' => 'email'.$sq.'@example.com',
            'gender' => rand(0, 1),
            'group_id' => 1,
            'middlename' => 'middlename'.$sq,
            'store_id' => 1,
            'website_id' => 1,
            'custom_attributes' => [
                [
                    'attribute_code' => 'disable_auto_group_change',
                    'value' => '0',
                ],
            ],
        ];
        return array_merge($customerData, $additional);
    }

    /**
     * Get Customer Data Including Password.
     *
     * @param string $password
     * @return array
     */
    protected function getCustomerDataWithPassword($password = '')
    {
       return ['customer' => $this->getCustomerData(), 'password' => ($password !== '') ? $password : '123123qW'];
    }
}
