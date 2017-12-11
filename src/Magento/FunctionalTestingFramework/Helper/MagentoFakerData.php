<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Helper;

/**
 * Class MagentoFakerData
 */
class MagentoFakerData extends \Codeception\Module
{
    /**
     * Get Customer data.
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
            'dateOfBirth' => $faker->date('m/d/Y', 'now'),
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
     * Get category data.
     *
     * @return array
     */
    public function getCategoryData()
    {
        $faker = \Faker\Factory::create();
        
        return [
            'enableCategory' => $faker->boolean(),
            'includeInMenu' => $faker->boolean(),
            'categoryName' => $faker->md5,
            'categoryImage' => '',
            'description' => $faker->sentence(10, true),
            'addCMSBlock' => '',

            'urlKey' => $faker->uuid,
            'metaTitle' => $faker->word,
            'metaKeywords' => $faker->sentence(5, true),
            'metaDescription' => $faker->sentence(10, true),
        ];
    }

    /**
     * Get simple product data.
     *
     * @return array
     */
    public function getProductData()
    {
        $faker = \Faker\Factory::create();
        return [
            'enableProduct' => $faker->boolean(),
            'attributeSet' => '',
            'productName' => $faker->text(20),
            'sku' => \Faker\Provider\DateTime::unixTime('now'),
            'price' => $faker->randomFloat(2, 0, 999),
            'quantity' => $faker->numberBetween(1, 999),

            'urlKey' => $faker->uuid,
            'metaTitle' => $faker->word,
            'metaKeywords' => $faker->sentence(5, true),
            'metaDescription' => $faker->sentence(10, true)
        ];
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
            'pageTitle' => $faker->sentence(3, true),
            'contentHeading' => $faker->sentence(3, true),
            'contentBody' => $faker->sentence(10, true),
            'urlKey' => $faker->uuid,
            'metaTitle' => $faker->word,
            'metaKeywords' => $faker->sentence(5, true),
            'metaDescription' => $faker->sentence(10, true),
            'from' => $faker->date($format = 'm/d/Y', 'now'),
            'to' => $faker->date($format = 'm/d/Y')
        ];
        $pageContent['layoutUpdateXml'] = "<note><to>Tove</to><from>Jani</from><heading>Reminder</heading>";
        $pageContent['layoutUpdateXml'] .= "<body>Don't forget me this weekend!</body></note>";

        return $pageContent;
    }
}
