<?php
namespace Magento\Xxyyzz\Module;

use Codeception\Module\Sequence;

class MagentoDataSequence extends Sequence
{
    /**
     * Get category data.
     *
     * @param array $categoryData
     * @return array
     */
    public function getCategoryData($categoryData = [])
    {
        $sq = $this->getSqs();
        return [
            'parent_id' => '2',
            'name' => isset($categoryData['name'])
                ? $categoryData['name'] : 'category'.$sq,
            'is_active' => '1',
            'include_in_menu' => '1',
            'available_sort_by' => ['position', 'name'],
            'custom_attributes' => [
                ['attribute_code' => 'url_key', 'value' => isset($categoryData['name'])
                    ? $categoryData['name'] : 'category'.$sq],
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
     * Get simple product data.
     *
     * @param integer $categoryId
     * @param array $productData
     * @return array
     */
    public function getSimpleProductData($categoryId = 0, $productData = [])
    {
        $sq = $this->getSqs();
        return [
            'sku' => isset($productData['sku'])
                ? $productData['sku'] : 'simple_product_sku'.$sq,
            'name' => isset($productData['name'])
                ? $productData['name'] : 'simple_product'.$sq,
            'visibility' => 4,
            'type_id' => 'simple',
            'price' => 17.71,
            'status' => 1,
            'attribute_set_id' => 4,
            'extension_attributes' => [
                'stock_item' => ['is_in_stock' => 1, 'qty' => 1000]
            ],
            'custom_attributes' => [
                ['attribute_code' => 'url_key', 'value' => isset($productData['sku'])
                    ? $productData['sku'] : 'sku'.$sq],
                ['attribute_code' => 'tax_class_id', 'value' => 2],
                ['attribute_code' => 'category_ids', 'value' => $categoryId],
            ],
        ];
    }

    /**
     * Get Customer Data.
     *
     * @param array $additional
     * @return array
     */
    public function getCustomerData(array $additional = [])
    {
        $sq = $this->getSqs();
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
     * Get customer data including password.
     *
     * @param string $password
     * @return array
     */
    public function getCustomerDataWithPassword($password = '')
    {
        return ['customer' => self::getCustomerData(), 'password' => ($password !== '') ? $password : '123123qW'];
    }

    /**
     * Get a unique sequence across suite.
     *
     * @return string
     */
    public function getSqs()
    {
        return sqs();
    }
}