<?php
namespace Magento\Xxyyzz\Module;

use Codeception\Module\Sequence;

class MagentoFakerData extends Sequence
{
    /**
     * Get category api data.
     *
     * @param array $categoryData
     * @return array
     */
    public function getCategoryApiData($categoryData = [])
    {
        $faker = \Faker\Factory::create();
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
                ['attribute_code' => 'description', 'value' => $faker->text(20)],
                ['attribute_code' => 'meta_title', 'value' => $faker->text(20)],
                ['attribute_code' => 'meta_keywords', 'value' => $faker->text(20)],
                ['attribute_code' => 'meta_description', 'value' => $faker->text(20)],
                ['attribute_code' => 'display_mode', 'value' => 'PRODUCTS'],
                ['attribute_code' => 'landing_page', 'value' => ''],
                ['attribute_code' => 'is_anchor', 'value' => '0'],
                ['attribute_code' => 'custom_use_parent_settings', 'value' => '0'],
                ['attribute_code' => 'custom_apply_to_products', 'value' => '0'],
                ['attribute_code' => 'custom_design', 'value' => ''],
                ['attribute_code' => 'custom_design_from', 'value' => $faker->date($format = 'm/d/Y', $max = 'now')],
                ['attribute_code' => 'custom_design_to', 'value' => $faker->date($format = 'm/d/Y')],
                ['attribute_code' => 'page_layout', 'value' => ''],
            ]
        ];
    }

    /**
     * Get simple product api data.
     *
     * @param integer $categoryId
     * @param array $productData
     * @return array
     */
    public function getSimpleProductApiData($categoryId = 0, $productData = [])
    {
        $faker = \Faker\Factory::create();
        $sq = $this->getSqs();
        return [
            'sku' => isset($productData['sku'])
                ? $productData['sku'] : 'simple_product_sku'.$sq,
            'name' => isset($productData['name'])
                ? $productData['name'] : 'simple_product'.$sq,
            'visibility' => 4,
            'type_id' => 'simple',
            'price' => $faker->randomFloat(2, 1),
            'status' => 1,
            'attribute_set_id' => 4,
            'extension_attributes' => [
                'stock_item' => ['is_in_stock' => 1, 'qty' => $faker->numberBetween(100, 9000)]
            ],
            'custom_attributes' => [
                ['attribute_code' => 'url_key', 'value' => isset($productData['name'])
                    ? $productData['name'] : 'simple_product'.$sq,],
                ['attribute_code' => 'tax_class_id', 'value' => 2],
                ['attribute_code' => 'category_ids', 'value' => $categoryId],
            ],
        ];
    }

    /**
     * Get Customer Api data.
     *
     * @param array $additional
     * @return array
     */
    public function getCustomerApiData(array $additional = [])
    {
        $faker = \Faker\Factory::create();
        $customerData = [
            'firstname' => $faker->firstName,
            'middlename' => $faker->firstName,
            'lastname' => $faker->lastName,
            'email' => $faker->email,
            'gender' => rand(0, 1),
            'group_id' => 1,
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
    public function getCustomerApiDataWithPassword($password = '')
    {
        return ['customer' => self::getCustomerApiData(), 'password' => ($password !== '') ? $password : '123123qW'];
    }

    /**
     * Get Customer Api data.
     *
     * @param array $additional
     * @return array
     */
    public function getCustomerData(array $additional = [])
    {
        $faker = \Faker\Factory::create();
        $customerData = [
            'prefix' => $faker->title,
            'firstname' => $faker->firstName,
            'middlename' => $faker->firstName,
            'lastname' => $faker->lastName,
            'suffix' => \Faker\Provider\en_US\Person::suffix(),
            'email' => $faker->email,
            'dateOfBirth' => $faker->date($format = 'm/d/Y', $max = 'now'),
            'gender' => rand(0, 1),
            'group_id' => 1,
            'store_id' => 1,
            'website_id' => 1,
            'taxVatNumber' => \Faker\Provider\at_AT\Payment::vat(),
            'company' => $faker->company,
            'phoneNumber' => $faker->phoneNumber,
            'address' => [
                'address1' => $faker->streetAddress,
                'address2' => $faker->streetAddress,
                'city' => $faker->city,
                'country' => 'United States',
                'state' => \Faker\Provider\en_US\Address::state(),
                'zipCode' => $faker->postcode
            ]
        ];
        return array_merge($customerData, $additional);
    }

    /**
     * Get Content Page Data.
     *
     * @return array
     */
    public function getContentPage()
    {
        $faker = \Faker\Factory::create();

        $pageContent = [
            'pageTitle' => $faker->sentence($nbWords = 3, $variableNbWords = true),
            'contentHeading' => $faker->sentence($nbWords = 3, $variableNbWords = true),
            'contentBody' => $faker->sentence($nbWords = 10, $variableNbWords = true),
            'urlKey' => $faker->uuid,
            'metaTitle' => $faker->word,
            'metaKeywords' => $faker->sentence($nbWords = 5, $variableNbWords = true),
            'metaDescription' => $faker->sentence($nbWords = 10, $variableNbWords = true),
            'layoutUpdateXml' => "<note><to>Tove</to><from>Jani</from><heading>Reminder</heading><body>Don't forget me this weekend!</body></note>",
            'from' => $faker->date($format = 'm/d/Y', $max = 'now'),
            'to' => $faker->date($format = 'm/d/Y')
        ];

        return $pageContent;
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
