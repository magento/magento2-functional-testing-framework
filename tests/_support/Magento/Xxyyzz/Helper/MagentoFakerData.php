<?php
namespace Magento\Xxyyzz\Helper;

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
            'description' => $faker->sentence($nbWords = 10, $variableNbWords = true),
            'addCMSBlock' => '',

            'urlKey' => $faker->uuid,
            'metaTitle' => $faker->word,
            'metaKeywords' => $faker->sentence($nbWords = 5, $variableNbWords = true),
            'metaDescription' => $faker->sentence($nbWords = 10, $variableNbWords = true),
        ];
    }

    /**
     * Get simple product data.
     *
     * @param integer $categoryId
     * @param array $productData
     * @return array
     */
    public function getProductData($categoryId = 0, $productData = [])
    {
        $faker = \Faker\Factory::create();
        return [
            'enableProduct' => $faker->boolean(),
            'attributeSet' => '',
            'productName' => $faker->text($maxNbChars = 20),
            'sku' => \Faker\Provider\DateTime::unixTime($max = 'now'),
            'price' => $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 999),
            'quantity' => $faker->numberBetween($min = 1, $max = 999),

            'urlKey' => $faker->uuid,
            'metaTitle' => $faker->word,
            'metaKeywords' => $faker->sentence($nbWords = 5, $variableNbWords = true),
            'metaDescription' => $faker->sentence($nbWords = 10, $variableNbWords = true)
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
}
