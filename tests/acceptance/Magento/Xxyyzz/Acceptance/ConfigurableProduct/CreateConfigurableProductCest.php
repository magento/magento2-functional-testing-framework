<?php
namespace Magento\Xxyyzz\Acceptance\ConfigurableProduct;

use Magento\Xxyyzz\Page\ConfigurableProduct\AdminConfigurableProductPage;
use Magento\Xxyyzz\Step\Backend\AdminStep;
use Magento\Xxyyzz\Page\Catalog\AdminProductGridPage;
use Magento\Xxyyzz\Page\Catalog\AdminProductPage;
use Magento\Xxyyzz\Page\Catalog\StorefrontCategoryPage;
use Magento\Xxyyzz\Page\Catalog\StorefrontProductPage;
use Magento\Xxyyzz\Step\Catalog\Api\CategoryApiStep;
use Magento\Xxyyzz\Step\Catalog\Api\ProductApiStep;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Model\SeverityLevel;
use Yandex\Allure\Adapter\Annotation\TestCaseId;

/**
 * Class CreateConfigurableProductCest
 *
 * Allure annotations
 * @Features({"Catalog"})
 * @Stories({"Create configurable product"})
 *
 * Codeception annotations
 * @group configurable
 * @group add
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class CreateConfigurableProductCest
{
    /**
     * @var array
     */
    protected $category;

    /**
     * @var array
     */
    protected $product;

    /**
     * @var array
     */
    protected $productVariations;

    /**
     * @var array
     */
    protected $attributeValues = [];

    /**
     * @var array
     */
    protected $variationPrice = [];

    /**
     * @var array
     */
    protected $variationQuantity = [];

    public function _before(AdminStep $I, CategoryApiStep $categoryApi, ProductApiStep $productApi)
    {
        $I->loginAsAdmin();
        $this->category = $I->getCategoryApiData();
        $categoryApi->amAdminTokenAuthenticated();
        $this->category = array_merge(
            $this->category,
            ['id' => $categoryApi->createCategory(['category' => $this->category])]
        );
        $this->category['url_key'] = $this->category['custom_attributes'][0]['value'];
        $this->product = $I->getProductApiData('configurable', $this->category['id']);
        if ($this->product['extension_attributes']['stock_item']['is_in_stock'] !== 0) {
            $this->product['stock_status'] = 'In Stock';
            $this->product['qty'] = $this->product['extension_attributes']['stock_item']['qty'];
        } else {
            $this->product['stock_status'] = 'Out of Stock';
        }
        $this->product['url_key'] = $this->product['custom_attributes'][0]['value'];

        $this->productVariations = [
            [
                'attribute_code' => 'Color',
                'attribute_value' => 'red',
                'sku' => $this->product['sku'].'-red',
                'price' => '11.11',
                'qty' => $this->product['qty'],
            ],
            [
                'attribute_code' => 'Color',
                'attribute_value' => 'blue',
                'sku' => $this->product['sku'].'-blue',
                'price' => '22.22',
                'qty' => $this->product['qty'],
            ],
            [
                'attribute_code' => 'Color',
                'attribute_value' => 'white',
                'sku' => $this->product['sku'].'-white',
                'price' => '33.33',
                'qty' => $this->product['qty'],
            ]
        ];

        for ($c = 0; $c < count($this->productVariations); $c++) {
            $this->variationPrice[$c] = $this->productVariations[$c]['price'];
            $this->variationQuantity[$c] = $this->productVariations[$c]['qty'];
            $this->attributeValues[$c] = $this->productVariations[$c]['attribute_value'];
        }
    }

    public function _after(AdminStep $I)
    {
        //$I->goToTheAdminLogoutPage();
    }

    /**
     * Allure annotations
     * @Title("Create a configurable product and verify on the storefront")
     * @Description("Create a configurable product and verify on the storefront.")
     * @TestCaseId("")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminProductGridPage", value = "$adminProductGridPage")
     * @Parameter(name = "AdminConfigurableProductPage", value = "$adminConfigurableProductPage")
     * @Parameter(name = "StorefrontCategoryPage", value = "$storefrontCategoryPage")
     * @Parameter(name = "StorefrontProductPage", value = "$storefrontProductPage")
     *
     * @param AdminStep $I
     * @param AdminProductGridPage $adminProductGridPage
     * @param AdminConfigurableProductPage $adminConfigurableProductPage
     * @param StorefrontCategoryPage $storefrontCategoryPage
     * @param StorefrontProductPage $storefrontProductPage
     * @return void
     */
    public function createConfigurableProductTest(
        AdminStep $I,
        AdminProductGridPage $adminProductGridPage,
        AdminConfigurableProductPage $adminConfigurableProductPage,
        StorefrontCategoryPage $storefrontCategoryPage,
        StorefrontProductPage $storefrontProductPage
    ) {
        $I->wantTo('create configurable product with required fields in admin product page.');
        $adminProductGridPage->amOnAdminProductGridPage();
        $adminProductGridPage->clickOnAddConfigurableProductOption();
        $adminConfigurableProductPage->amOnAdminNewProductPage();
        $adminConfigurableProductPage->fillFieldProductName($this->product['name']);
        $adminConfigurableProductPage->fillFieldProductSku($this->product['sku']);
        $adminConfigurableProductPage->fillFieldProductPrice($this->product['price']);
        if (isset($this->product['qty'])) {
            $adminConfigurableProductPage->fillFieldProductQuantity($this->product['qty']);
        }
        $adminConfigurableProductPage->selectProductStockStatus($this->product['stock_status']);
        $adminConfigurableProductPage->selectProductCategories([$this->category['name']]);
        $adminConfigurableProductPage->fillFieldProductUrlKey($this->product['url_key']);

        $I->wantTo('create configurations for product.');
        $adminConfigurableProductPage->clickCreateConfigurationsButton();

        $I->wantTo('on Create Product Configurations Wizard - Select Attributes...');
        $adminConfigurableProductPage->filterAndSelectAttributeByCode(
            strtolower($this->productVariations[0]['attribute_code'])
        );
        $adminConfigurableProductPage->checkCheckboxInCurrentNthRow(1);
        $adminConfigurableProductPage->clickNextButton();

        $I->wantTo('on Create Product Configurations Wizard - Attributes Values...');
        for ($c = 0; $c < count($this->productVariations); $c++) {
            $adminConfigurableProductPage->checkAndSelectAttributeOption(
                $this->productVariations[$c]['attribute_code'],
                $this->productVariations[$c]['attribute_value']
            );
        }
        $adminConfigurableProductPage->clickNextButton();

        $I->wantTo('on Create Product Configurations Wizard - Bulk Images, Price and Quantity...');
        $adminConfigurableProductPage->clickApplyUniquePriceRadioButton();
        $adminConfigurableProductPage->selectAttributeToApplyUniquePrice(
            $this->productVariations[0]['attribute_code']
        );
        $adminConfigurableProductPage->fillFieldWithUniquePrice($this->variationPrice);
        $adminConfigurableProductPage->clickApplySingleQuantityRadioButton();
        $adminConfigurableProductPage->fillFieldApplySingleQuantity($this->variationQuantity[0]);
        $adminConfigurableProductPage->clickNextButton();

        $I->wantTo('on Create Product Configurations Wizard - Summary...');
        $I->wantTo('generate configurable products.');
        $adminConfigurableProductPage->clickNextButton();

        $I->wantTo('see configurable product successfully saved message.');
        $adminConfigurableProductPage->saveProduct();
        $I->seeElement($adminConfigurableProductPage::$successMessage);

        $I->wantTo('verify configurable product data in admin product page.');
        $adminConfigurableProductPage->seeProductAttributeSet('Default');
        $adminConfigurableProductPage->seeProductName($this->product['name']);
        $adminConfigurableProductPage->seeProductSku($this->product['sku']);
        $adminConfigurableProductPage->seeProductPriceDisabled();
        $adminConfigurableProductPage->seeProductQuantityDisabled();
        $adminConfigurableProductPage->seeProductStockStatus($this->product['stock_status']);
        $adminConfigurableProductPage->seeProductCategories([$this->category['name']]);
        $adminConfigurableProductPage->seeProductUrlKey(str_replace('_', '-', $this->product['url_key']));
        $adminConfigurableProductPage->assertNumberOfConfigurableVariations(count($this->productVariations));
        foreach ($this->productVariations as $variation) {
            $adminConfigurableProductPage->seeInConfigurableVariations($variation);
        }

        $I->wantTo('verify configurable product data in frontend category page.');
        $storefrontCategoryPage->amOnCategoryPage($this->category['url_key']);
        $storefrontCategoryPage->seeProductLinksInPage(
            $this->product['name'],
            str_replace('_', '-', $this->product['url_key'])
        );
        $storefrontCategoryPage->seeProductNameInPage($this->product['name']);
        $storefrontCategoryPage->seeProductPriceInPage($this->product['name'], $this->variationPrice[0]);

        $I->wantTo('verify configurable product data in frontend product page.');
        $storefrontProductPage->amOnProductPage(str_replace('_', '-', $this->product['url_key']));
        $storefrontProductPage->seeProductNameInPage($this->product['name']);
        $storefrontProductPage->seeProductPriceInPage($this->variationPrice[0]);
        $storefrontProductPage->seeProductStockStatusInPage($this->product['stock_status']);
        $storefrontProductPage->seeProductSkuInPage($this->productVariations[0]['sku']);
        $storefrontProductPage->seeProductOptions($this->attributeValues);
    }
}
