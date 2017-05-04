<?php
namespace Magento\Xxyyzz\Page\ConfigurableProduct;

use Magento\Xxyyzz\AcceptanceTester;
use Magento\Xxyyzz\Page\Catalog\AdminProductPage;
use Magento\Xxyyzz\Page\AdminGridPage;
use Codeception\Exception\ElementNotFound;

class AdminConfigurableProductPage extends AdminProductPage
{
    /**
     * Configurable product step wizard form.
     */

    public static $configurableTitle = '//header/h1[text()[contains(.,"Create Product Configurations")]]';
    public static $createProductConfigurationsForm = '.steps-wizard.productFormConfigurable';
    public static $nextButton = '.action-default.action-primary.action-next-step';

    /**
     * Page 1
     */
    public static $filterAttributeCode = '.admin__form-field input[name=attribute_code]';

    /**
     * Page 2
     */
    public static $attributeSelectAllButton = 'div[data-attribute-title=%s] .action-select-all.action-tertiary';
    public static $attributeDeSelectAllButton = 'div[data-attribute-title=%s] .action-deselect-all.action-tertiary';
    public static $attributeCreateNewValueButton = 'div[data-attribute-title=%s] button[data-action=addOption]';
    public static $newAttributeOptionText = 'div[data-attribute-title=%s] li[data-attribute-option-title=""] input[type=text]';
    public static $newAttributeOptionSave = 'div[data-attribute-title=%s] li[data-attribute-option-title=""] button[data-action=save]';
    public static $newAttributeOptionRemove = 'div[data-attribute-title=%s] li[data-attribute-option-title=""] button[data-action=remove]';
    public static $attributeOption = 'div[data-attribute-title=%s] li[data-attribute-option-title=%s]';
    public static $attributeOptionCheckbox = 'div[data-attribute-title=%s] li[data-attribute-option-title=%s] .admin__control-checkbox';

    /**
     * Page 3
     */
    public static $applySingleImageRadioButton = '#apply-single-set-radio';
    public static $applyUniqueImageRadioButton = '#apply-unique-images-radio';
    public static $skipImageRadioButton = '#skip-images-uploading-radio';

    public static $applySinglePriceRadioButton = '#apply-single-price-radio';
    public static $applySinglePriceField = '#apply-single-price-input';
    public static $applyUniquePriceRadioButton = '#apply-unique-prices-radio';
    public static $applyUniquePriceDropDown = '#select-each-price';
    public static $applyUniquePriceField = '#apply-single-price-input-%s';
    public static $skipPriceRadioButton = '#skip-pricing-radio';

    public static $applySingleQuantityRadioButton = '#apply-single-inventory-radio';
    public static $applySingleQuantityField = '#apply-single-inventory-input';
    public static $applyUniqueQuantityRadioButton = '#apply-unique-inventory-radio';
    public static $applyUniqueQuantityDropDown = '#apply-single-price-input-qty';
    public static $applyUniqueQuantityField = '#apply-qty-input-%s';
    public static $skipQuantityRadioButton = '#skip-inventory-radio';

    public static $configurableVariationsGrid = '.admin__dynamic-rows.data-grid';
    private static $lastConfigurableVariation = '.admin__dynamic-rows.data-grid tbody>tr:last-child';

    /**
     * @var AdminGridPage
     */
    public static $configurableAttributesGrid;

    public function __construct(AcceptanceTester $I)
    {
        parent::__construct($I);
        if (is_null(self::$configurableAttributesGrid)) {
            self::$configurableAttributesGrid = new AdminGridPage($I);
        }
    }

    public function amOnCreateProductConfigurationsForm()
    {
        $I = $this->acceptanceTester;
        $I->waitForPageLoad();
        $I->seeElement(self::$createProductConfigurationsForm);
    }

    public function filterAndSelectAttributeByCode($code)
    {
        self::$configurableAttributesGrid->searchAndFiltersByValue($code, self::$filterAttributeCode);
    }

    public function checkCheckboxInCurrentNthRow(int $n)
    {
        self::$configurableAttributesGrid->checkCheckboxInCurrentNthRow($n);
    }

    public function clickNextButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$nextButton);
        $I->waitForPageLoad();
    }

    public function clickCreateNewAttributeValue($attribute)
    {
        $I = $this->acceptanceTester;
        $I->click(sprintf(self::$attributeCreateNewValueButton, $attribute));
        $I->waitForPageLoad();
    }

    public function fillFieldAttributeOptionValue($attribute, $option)
    {
        $I = $this->acceptanceTester;
        $I->fillField(sprintf(self::$newAttributeOptionText, $attribute), $option);
        $I->waitForPageLoad();
    }

    public function clickSaveAttributeOption($attribute)
    {
        $I = $this->acceptanceTester;
        $I->click(sprintf(self::$newAttributeOptionSave, $attribute));
        $I->waitForPageLoad();
    }

    public function clickRemoveAttributeOption($attribute)
    {
        $I = $this->acceptanceTester;
        $I->click(sprintf(self::$newAttributeOptionRemove, $attribute));
        $I->waitForPageLoad();
    }

    public function clickApplySingleImageRadioButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$applySingleImageRadioButton);
        $I->waitForPageLoad();
    }

    public function clickApplyUniqueImageRadioButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$applyUniqueImageRadioButton);
        $I->waitForPageLoad();
    }

    public function clickSkipImageRadioButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$skipImageRadioButton);
    }

    public function clickApplySinglePriceRadioButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$applySinglePriceRadioButton);
        $I->waitForPageLoad();
    }

    public function clickApplyUniquePriceRadioButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$applyUniquePriceRadioButton);
        $I->waitForPageLoad();
    }

    public function clickSkipPriceRadioButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$skipPriceRadioButton);
    }

    public function clickApplySingleQuantityRadioButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$applySingleQuantityRadioButton);
        $I->waitForPageLoad();
    }

    public function clickApplyUniqueQuantityRadioButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$applyUniqueQuantityRadioButton);
        $I->waitForPageLoad();
    }

    public function clickSkipQuantityRadioButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$skipQuantityRadioButton);
    }

    public function fillFieldApplySinglePrice($price)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$applySinglePriceField, $price);
    }

    public function fillFieldApplySingleQuantity($quantity)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$applySingleQuantityField, $quantity);
    }

    /**
     * @param string $attribute
     */
    public function selectAttributeToApplyUniquePrice($attribute)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$applyUniquePriceDropDown, $attribute);
        $I->waitForPageLoad();
    }

    /**
     * @param string $attribute
     */
    public function selectAttributeToApplyUniqueQuantity($attribute)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$applyUniqueQuantityDropDown, $attribute);
        $I->waitForPageLoad();
    }

    /**
     * @param array $prices
     */
    public function fillFieldWithUniquePrice(array $prices)
    {
        $I = $this->acceptanceTester;
        $c = 0;
        foreach ($prices as $price) {
            $I->fillField(sprintf(self::$applyUniquePriceField, $c), $price);
            $c++;
        }
    }

    /**
     * @param array $quantities
     */
    public function fillFieldWithUniqueQuantity(array $quantities)
    {
        $I = $this->acceptanceTester;
        for ($c = 0; $c < count($quantities); $c++) {
            $I->fillField(sprintf(self::$applyUniqueQuantityField, $c), $quantities[$c]);
        }
    }

    /**
     * check and select attribute option, create if it does not exist.
     *
     * @param $attribute
     * @param $option
     */
    public function checkAndSelectAttributeOption($attribute, $option)
    {
        $I = $this->acceptanceTester;
        try {
            $I->seeElement(sprintf(self::$attributeOption, $attribute, $option));
            try {
                $I->dontSeeCheckboxIsChecked(sprintf(self::$attributeOptionCheckbox, $attribute, $option));
                $I->checkOption(sprintf(self::$attributeOptionCheckbox, $attribute, $option));
            } catch (ElementNotFound $e) {
            }
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            $this->clickCreateNewAttributeValue($attribute);
            $this->fillFieldAttributeOptionValue($attribute, $option);
            $this->clickSaveAttributeOption($attribute);
            $I->checkOption(sprintf(self::$attributeOptionCheckbox, $attribute, $option));
        }
    }

    public function seeProductPriceDisabled()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$productPrice, ['disabled' => 'true']);
    }

    public function seeProductQuantityDisabled()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$productQuantity, ['disabled' => 'true']);
    }

    /**
     * @see $texts in configurable product variations section.
     *
     * @param array $texts
     */
    public function seeInConfigurableVariations(array $texts)
    {
        $I = $this->acceptanceTester;
        $I->waitForPageLoad();
        foreach ($texts as $text) {
            $I->see($text, self::$configurableVariationsGrid);
        }
    }

    /**
     * @param int $number
     */
    public function assertNumberOfConfigurableVariations(int $number)
    {
        $I = $this->acceptanceTester;
        $I->waitForPageLoad();
        $I->assertEquals($number, intval($this->getIndexForLastConfigurableVariation())+1);
    }

    private function getIndexForLastConfigurableVariation()
    {
        $I = $this->acceptanceTester;
        return $I->grabAttributeFrom(self::$lastConfigurableVariation, 'data-repeat-index');
    }
}
