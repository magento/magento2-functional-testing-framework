<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Magento\FunctionalTestingFramework\Module;

use Codeception\Module\REST;
use Magento\FunctionalTestingFramework\Module\MagentoSequence;
use Magento\FunctionalTestingFramework\Util\ConfigSanitizerUtil;
use Flow\JSONPath;

/**
 * MagentoRestDriver module provides Magento REST WebService.
 *
 * This module can be used either with frameworks or PHPBrowser.
 * If a framework module is connected, the testing will occur in the application directly.
 * Otherwise, a PHPBrowser should be specified as a dependency to send requests and receive responses from a server.
 *
 * ## Configuration
 *
 * * url *optional* - the url of api
 *
 * This module requires PHPBrowser or any of Framework modules enabled.
 *
 * ### Example
 *
 *     modules:
 *        enabled:
 *            - MagentoRestDriver:
 *                depends: PhpBrowser
 *                url: 'http://magento_base_url/rest/default/V1/'
 *
 *
 * ## Public Properties
 *
 * * headers - array of headers going to be sent.
 * * params - array of sent data
 * * response - last response (string)
 *
 * ## Parts
 *
 * * Json - actions for validating Json responses (no Xml responses)
 * * Xml - actions for validating XML responses (no Json responses)
 *
 * ## Conflicts
 *
 * Conflicts with SOAP module
 *
 */
class MagentoRestDriver extends REST
{
    /**
     * HTTP methods supported by REST.
     */
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_DELETE = 'DELETE';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_POST = 'POST';

    /**
     * Module required fields.
     *
     * @var array
     */
    protected $requiredFields = [
        'url',
        'username',
        'password'
    ];

    /**
     * Module configurations.
     *
     * @var array
     */
    protected $config = [
        'url' => '',
        'username' => '',
        'password' => ''
    ];

    /**
     * Admin tokens for Magento webapi access.
     *
     * @var array
     */
    protected static $adminTokens = [];

    /**
     * Before suite.
     *
     * @param array $settings
     * @return void
     */
    public function _beforeSuite($settings = [])
    {
        parent::_beforeSuite($settings);
        if (empty($this->config['url']) || empty($this->config['username']) || empty($this->config['password'])) {
            return;
        }

        $this->config = ConfigSanitizerUtil::sanitizeWebDriverConfig($this->config, ['url']);

        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->sendPOST(
            'integration/admin/token',
            ['username' => $this->config['username'], 'password' => $this->config['password']]
        );
        $token = substr($this->grabResponse(), 1, strlen($this->grabResponse())-2);
        $this->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        self::$adminTokens[$this->config['username']] = $token;
        // @codingStandardsIgnoreStart
        $this->getModule('\Magento\FunctionalTestingFramework\Module\MagentoSequence')->_initialize();
        // @codingStandardsIgnoreEnd
    }

    /**
     * After suite.
     * @return void
     */
    public function _afterSuite()
    {
        parent::_afterSuite();
        $this->deleteHeader('Authorization');
    }

    /**
     * Get admin auth token by username and password.
     *
     * @param string  $username
     * @param string  $password
     * @param boolean $newToken
     * @return string
     * @part json
     * @part xml
     */
    public function getAdminAuthToken($username = null, $password = null, $newToken = false)
    {
        $username = $username !== null ? $username : $this->config['username'];
        $password = $password !== null ? $password : $this->config['password'];

        // Use existing token if it exists
        if (!$newToken
            && (isset(self::$adminTokens[$username]) || array_key_exists($username, self::$adminTokens))) {
            return self::$adminTokens[$username];
        }
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->sendPOST('integration/admin/token', ['username' => $username, 'password' => $password]);
        $token = substr($this->grabResponse(), 1, strlen($this->grabResponse())-2);
        $this->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        self::$adminTokens[$username] = $token;
        return $token;
    }

    /**
     * Admin token authentication for a given user.
     *
     * @param string  $username
     * @param string  $password
     * @param boolean $newToken
     * @part json
     * @part xml
     * @return void
     */
    public function amAdminTokenAuthenticated($username = null, $password = null, $newToken = false)
    {
        $username = $username !== null ? $username : $this->config['username'];
        $password = $password !== null ? $password : $this->config['password'];

        $this->haveHttpHeader('Content-Type', 'application/json');
        if ($newToken || !isset(self::$adminTokens[$username])) {
            $this->sendPOST('integration/admin/token', ['username' => $username, 'password' => $password]);
            $token = substr($this->grabResponse(), 1, strlen($this->grabResponse()) - 2);
            $this->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
            self::$adminTokens[$username] = $token;
        }
        $this->amBearerAuthenticated(self::$adminTokens[$username]);
    }

    /**
     * Send REST API request.
     *
     * @param string  $endpoint
     * @param string  $httpMethod
     * @param array   $params
     * @param string  $grabByJsonPath
     * @param boolean $decode
     * @return mixed
     * @throws \LogicException
     * @part json
     * @part xml
     */
    public function sendRestRequest($endpoint, $httpMethod, $params = [], $grabByJsonPath = null, $decode = true)
    {
        $this->amAdminTokenAuthenticated();
        switch ($httpMethod) {
            case self::HTTP_METHOD_GET:
                $this->sendGET($endpoint, $params);
                break;
            case self::HTTP_METHOD_POST:
                $this->sendPOST($endpoint, $params);
                break;
            case self::HTTP_METHOD_PUT:
                $this->sendPUT($endpoint, $params);
                break;
            case self::HTTP_METHOD_DELETE:
                $this->sendDELETE($endpoint, $params);
                break;
            default:
                throw new \LogicException("HTTP method '{$httpMethod}' is not supported.");
        }
        $this->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        if (!$decode && $grabByJsonPath === null) {
            return $this->grabResponse();
        } elseif (!$decode) {
            return $this->grabDataFromResponseByJsonPath($grabByJsonPath);
        } else {
            return \GuzzleHttp\json_decode($this->grabResponse());
        }
    }

    /**
     * Create a category in Magento.
     *
     * @param array $categoryData
     * @return array|mixed
     * @part json
     * @part xml
     */
    public function requireCategory($categoryData = [])
    {
        if (!$categoryData) {
            $categoryData = $this->getCategoryApiData();
        }
        $categoryData = $this->sendRestRequest(
            self::$categoryEndpoint,
            self::HTTP_METHOD_POST,
            ['category' => $categoryData]
        );
        return $categoryData;
    }

    /**
     * Create a simple product in Magento.
     *
     * @param integer $categoryId
     * @param array   $simpleProductData
     * @return array|mixed
     * @part json
     * @part xml
     */
    public function requireSimpleProduct($categoryId = 0, $simpleProductData = [])
    {
        if (!$simpleProductData) {
            $simpleProductData = $this->getProductApiData('simple', $categoryId);
        }
        $simpleProductData = $this->sendRestRequest(
            self::$productEndpoint,
            self::HTTP_METHOD_POST,
            ['product' => $simpleProductData]
        );
        return $simpleProductData;
    }

    /**
     * Create a configurable product in Magento.
     *
     * @param integer $categoryId
     * @param array   $configurableProductData
     * @return array|mixed
     * @part json
     * @part xml
     */
    public function requireConfigurableProduct($categoryId = 0, $configurableProductData = [])
    {
        $configurableProductData = $this->getProductApiData('configurable', $categoryId, $configurableProductData);
        $this->sendRestRequest(
            self::$productEndpoint,
            self::HTTP_METHOD_POST,
            ['product' => $configurableProductData]
        );

        $attributeData = $this->getProductAttributeApiData();
        $attribute = $this->sendRestRequest(
            self::$productAttributesEndpoint,
            self::HTTP_METHOD_POST,
            $attributeData
        );
        $options = $this->sendRestRequest(
            sprintf(self::$productAttributesOptionsEndpoint, $attribute->attribute_code),
            self::HTTP_METHOD_GET
        );

        $attributeSetData = $this->getAssignAttributeToAttributeSetApiData($attribute->attribute_code);
        $this->sendRestRequest(
            self::$productAttributeSetEndpoint,
            self::HTTP_METHOD_POST,
            $attributeSetData
        );

        $simpleProduct1Data = $this->getProductApiData('simple', $categoryId);
        array_push(
            $simpleProduct1Data['custom_attributes'],
            [
                'attribute_code' => $attribute->attribute_code,
                'value' => $options[1]->value
            ]
        );
        $simpleProduct1Id = $this->sendRestRequest(
            self::$productEndpoint,
            self::HTTP_METHOD_POST,
            ['product' => $simpleProduct1Data]
        )->id;

        $simpleProduct2Data = $this->getProductApiData('simple', $categoryId);
        array_push(
            $simpleProduct2Data['custom_attributes'],
            [
                'attribute_code' => $attribute->attribute_code,
                'value' => $options[2]->value
            ]
        );
        $simpleProduct2Id = $this->sendRestRequest(
            self::$productEndpoint,
            self::HTTP_METHOD_POST,
            ['product' => $simpleProduct2Data]
        )->id;

        $tAttributes[] = [
            'id' => $attribute->attribute_id,
            'code' => $attribute->attribute_code
        ];

        $tOptions = [
            intval($options[1]->value),
            intval($options[2]->value)
        ];

        $configOptions = $this->getConfigurableProductOptionsApiData($tAttributes, $tOptions);

        $configurableProductData = $this->getConfigurableProductApiData(
            $configOptions,
            [$simpleProduct1Id, $simpleProduct2Id],
            $configurableProductData
        );

        $configurableProductData = $this->sendRestRequest(
            self::$productEndpoint . '/' . $configurableProductData['sku'],
            self::HTTP_METHOD_PUT,
            ['product' => $configurableProductData]
        );
        return $configurableProductData;
    }

    /**
     * Create a product attribute in Magento.
     *
     * @param string $code
     * @return array|mixed
     * @part json
     * @part xml
     */
    public function requireProductAttribute($code = 'attribute')
    {
        $attributeData = $this->getProductAttributeApiData($code);
        $attributeData = $this->sendRestRequest(
            self::$productAttributesEndpoint,
            self::HTTP_METHOD_POST,
            $attributeData
        );
        return $attributeData;
    }

    /**
     * Create a customer in Magento.
     *
     * @param array  $customerData
     * @param string $password
     * @return array|mixed
     * @part json
     * @part xml
     */
    public function requireCustomer(array $customerData = [], $password = '123123qW')
    {
        if (!$customerData) {
            $customerData = $this->getCustomerApiData();
        }
        $customerData = $this->getCustomerApiDataWithPassword($customerData, $password);
        $customerData = $this->sendRestRequest(
            self::$customersEndpoint,
            self::HTTP_METHOD_POST,
            $customerData
        );
        return $customerData;
    }

    /**
     * Get category api data.
     *
     * @param array $categoryData
     * @return array
     * @part json
     * @part xml
     */
    public function getCategoryApiData($categoryData = [])
    {
        $faker = \Faker\Factory::create();
        $sq = sqs();
        return array_replace_recursive(
            [
                'parent_id' => '2',
                'name' => 'category' . $sq,
                'is_active' => true,
                'include_in_menu' => true,
                'available_sort_by' => ['position', 'name'],
                'custom_attributes' => [
                    ['attribute_code' => 'url_key', 'value' => 'category' . $sq],
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
                    ['attribute_code' => 'page_layout', 'value' => ''],
                    ['attribute_code' => 'custom_design_to', 'value' => $faker->date($format = 'm/d/Y')],
                    ['attribute_code' => 'custom_design_from', 'value' => $faker->date($format = 'm/d/Y', 'now')]
                ]
            ],
            $categoryData
        );
    }

    /**
     * Get simple product api data.
     *
     * @param string  $type
     * @param integer $categoryId
     * @param array   $productData
     * @return array
     * @part json
     * @part xml
     */
    public function getProductApiData($type = 'simple', $categoryId = 0, $productData = [])
    {
        $faker = \Faker\Factory::create();
        $sq = sqs();
        return array_replace_recursive(
            [
                'sku' => $type . '_product_sku' . $sq,
                'name' => $type . '_product' . $sq,
                'visibility' => 4,
                'type_id' => $type,
                'price' => $faker->randomFloat(2, 1),
                'status' => 1,
                'attribute_set_id' => 4,
                'extension_attributes' => [
                    'stock_item' => ['is_in_stock' => 1, 'qty' => $faker->numberBetween(100, 9000)]
                ],
                'custom_attributes' => [
                    ['attribute_code' => 'url_key', 'value' => $type . '_product' . $sq],
                    ['attribute_code' => 'tax_class_id', 'value' => 2],
                    ['attribute_code' => 'category_ids', 'value' => $categoryId],
                ],
            ],
            $productData
        );
    }

    /**
     * Get Customer Api data.
     *
     * @param array $customerData
     * @return array
     * @part json
     * @part xml
     */
    public function getCustomerApiData($customerData = [])
    {
        $faker = \Faker\Factory::create();
        return array_replace_recursive(
            [
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
            ],
            $customerData
        );
    }

    /**
     * Get customer data including password.
     *
     * @param array  $customerData
     * @param string $password
     * @return array
     * @part json
     * @part xml
     */
    public function getCustomerApiDataWithPassword($customerData = [], $password = '123123qW')
    {
        return ['customer' => self::getCustomerApiData($customerData), 'password' => $password];
    }

    /**
     * @param string $code
     * @param array  $attributeData
     * @return array
     * @part json
     * @part xml
     */
    public function getProductAttributeApiData($code = 'attribute', $attributeData = [])
    {
        $sq = sqs();
        return array_replace_recursive(
            [
                'attribute' => [
                    'attribute_code' => $code . $sq,
                    'frontend_labels' => [
                        [
                            'store_id' => 0,
                            'label' => $code . $sq
                        ],
                    ],
                    'is_required' => false,
                    'is_unique' => false,
                    'is_visible' => true,
                    'scope' => 'global',
                    'default_value' => '',
                    'frontend_input' => 'select',
                    'is_visible_on_front' => true,
                    'is_searchable' => true,
                    'is_visible_in_advanced_search' => true,
                    'is_filterable' => true,
                    'is_filterable_in_search' => true,
                    //'is_used_in_grid' => true,
                    //'is_visible_in_grid' => true,
                    //'is_filterable_in_grid' => true,
                    'used_in_product_listing' => true,
                    'is_used_for_promo_rules' => true,
                    'options' => [
                        [
                            'label' => 'option1',
                            'value' => '',
                            'sort_order' => 0,
                            'is_default' => true,
                            'store_labels' => [
                                [
                                    'store_id' => 0,
                                    'label' => 'option1'
                                ],
                                [
                                    'store_id' => 1,
                                    'label' => 'option1'
                                ]
                            ]
                        ],
                        [
                            'label' => 'option2',
                            'value' => '',
                            'sort_order' => 1,
                            'is_default' => false,
                            'store_labels' => [
                                [
                                    'store_id' => 0,
                                    'label' => 'option2'
                                ],
                                [
                                    'store_id' => 1,
                                    'label' => 'option2'
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            $attributeData
        );
    }

    /**
     * @param array $attributes
     * @param array $optionIds
     * @return array
     * @part json
     * @part xml
     */
    public function getConfigurableProductOptionsApiData($attributes, $optionIds)
    {
        $configurableProductOptions = [];
        foreach ($attributes as $attribute) {
            $attributeItem = [
                'attribute_id' => (string)$attribute['id'],
                'label' => $attribute['code'],
                'values' => []
            ];
            foreach ($optionIds as $optionId) {
                $attributeItem['values'][] = ['value_index' => $optionId];
            }
            $configurableProductOptions [] = $attributeItem;
        }
        return $configurableProductOptions;
    }

    /**
     * @param array   $configurableProductOptions
     * @param array   $childProductIds
     * @param array   $configurableProduct
     * @param integer $categoryId
     * @return array
     * @part json
     * @part xml
     */
    public function getConfigurableProductApiData(
        array $configurableProductOptions,
        array $childProductIds,
        array $configurableProduct = [],
        int $categoryId = 0
    ) {
        if (!$configurableProduct) {
            $configurableProduct = $this->getProductApiData('configurable', $categoryId);
        }
        $configurableProduct = array_merge_recursive(
            $configurableProduct,
            [
                'extension_attributes' => [
                    'configurable_product_options' => $configurableProductOptions,
                    'configurable_product_links' => $childProductIds,
                ],
            ]
        );
        return $configurableProduct;
    }

    /**
     * @param string  $attributeCode
     * @param integer $attributeSetId
     * @param integer $attributeGroupId
     * @return array
     * @part json
     * @part xml
     */
    public function getAssignAttributeToAttributeSetApiData(
        $attributeCode,
        int $attributeSetId = 4,
        int $attributeGroupId = 7
    ) {
        return [
            'attributeSetId' => $attributeSetId,
            'attributeGroupId' => $attributeGroupId,
            'attributeCode' => $attributeCode,
            'sortOrder' => 0
        ];
    }
}
