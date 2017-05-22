<?php
namespace Magento\Xxyyzz\Page\Sales;

use Magento\Xxyyzz\Page\AbstractAdminPage;

class AdminOrderAddPage extends AbstractAdminPage
{
    /**
     * Sales - Orders - "Please select a customer" search grid Selectors
     */
    public static $selectCustomerCreateNewCustomerButton  = 'button[title="Create New Customer"]';

    public static $selectCustomerSearchButton             = '#sales_order_create_customer_grid button[title="Search"]';
    public static $selectCustomerResetFilterButton        = '#sales_order_create_customer_grid button[title="Reset Filter"]';

    public static $selectCustomerIdSearchField            = '#sales_order_create_customer_grid_filter_entity_id';
    public static $selectCustomerNameSearchField          = '#sales_order_create_customer_grid_filter_name';
    public static $selectCustomerEmailSearchField         = '#sales_order_create_customer_grid_filter_email';
    public static $selectCustomerPhoneSearchField         = '#sales_order_create_customer_grid_filter_Telephone';
    public static $selectCustomerZipPostCodeSearchField   = '#sales_order_create_customer_grid_filter_billing_postcode';
    public static $selectCustomerCountrySearchDropDown    = '#sales_order_create_customer_grid_filter_billing_country_id';
    public static $selectCustomerStateProvinceSearchField = '#sales_order_create_customer_grid_filter_billing_regione';
    public static $selectCustomerSignedUpPointSearchField = '#sales_order_create_customer_grid_filter_store_name';

    /**
     * Sales - Orders - "Please select a Store" Selectors
     */
    public static $defaultStoreViewLink                   = '#store_1';

    /**
     * Sales - Orders - "Create New Order" Selectors
     */
    public static $cancelButton                           = '#reset_order_top_button';
    public static $submitOrderButton                      = '#submit_order_top_button';


    /**
     * Sales - Orders - "Customer's Activities" Selectors
     */

    // TODO: Add Customer's Activities selectors

    /**
     * Sales - Orders - "Items Ordered" Selectors
     */
    public static $addProductsToOrderButton               = '#order-items .action-add';
    public static $addSelectedProductToOrderButton        = '#order-search .action-add';
    public static $productsSearchButton                   = '#sales_order_create_search_grid button[title="Search"]';
    public static $productsResetFilterButton              = '#sales_order_create_search_grid button[title="Reset Filter"]';
    public static $updateItemsAndQuantitiesButton         = 'button[title="Update Items and Quantities"]';

    // TODO: Add Order Product search page controls Selectors

    public static $productIdSearchField                   = '#sales_order_create_search_grid_filter_entity_id';
    public static $productNameSearchField                 = '#sales_order_create_search_grid_filter_name';
    public static $productSkuSearchField                  = '#sales_order_create_search_grid_filter_sku';
    public static $productPriceFromSearchField            = '#sales_order_create_search_grid_filter_price_from';
    public static $productPriceToSearchField              = '#sales_order_create_search_grid_filter_price_to';
    public static $productSelectSearchDropDown            = '#sales_order_create_search_grid_filter_in_products';

    public static $productResultsSelectCheckBox           = ''; // ?
    public static $productResultsQuantityField            = ''; // ?

    // TODO: Add Product grid Selectors

    public static $applyCouponCodeField                   = 'input[name="coupon_code"]';
    public static $applyCouponCodeButton                  = '#order-coupons button[title="Apply"]';

    // TODO: Add Product List Selectors

    /**
     * Sales - Orders - "Account Information" Selectors
     */
    public static $groupDropDown                          = '#group_id';
    public static $emailAddressField                      = '#email';

    /**
     * Sales - Orders - "Address Information" Selectors
     */
    public static $savedBillAddressesDropDown             = '#order-billing_address_customer_address_id';
    public static $billingPrefixField                     = '#order-billing_address_prefix';
    public static $billingFirstNameField                  = '#order-billing_address_firstname';
    public static $billingMiddleNameField                 = '#order-billing_address_middlename';
    public static $billingLastNameField                   = '#order-billing_address_lastname';
    public static $billingSuffixField                     = '#order-billing_address_suffix';
    public static $billingCompanyField                    = '#order-billing_address_company';
    public static $billingAddress1Field                   = '#order-billing_address_street0';
    public static $billingAddress2Field                   = '#order-billing_address_street1';
    public static $billingCityField                       = '#order-billing_address_city';
    public static $billingCountryDropDown                 = '#order-billing_address_country_id';
    public static $billingStateDropDown                   = '#order-billing_address_region_id';
    public static $billingProvinceField                   = '#order-billing_address_region';
    public static $billingZipPostalCodeField              = '#order-billing_address_postcode';
    public static $billingPhoneNumberField                = '#order-billing_address_telephone';
    public static $billingFaxNumberField                  = '#order-billing_address_fax';
    public static $billingVatNumberField                  = '#order-billing_address_vat_id';
    public static $billingValidateVatNumberLink           = '#order-billing_address button[title="Validate VAT Number"]]';
    public static $billingSaveInAddressBook               = '#order-billing_address_save_in_address_book';

    public static $sameAsBillingAddressCheckbox           = '#order-shipping_same_as_billing';
    public static $savedShippingAddressesDropDown         = '#order-shipping_address_customer_address_id';
    public static $shippingPrefixField                    = '#order-shipping_address_prefix';
    public static $shippingFirstNameField                 = '#order-shipping_address_firstname';
    public static $shippingMiddleNameField                = '#order-shipping_address_middlename';
    public static $shippingLastNameField                  = '#order-shipping_address_lastname';
    public static $shippingSuffixField                    = '#order-shipping_address_suffix';
    public static $shippingCompanyField                   = '#order-shipping_address_company';
    public static $shippingAddress1Field                  = '#order-shipping_address_street0';
    public static $shippingAddress2Field                  = '#order-shipping_address_street1';
    public static $shippingCityField                      = '#order-shipping_address_city';
    public static $shippingCountryDropDown                = '#order-shipping_address_country_id';
    public static $shippingStateDropDown                  = '#order-shipping_address_region_id';
    public static $shippingProvinceField                  = '#order-shipping_address_region';
    public static $shippingZipPostalCodeField             = '#order-shipping_address_postcode';
    public static $shippingPhoneNumberField               = '#order-shipping_address_telephone';
    public static $shippingFaxNumberField                 = '#order-shipping_address_fax';
    public static $shippingVatNumberField                 = '#order-shipping_address_vat_id';
    public static $shippingValidateVatNumberLink          = '#order-shipping_address button[title="Validate VAT Number"]';
    public static $shippingSaveInAddressBookCheckbox      = '#order-shipping_address_save_in_address_book';

    /**
     * Sales - Orders - "Payment & Shipping Information" Selectors
     */
    public static $paymentShippingInformationMainArea     = '#order-methods';
    public static $paymentMethodMainArea                  = '#order-billing_method';
    public static $noPaymentInformationRequiredText       = '#admin__payment-methods label[for="p_method_free"]';

    public static $shippingMethodMainArea                 = '#order-shipping_method';
    public static $getShippingMethodsAndRatesLink         = '#order-shipping-method-summary a';
    public static $sorryNoQuotesAreAvailableText          = '.order-shipping-method-not-available';

    public static $fixedFlatRateShippingMethod            = '';
    public static $tableRateFlatRateShippingMethod        = '';
    public static $freeShippingMethod                     = '';

    /**
     * Sales - Orders - "Order Total" Selectors
     */
    public static $orderCommentsField                     = '#order-comment';

    public static $subTotalPrice                          = "//tr[contains(@class, 'row-totals')]/td[contains(text(), 'Subtotal')]/parent::tr/td/span[contains(@class, 'price')]";
    public static $shippingAndHandlingPrice               = '';
    public static $discountPrice                          = '';
    public static $taxPrice                               = '';
    public static $grandTotalPrice                        = '';
    public static $appendCommentsCheckbox                 = '';
    public static $emailOrderConfirmationCheckbox         = '';

    public static $bottomSubmitOrderButton                = '.order-totals-actions button';

    /**
     * Sales - Orders - "Please select a customer" search grid Methods
     */
    public function clickOnCreateNewCustomerButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$selectCustomerCreateNewCustomerButton);
        $I->waitForPageLoad();
    }

    public function clickOnCustomerSearchButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$selectCustomerSearchButton);
        $I->waitForPageLoad();
    }

    public function clickOnCustomerResetFilterButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$selectCustomerResetFilterButton);
        $I->waitForPageLoad();
    }

    public function enterCustomerIdSearchTerm($customerId)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$selectCustomerIdSearchField, $customerId);
    }

    public function enterCustomerNameSearchTerm($customerName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$selectCustomerNameSearchField, $customerName);
    }

    public function enterCustomerEmailSearchTerm($customerEmail)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$selectCustomerEmailSearchField, $customerEmail);
    }

    public function enterCustomerPhoneSearchTerm($customerPhone)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$selectCustomerPhoneSearchField, $customerPhone);
    }

    public function enterZipPostalCodeSearchTerm($zipPostalCode)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$selectCustomerZipPostCodeSearchField, $zipPostalCode);
    }

    public function enterCustomerCountrySearchTerm($country)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$selectCustomerCountrySearchDropDown, $country);
    }

    public function enterCustomerStateProvinceSearchTerm($stateProvince)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$selectCustomerStateProvinceSearchField, $stateProvince);
    }

    public function enterSignedUpPointSearchTerm($signedUpPoint)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$selectCustomerSignedUpPointSearchField, $signedUpPoint);
    }

    public function clickOnCustomerFor($customerEmail)
    {
        $I = $this->acceptanceTester;
        $selector = "//td[contains(text(), '" . $customerEmail . "')]/parent::tr";

        $I->waitForLoadingMaskToDisappear();
        $I->click($selector);
        $I->waitForPageLoad();
    }

    /**
     * Sales - Orders - "Please select a Store" Methods
     */
    public function clickOnDefaultStoreView()
    {
        $I = $this->acceptanceTester;
        $I->waitForLoadingMaskToDisappear();
        $I->click(self::$defaultStoreViewLink);
        $I->waitForPageLoad();
    }

    /**
     * Sales - Orders - "Customer's Activities" Methods - Left panel
     */

    // TODO: Add Customer's Activities methods

    /**
     * Sales - Orders - "Items Ordered" Methods
     */
    public function clickOnAddProductsButton()
    {
        $I = $this->acceptanceTester;
        $I->waitForLoadingMaskToDisappear();
        $I->click(self::$addProductsToOrderButton);
        $I->waitForPageLoad();
    }

    public function enterIdSearchField($productId)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$productIdSearchField, $productId);
    }

    public function enterProductNameSearchField($productName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$productNameSearchField, $productName);
    }

    public function enterProductSkuSearchField($productSku)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$productSkuSearchField, $productSku);
    }

    public function enterProductPriceFromSearchField($productPriceFrom)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$productPriceFromSearchField, $productPriceFrom);
    }

    public function enterProductPriceToSearchField($productPriceTo)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$productPriceToSearchField, $productPriceTo);
    }

    public function selectProductSearchField($value)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$productSelectSearchDropDown, $value);
    }

    public function clickOnProductSkuFor($productSku)
    {
        $I = $this->acceptanceTester;
        $selector = "//td[contains(@class, 'col-sku')][contains(text(), '" . $productSku . "')]/parent::tr";
        $I->click($selector);
    }

    public function enterProductQtyFor($productSku, $productQty)
    {
        $I = $this->acceptanceTester;
        $selector = "//td[contains(@class, 'col-sku')][contains(text(), '" . $productSku . "')]/parent::tr";
        $I->fillField($selector, $productQty);
    }

    public function clickOnAddSelectedProductsToOrderButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$addSelectedProductToOrderButton);
        $I->waitForLoadingMaskToDisappear();
    }

    public function clickOnProductsSearchButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$productsSearchButton);
        $I->waitForLoadingMaskToDisappear();
    }

    public function clickOnProductsSearchResetButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$productsResetFilterButton);
        $I->waitForLoadingMaskToDisappear();
    }

    // TODO: Add Product methods
    

    /**
     * Sales - Orders - "Account Information" Methods
     */
    public function enterAccountInformationGroup($groupName)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$groupDropDown, $groupName);
    }

    public function selectAccountInformationGroupGeneral()
    {
        self::enterAccountInformationGroup('General');
    }

    public function enterAccountInformationEmail($customerEmail)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$emailAddressField, $customerEmail);
    }

    public function verifyAccountInformationEmail($customerEmail)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$emailAddressField, $customerEmail);
    }

    /**
     * Sales - Orders - "Address Information" Methods - Billing
     */
    public function enterBillingPrefix($prefix)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$billingPrefixField, $prefix);
    }

    public function verifyBillingPrefix($prefix)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$billingPrefixField, $prefix);
    }

    public function enterBillingFirstName($firstName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$billingFirstNameField, $firstName);
    }

    public function verifyBillingFirstName($firstName)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$billingFirstNameField, $firstName);
    }

    public function enterBillingMiddleName($middleName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$billingMiddleNameField, $middleName);
    }

    public function verifyBillingMiddleName($middleName)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$billingMiddleNameField, $middleName);
    }

    public function enterBillingLastName($lastName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$billingLastNameField, $lastName);
    }

    public function verifyBillingLastName($lastName)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$billingLastNameField, $lastName);
    }

    public function enterBillingSuffix($suffix)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$billingSuffixField, $suffix);
    }

    public function verifyBillingSuffix($suffix)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$billingSuffixField, $suffix);
    }

    public function enterBillingCompany($company)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$billingCompanyField, $company);
    }

    public function verifyBillingCompany($company)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$billingCompanyField, $company);
    }

    public function enterBillingAddress1($address1)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$billingAddress1Field, $address1);
    }

    public function verifyBillingAddress1($address1)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$billingAddress1Field, $address1);
    }

    public function enterBillingAddress2($address2)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$billingAddress2Field, $address2);
    }

    public function verifyBillingAddress2($address2)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$billingAddress2Field, $address2);
    }

    public function enterBillingCity($city)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$billingCityField, $city);
    }

    public function verifyBillingCity($city)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$billingCityField, $city);
    }

    public function enterBillingCountry($country)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$billingCountryDropDown, $country);
    }

    public function verifyBillingCountry($country)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$billingCountryDropDown, $country);
    }

    public function enterBillingState($stateProvince)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$billingStateDropDown, $stateProvince);
    }

    public function enterBillingProvince($province)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$billingProvinceField, $province);
    }

    public function verifyBillingState($state)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$billingStateDropDown, $state);
    }

    public function verifyBillingProvince($province)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$billingProvinceField, $province);
    }

    public function enterBillingZipPostalCode($zipCode)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$billingZipPostalCodeField, $zipCode);
    }

    public function verifyBillingZipPostalCode($zipCode)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$billingZipPostalCodeField, $zipCode);
    }

    public function enterBillingPhoneNumber($phoneNumber)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$billingPhoneNumberField, $phoneNumber);
    }

    public function verifyBillingPhoneNumber($phoneNumber)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$billingPhoneNumberField, $phoneNumber);
    }

    public function enterBillingFaxNumber($faxNumber)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$billingFaxNumberField, $faxNumber);
    }

    public function verifyBillingFaxNumber($faxNumber)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$billingFaxNumberField, $faxNumber);
    }

    public function enterBillingVatNumber($taxVatNumber)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$billingVatNumberField, $taxVatNumber);
    }

    public function verifyBillingVatNumber($taxVatNumber)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$billingVatNumberField, $taxVatNumber);
    }

    public function clickOnBillingValidateVatNumberLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$billingValidateVatNumberLink);
        $I->waitForLoadingMaskToDisappear();
    }

    public function clickOnBillingSaveInAddressBook()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$billingSaveInAddressBook);
    }

    /**
     * Sales - Orders - "Address Information" Methods - Shipping
     */
    public function clickOnSameAsBillingAddress()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$sameAsBillingAddressCheckbox);
    }

    public function enterShippingPrefix($prefix)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$shippingPrefixField, $prefix);
    }

    public function verifyShippingPrefix($prefix)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$shippingPrefixField, $prefix);
    }

    public function enterShippingFirstName($firstName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$shippingFirstNameField, $firstName);
    }

    public function verifyShippingFirstName($firstName)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$shippingFirstNameField, $firstName);
    }

    public function enterShippingMiddleName($middleName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$shippingMiddleNameField, $middleName);
    }

    public function verifyShippingMiddleName($middleName)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$shippingMiddleNameField, $middleName);
    }

    public function enterShippingLastName($lastName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$shippingLastNameField, $lastName);
    }

    public function verifyShippingLastName($lastName)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$shippingLastNameField, $lastName);
    }

    public function enterShippingSuffix($suffix)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$shippingSuffixField, $suffix);
    }

    public function verifyShippingSuffix($suffix)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$shippingSuffixField, $suffix);
    }

    public function enterShippingCompany($company)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$shippingCompanyField, $company);
    }

    public function verifyShippingCompany($company)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$shippingCompanyField, $company);
    }

    public function enterShippingAddress1($address1)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$shippingAddress1Field, $address1);
    }

    public function verifyShippingAddress1($address1)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$shippingAddress1Field, $address1);
    }

    public function enterShippingAddress2($address2)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$shippingAddress2Field, $address2);
    }

    public function verifyShippingAddress2($address2)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$shippingAddress2Field, $address2);
    }

    public function enterShippingCity($city)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$shippingCityField, $city);
    }

    public function verifyShippingCity($city)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$shippingCityField, $city);
    }

    public function enterShippingCountry($country)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$shippingCountryDropDown, $country);
    }

    public function verifyShippingCountry($country)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$shippingCountryDropDown, $country);
    }

    public function enterShippingState($stateProvince)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$shippingStateDropDown, $stateProvince);
    }

    public function enterShippingProvince($province)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$shippingProvinceField, $province);
    }

    public function verifyShippingState($state)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$shippingStateDropDown, $state);
    }

    public function verifyShippingProvince($province)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$shippingProvinceField, $province);
    }

    public function enterShippingZipPostalCode($zipCode)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$shippingZipPostalCodeField, $zipCode);
    }

    public function verifyShippingZipPostalCode($zipCode)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$shippingZipPostalCodeField, $zipCode);
    }

    public function enterShippingPhoneNumber($phoneNumber)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$shippingPhoneNumberField, $phoneNumber);
    }

    public function verifyShippingPhoneNumber($phoneNumber)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$shippingPhoneNumberField, $phoneNumber);
    }

    public function enterShippingFaxNumber($faxNumber)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$shippingFaxNumberField, $faxNumber);
    }

    public function verifyShippingFaxNumber($faxNumber)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$shippingFaxNumberField, $faxNumber);
    }

    public function enterShippingVatNumber($taxVatNumber)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$shippingVatNumberField, $taxVatNumber);
    }

    public function verifyShippingVatNumber($taxVatNumber)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$shippingVatNumberField, $taxVatNumber);
    }

    public function clickOnShippingValidateVatNumberLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$shippingValidateVatNumberLink);
        $I->waitForLoadingMaskToDisappear();
    }

    public function clickOnShippingSaveInAddressBook()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$shippingSaveInAddressBookCheckbox);
    }

    public function verifyBillingAddress($customerData)
    {
        self::verifyBillingPrefix($customerData['prefix']);
        self::verifyBillingFirstName($customerData['firstname']);
        self::verifyBillingMiddleName($customerData['middlename']);
        self::verifyBillingLastName($customerData['lastname']);
        self::verifyBillingSuffix($customerData['suffix']);
        self::verifyBillingCompany($customerData['company']);
        self::verifyBillingAddress1($customerData['address']['address1']);
        self::verifyBillingAddress2($customerData['address']['address2']);
        self::verifyBillingCity($customerData['address']['city']);
        self::verifyBillingCountry($customerData['address']['country']);
        self::verifyBillingState($customerData['address']['state']);
        self::verifyBillingZipPostalCode($customerData['address']['zipCode']);
        self::verifyBillingPhoneNumber($customerData['phoneNumber']);
        self::verifyBillingFaxNumber($customerData['faxNumber']);
    }

    public function verifyShippingAddress($customerData)
    {
        self::verifyShippingPrefix($customerData['prefix']);
        self::verifyShippingFirstName($customerData['firstname']);
        self::verifyShippingMiddleName($customerData['middlename']);
        self::verifyShippingLastName($customerData['lastname']);
        self::verifyShippingSuffix($customerData['suffix']);
        self::verifyShippingCompany($customerData['company']);
        self::verifyShippingAddress1($customerData['address']['address1']);
        self::verifyShippingAddress2($customerData['address']['address2']);
        self::verifyShippingCity($customerData['address']['city']);
        self::verifyShippingCountry($customerData['address']['country']);
        self::verifyShippingState($customerData['address']['state']);
        self::verifyShippingZipPostalCode($customerData['address']['zipCode']);
        self::verifyShippingPhoneNumber($customerData['phoneNumber']);
        self::verifyShippingFaxNumber($customerData['faxNumber']);
    }

    /**
     * Sales - Orders - "Payment & Shipping Information" Methods
     */
    public function clickOnGetShippingMethodsAndRatesLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$getShippingMethodsAndRatesLink);
        $I->waitForLoadingMaskToDisappear();
    }

    // TODO: Add Payment Method selection Methods

    // TODO: Add more Shipping Method selection Methods

    public function clickOnShippingMethod($shippingMethodName)
    {
        $I = $this->acceptanceTester;
        $selector = "//label[contains(@class, 'admin__field-label')][contains(text(), '" . $shippingMethodName . "')]/parent::li";
        $I->click($selector);
        $I->waitForLoadingMaskToDisappear();
    }

    public function clickOnFixedShippingMethod()
    {
        self::clickOnShippingMethod('Fixed');
    }

    public function clickOnTableRateShippingMethod()
    {
        self::clickOnShippingMethod('Table Rate');
    }

    public function clickOnFreeShippingMethod()
    {
        self::clickOnShippingMethod('Free');
    }

    /**
     * Sales - Orders - "Order Total" Methods
     */
    public function enterOrderComments($comments)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$orderCommentsField, $comments);
    }

    public function verifyOrderComments($comments)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$orderCommentsField, $comments);
    }

    public function getSubTotalPriceValue()
    {
        $I = $this->acceptanceTester;
        return $I->grabTextFrom(self::$subTotalPrice);
    }

    public function getShippingHandlingValue()
    {
        $I = $this->acceptanceTester;
        return $I->grabTextFrom(self::$shippingAndHandlingPrice);
    }

    public function getDiscountPriceValue()
    {
        $I = $this->acceptanceTester;
        return $I->grabTextFrom(self::$discountPrice);
    }

    public function getTaxPriceValue()
    {
        $I = $this->acceptanceTester;
        return $I->grabTextFrom(self::$taxPrice);
    }

    public function getGrandTotalPriceValue()
    {
        $I = $this->acceptanceTester;
        return $I->grabTextFrom(self::$grandTotalPrice);
    }

    public function verifySubTotalPrice($expectedValue)
    {
        $I = $this->acceptanceTester;
        $I->assertEquals(self::getSubTotalPriceValue(), $expectedValue);
    }

    public function verifyShippingHandlingPrice($expectedValue)
    {
        $I = $this->acceptanceTester;
        $I->assertEquals(self::getShippingHandlingValue(), $expectedValue);
    }

    public function verifyDiscountPrice($expectedValue)
    {
        $I = $this->acceptanceTester;
        $I->assertEquals(self::getDiscountPriceValue(), $expectedValue);
    }

    public function verifyTaxPrice($expectedValue)
    {
        $I = $this->acceptanceTester;
        $I->assertEquals(self::getTaxPriceValue(), $expectedValue);
    }

    public function verifyGrandTotalPrice($expectedValue)
    {
        $I = $this->acceptanceTester;
        $I->assertEquals(self::getGrandTotalPriceValue(), $expectedValue);
    }

    public function clickOnAppendComments()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$appendCommentsCheckbox);
    }

    public function clickOnEmailOrderConfirmation()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$emailOrderConfirmationCheckbox);
    }

    public function clickOnBottomSubmitButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$bottomSubmitOrderButton);
        $I->waitForPageLoad();
    }
}