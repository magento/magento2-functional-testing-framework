<?php
namespace Magento\Xxyyzz\Page\Customer;

use Magento\Xxyyzz\Page\AdminGridPage;

class AdminCustomerGrid extends AdminGridPage
{
    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $addNewCustomerButton         = '#add';

    /**
     * UI map for admin data grid filter section.
     */
    public static $filterIdFromField            = '.admin__control-text[name="entity_id[from]"]';
    public static $filterIdToField              = '.admin__control-text[name="entity_id[to]"]';
    public static $filterCustomerSinceFromField = '.admin__control-text[name="created_at[from]"]';
    public static $filterCustomerSinceToField   = '.admin__control-text[name="created_at[to]"]';
    public static $filterDateOfBirthFromField   = '.admin__control-text[name="dob[from]"]';
    public static $filterDateOfBirthToField     = '.admin__control-text[name="dob[to]"]';
    public static $filterNameField              = '.admin__control-text[name="name"]';
    public static $filterEmailField             = '.admin__control-text[name="email"]';
    public static $filterGroupDropDown          = '.admin__control-select[name="group_id"]';
    public static $filterPhoneField             = '.admin__control-text[name="billing_telephone"]';
    public static $filterZipCodeField           = '.admin__control-text[name="billing_postcode"]';
    public static $filterCountryDropDown        = '.admin__control-select[name="billing_country_id"]';
    public static $filterStateProvinceField     = '.admin__control-text[name="billing_region"]';
    public static $filterWebSiteDropDown        = '.admin__control-select[name="website_id"]';
    public static $filterTaxVatNumberField      = '.admin__control-text[name="taxvat"]';
    public static $filterGenderDropDown         = '.admin__control-select[name="gender"]';

    public function clickOnAddNewCustomerButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$addNewCustomerButton);
        $I->waitForPageLoad();
    }

    public function enterFilterIdFrom($idFrom)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterIdFromField, $idFrom);
    }

    public function enterFilterIdTo($idTo)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterIdToField, $idTo);
    }

    public function enterFilterCustomerSinceFrom($customerSinceFrom)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterCustomerSinceFromField, $customerSinceFrom);
    }

    public function enterFilterCustomerSinceTo($customerSinceTo)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterCustomerSinceToField, $customerSinceTo);
    }

    public function enterFilterDateOfBirthFrom($dateOfBirthFrom)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterDateOfBirthFromField, $dateOfBirthFrom);
    }

    public function enterFilterDateOfBirthTo($dateOfBirthTo)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterDateOfBirthToField, $dateOfBirthTo);
    }

    public function enterFilterName($name)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterNameField, $name);
    }

    public function enterFilterEmail($email)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterEmailField, $email);
    }

    public function selectFilterGroup($group)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$filterGroupDropDown, $group);
    }

    public function enterFilterPhone($phoneNumber)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterPhoneField, $phoneNumber);
    }

    public function enterFilterZipCode($zipCode)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterZipCodeField, $zipCode);
    }

    public function selectFilterCountry($country)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$filterCountryDropDown, $country);
    }

    public function enterFilterStateProvince($stateProvince)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterStateProvinceField, $stateProvince);
    }

    public function selectFilterWebSite($website)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$filterWebSiteDropDown, $website);
    }

    public function enterFilterTaxVatNumber($taxVatNumber)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterTaxVatNumberField, $taxVatNumber);
    }

    public function selectFilterGender($gender)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$filterGenderDropDown, $gender);
    }
}