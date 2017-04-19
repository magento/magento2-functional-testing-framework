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
    

    public function clickOnAddCustomerButton()
    {
        self::clickOnAdminAddButton();
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
        $I->click(self::$accountInformationLink);
    }

    public function clickOnAddressesLink()
    {
        $I = $this->acceptanceTester;
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
}