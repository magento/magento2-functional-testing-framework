<?php
namespace Magento\Xxyyzz\Page\Customer;

use Magento\Xxyyzz\Page\AbstractAdminPage;

class AdminCustomerPage extends AbstractAdminPage
{
    /**
     * Include url of current page.
     */
    public static $URL = '/admin/customer/index/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $saveCustomerButton            = '#save';
    public static $deleteCustomerButton          = '#customer-edit-delete-button';
    public static $createOrderButton             = '#customer-edit-delete-button';
    public static $resetPasswordButton           = '#resetPassword';
    public static $forceSignInButton             = '#invalidateToken';

    public static $customerInformationMainArea   = '.admin__page-nav';
    public static $customerViewLink              = '#tab_block_customer_edit_tab_view';
    public static $accountInformationLink        = '#tab_customer';
    public static $addressesLink                 = '#tab_address';
    public static $ordersLink                    = '#tab_block_orders';
    public static $billingAgreementsLink         = '#tab_block_customer_edit_tab_agreements';
    public static $newsletterLink                = '#tab_block_newsletter';
    public static $productReviewsLink            = '#tab_block_reviews';
    public static $wishListLink                  = '#tab_block_wishlist';

    public static $sectionTitle                  = '.page-title';

    // TODO: Add Selectors for the "Customer View" section and controls

    public static $associateToWebsiteDropDown    = '.admin__control-select[name="customer[website_id]"]';
    public static $groupDropDown                 = '.admin__control-select[name="customer[group_id]"]';
    public static $disableAutomaticGroupCheckbox = '.admin__field[data-index="disable_auto_group_change"] .admin__field-label';
    public static $prefixField                   = '.admin__control-text[name="customer[prefix]"]';
    public static $firstNameField                = '.admin__control-text[name="customer[firstname]"]';
    public static $middleNameField               = '.admin__control-text[name="customer[middlename]"]';
    public static $lastNameField                 = '.admin__control-text[name="customer[lastname]"]';
    public static $suffixField                   = '.admin__control-text[name="customer[suffix]"]';
    public static $emailField                    = '.admin__control-text[name="customer[email]"]';
    public static $dateOfBirthField              = '.admin__control-text[name="customer[dob]"]';
    public static $taxVatNumberField             = '.admin__control-text[name="customer[taxvat]"]';
    public static $genderDropDown                = '.admin__control-select[name="customer[gender]"]';
    public static $sendWelcomeEmailFromDropDown  = '.admin__control-select[name="customer[sendemail_store_id]"]';

    public static $addNewAddressesButton         = '.add';

    public static $newAddressTypeMainArea        = '.address-list-item.ui-state-active';
    public static $newAddressDefaultBilling      = '.admin__control-checkbox[name*="[default_billing]"]';
    public static $newAddressDefaultShipping     = '.admin__control-checkbox[name*="[default_shipping]"]';

    public static $addPrefixField                = '.address-item-edit-content .admin__control-text[name*="[prefix]"]';
    public static $addFirstNameField             = '.address-item-edit-content .admin__control-text[name*="[firstname]"]';
    public static $addMiddleNameField            = '.address-item-edit-content .admin__control-text[name*="[middlename]"]';
    public static $addLastNameField              = '.address-item-edit-content .admin__control-text[name*="[lastname]"]';
    public static $addSuffixField                = '.address-item-edit-content .admin__control-text[name*="[suffix]"]';
    public static $addCompanyField               = '.address-item-edit-content .admin__control-text[name*="[company]"]';
    public static $addAddress1Field              = '.address-item-edit-content .admin__control-text[name*="[street][0]"]';
    public static $addAddress2Field              = '.address-item-edit-content .admin__control-text[name*="[street][1]"]';
    public static $addCityField                  = '.address-item-edit-content .admin__control-text[name*="[city]"]';
    public static $addCountryDropDown            = '.address-item-edit-content .admin__control-select[name*="[country_id]"]';
    public static $addStateDropDown              = '.address-item-edit-content .admin__control-select[name*="[region_id]"]';
    public static $addProvinceField              = '.address-item-edit-content .admin__control-text[name*="[region]"]';
    public static $addZipPostalCodeField         = '.address-item-edit-content .admin__control-text[name*="[postcode]"]';
    public static $addPhoneNumberField           = '.address-item-edit-content .admin__control-text[name*="[telephone]"]';
    public static $addVatNumberField             = '.address-item-edit-content .admin__control-text[name*="[vat_id]"]';

    // TODO: Add Selectors for the "Orders" section and controls
    // TODO: Add Selectors for the "Billing Agreements" section and controls
    // TODO: Add Selectors for the "Newsletter" section and controls
    // TODO: Add Selectors for the "Product Reviews" section and controls
    // TODO: Add Selectors for the "Wish List" section and controls

    public function clickOnAddCustomerButton()
    {
        $I = $this->acceptanceTester;
        self::clickOnAdminAddButton();
        $I->waitForPageLoad();
    }

    public function clickOnSaveCustomerButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$saveCustomerButton);
        $I->waitForPageLoad();
    }
    
    public function clickOnDeleteCustomerButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$deleteCustomerButton);
    }

    public function clickOnCreateOrderButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$createOrderButton);
    }

    public function clickOnResetPasswordButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$resetPasswordButton);
    }

    public function clickOnForceSignInButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$forceSignInButton);
    }

    public function clickOnCustomerViewLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$customerViewLink);
    }

    public function clickOnAccountInformationLink()
    {
        $I = $this->acceptanceTester;
        $I->scrollToTopOfPage();
        $I->click(self::$accountInformationLink);
    }

    public function clickOnAddressesLink()
    {
        $I = $this->acceptanceTester;
        $I->scrollToTopOfPage();
        $I->click(self::$addressesLink);
    }

    public function clickOnOrdersLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$ordersLink);
    }

    public function clickOnBillingAgreementsLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$billingAgreementsLink);
    }

    public function clickOnNewsletterLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$newsletterLink);
    }

    public function clickOnProductReviewsLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$productReviewsLink);
    }

    public function clickOnWishListLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$wishListLink);
    }

    // TODO: Add Methods for the "Customer View" section and controls

    public function selectAssociateToWebsite($associateWebsite)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$associateToWebsiteDropDown, $associateWebsite);
    }

    public function verifyAssociateToWebsite($associateWebsite)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$associateToWebsiteDropDown, $associateWebsite);
    }

    public function selectAssociateToWebsiteMainWebsite()
    {
        self::selectAssociateToWebsite('Main Website');
    }

    public function verifyAssociateToWebsiteMainWebsite()
    {
        self::verifyAssociateToWebsite('Main Website');
    }

    public function selectGroup($group)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$groupDropDown, $group);
    }

    public function verifyGroup($group)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$groupDropDown, $group);
    }

    public function selectGroupGeneral()
    {
        self::selectGroup('General');
    }

    public function verifyGroupGeneral()
    {
        self::verifyGroup('General');
    }

    public function selectGroupWholesale()
    {
        $I = $this->acceptanceTester;
        self::selectGroup('Wholesale');
    }

    public function verifyGroupWholesale()
    {
        self::verifyGroup('Wholesale');
    }

    public function clickOnDisableAutomaticGroupCheckbox()
    {
        $I = $this->acceptanceTester;
        $I->checkOption(self::$disableAutomaticGroupCheckbox);
    }

    public function seeDisableAutomaticGroupIs($expectedState)
    {
        $I = $this->acceptanceTester;
        $I->seeCheckboxIsChecked(self::$disableAutomaticGroupCheckbox, $expectedState);
    }

    public function enterPrefix($prefix)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$prefixField, $prefix);
    }

    public function verifyPrefix($prefix)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$prefixField, $prefix);
    }

    public function enterFirstName($firstName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$firstNameField, $firstName);
    }

    public function verifyFirstName($firstName)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$firstNameField, $firstName);
    }

    public function enterMiddleName($middleName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$middleNameField, $middleName);
    }

    public function verifyMiddleName($middleName)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$middleNameField, $middleName);
    }

    public function enterLastName($lastName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$lastNameField, $lastName);
    }

    public function verifyLastName($lastName)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$lastNameField, $lastName);
    }

    public function enterSuffix($suffix)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$suffixField, $suffix);
    }

    public function verifySuffix($suffix)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$suffixField, $suffix);
    }

    public function enterEmailAddress($emailAddress)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$emailField, $emailAddress);
    }

    public function verifyEmailAddress($emailAddress)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$emailField, $emailAddress);
    }

    public function enterDateOfBirth($dateOfBirth)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$dateOfBirthField, $dateOfBirth);
    }

    public function verifyDateOfBirth($dateOfBirth)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$dateOfBirthField, $dateOfBirth);
    }

    public function enterTaxVatNumber($taxVatNumber)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$taxVatNumberField, $taxVatNumber);
    }

    public function verifyTaxVatNumber($taxVatNumber)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$taxVatNumberField, $taxVatNumber);
    }

    public function selectGender($gender)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$genderDropDown, $gender);
    }

    public function verifyGender($gender)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$genderDropDown, $gender);
    }

    public function selectGenderMale()
    {
        self::selectGender('Male');
    }

    public function selectGenderFemale()
    {
        self::selectGender('Female');
    }

    public function selectGenderNotSpecified()
    {
        self::selectGender('Not Specified');
    }

    public function verifyGenderMale()
    {
        self::verifyGender('Male');
    }

    public function verifyGenderFemale()
    {
        self::verifyGender('Female');
    }

    public function verifyGenderNotSpecified()
    {
        self::verifyGender('Not Specified');
    }
    
    public function selectSendWelcomeEmailFrom($storeName)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$sendWelcomeEmailFromDropDown, $storeName);
    }

    public function verifySendWelcomeEmailFrom($storeName)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$sendWelcomeEmailFromDropDown, $storeName);
    }

    public function selectSendWelcomeEmailFromDefaultStoreView()
    {
        self::selectSendWelcomeEmailFrom('Default Store View');
    }

    public function verifySendWelcomeEmailFromDefaultStoreView()
    {
        self::verifySendWelcomeEmailFrom('Default Store View');
    }

    public function clickOnAddNewAddressButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$addNewAddressesButton);
    }

    public function clickOnAddNewAddressDefaultBillingAddress()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$newAddressDefaultBilling);
    }

    public function verifyAddAddressDefaultBillingAddress($expectedState)
    {
        $I = $this->acceptanceTester;
        if ($expectedState) {
            $I->seeCheckboxIsChecked(self::$newAddressDefaultBilling);
        } else {
            $I->dontSeeCheckboxIsChecked(self::$newAddressDefaultBilling);
        }
    }

    public function verifyAddAddressDefaultShippingAddress($expectedState)
    {
        $I = $this->acceptanceTester;
        if ($expectedState) {
            $I->seeCheckboxIsChecked(self::$newAddressDefaultShipping);
        } else {
            $I->dontSeeCheckboxIsChecked(self::$newAddressDefaultShipping);
        }
    }

    public function clickOnAddNewAddressDefaultShippingAddress()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$newAddressDefaultShipping);
    }

    public function enterAddAddressPrefix($prefix)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$addPrefixField, $prefix);
    }

    public function verifyAddAddressPrefix($prefix)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$addPrefixField, $prefix);
    }

    // TODO: Find better solution for the First/Last Name field validation issue.

    public function enterAddAddressFirstName($firstName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$addFirstNameField, $firstName);
    }

    public function verifyAddAddressFirstName($firstName)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$addFirstNameField, $firstName);
    }

    public function enterAddAddressMiddleName($middleName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$addMiddleNameField, $middleName);
    }

    public function verifyAddAddressMiddleName($middleName)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$addMiddleNameField, $middleName);
    }

    public function enterAddAddressLastName($lastName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$addLastNameField, $lastName);
    }

    public function verifyAddAddressLastName($lastName)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$addLastNameField, $lastName);
    }

    public function enterAddAddressSuffix($suffix)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$addSuffixField, $suffix);
    }

    public function verifyAddAddressSuffix($suffix)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$addSuffixField, $suffix);
    }

    public function enterAddAddressCompany($company)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$addCompanyField, $company);
    }

    public function verifyAddAddressCompany($company)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$addCompanyField, $company);
    }

    public function enterAddAddressAddress1($address1)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$addAddress1Field, $address1);
    }

    public function verifyAddAddressAddress1($address1)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$addAddress1Field, $address1);
    }

    public function enterAddAddressAddress2($address2)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$addAddress2Field, $address2);
    }

    public function verifyAddAddressAddress2($address2)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$addAddress2Field, $address2);
    }

    public function enterAddAddressCity($city)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$addCityField, $city);
    }

    public function verifyAddAddressCity($city)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$addCityField, $city);
    }

    public function enterAddAddressCountry($country)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$addCountryDropDown, $country);
    }

    public function verifyAddAddressCountry($country)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$addCountryDropDown, $country);
    }

    public function enterAddAddressState($stateProvince)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$addStateDropDown, $stateProvince);
    }

    public function enterAddAddressProvince($province)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$addProvinceField, $province);
    }

    public function verifyAddAddressState($state)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$addStateDropDown, $state);
    }

    public function verifyAddAddressProvince($province)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$addProvinceField, $province);
    }

    public function enterAddAddressZipPostalCode($zipCode)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$addZipPostalCodeField, $zipCode);
    }

    public function verifyAddAddressZipPostalCode($zipCode)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$addZipPostalCodeField, $zipCode);
    }

    public function enterAddAddressPhoneNumber($phoneNumber)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$addPhoneNumberField, $phoneNumber);
    }

    public function verifyAddAddressPhoneNumber($phoneNumber)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$addPhoneNumberField, $phoneNumber);
    }

    public function enterAddAddressVatNumber($vatNumber)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$addVatNumberField, $vatNumber);
    }

    public function verifyAddAddressVatNumber($vatNumber)
    {
        $I = $this->acceptanceTester;
        $I->seeInField(self::$addVatNumberField, $vatNumber);
    }

    // TODO: Add Methods for the "Orders" section and controls
    // TODO: Add Methods for the "Billing Agreements" section and controls
    // TODO: Add Methods for the "Newsletter" section and controls
    // TODO: Add Methods for the "Product Reviews" section and controls
    // TODO: Add Methods for the "Wish List" section and controls

    public function addBasicCustomerWithAddress($customerDetails)
    {
        self::clickOnAddCustomerButton();

        self::clickOnAddressesLink();
        self::clickOnAddNewAddressButton();

        self::clickOnAddNewAddressDefaultBillingAddress();
        self::clickOnAddNewAddressDefaultShippingAddress();

        self::enterAddAddressPrefix($customerDetails['prefix']);
        self::enterAddAddressFirstName($customerDetails['firstname']);
        self::enterAddAddressMiddleName($customerDetails['middlename']);
        self::enterAddAddressLastName($customerDetails['lastname']);
        self::enterAddAddressSuffix($customerDetails['suffix']);
        self::enterAddAddressCompany($customerDetails['company']);
        self::enterAddAddressAddress1($customerDetails['address']['address1']);
        self::enterAddAddressAddress2($customerDetails['address']['address2']);
        self::enterAddAddressCity($customerDetails['address']['city']);
        self::enterAddAddressCountry($customerDetails['address']['country']);
        self::enterAddAddressState($customerDetails['address']['state']);
        self::enterAddAddressZipPostalCode($customerDetails['address']['zipCode']);
        self::enterAddAddressPhoneNumber($customerDetails['phoneNumber']);
        self::enterAddAddressVatNumber($customerDetails['taxVatNumber']);

        self::clickOnAccountInformationLink();

        self::enterFirstName($customerDetails['firstname']);
        self::enterLastName($customerDetails['lastname']);
        self::enterEmailAddress($customerDetails['email']);
        self::selectAssociateToWebsiteMainWebsite();
        self::selectGroupGeneral();

        self::clickOnAdminSaveButton();
    }
}