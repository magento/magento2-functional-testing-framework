<?php
namespace Magento\Xxyyzz\Acceptance\Customer;

use Magento\Xxyyzz\Page\Customer\AdminCustomerPage;
use Magento\Xxyyzz\Page\Customer\AdminCustomerGrid;
use Magento\Xxyyzz\Step\Backend\AdminStep;
use Magento\Xxyyzz\Page\Cms\AdminCmsPage;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Model\SeverityLevel;

/**
 * Class CreateCustomerCest
 *
 * Allure annotations
 * @Features({"Customer"})
 * @Stories({"Exercise all Customer fields", "Create a basic Customer", "Create a basic Customer with an Address"})
 * @Title("Exercise all fields, create basic Customer and basic Customer with an Address")
 * @Description("Attempt to enter Text into all fields, create a basic Customer and create a basic Customer with an Address.")
 *
 * Codeception annotations
 * @group customer
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class CreateCustomerCest
{
    public function _before(
        AdminStep $I,
        AdminCustomerPage $customerPage
    )
    {
        $I->am('an Admin');
        $I->loginAsAdmin();
        $I->goToTheAdminCustomersAllCustomersPage();
        $customerPage->clickOnAddCustomerButton();
        $I->waitForSpinnerToDisappear();
    }

    /**
     * Allure annotations
     * @Title("Enter text into every field on the ADD Customer page.")
     * @Description("Enter text into ALL fields on the ADD Customer page and verify the contents of the fields.")
     * @Severity(level = SeverityLevel::NORMAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminCustomerPage", value = "$customerPage")
     *
     * Codeception annotations
     * @group fields
     * @param AdminStep $I
     * @param AdminCustomerPage $customerPage
     * @return void
     */
    public function verifyThatEachFieldOnTheCustomerAddPageWorks(
        AdminStep $I,
        AdminCustomerPage $customerPage
    )
    {
        $I->wantTo('verify that I can use all of the fields on the page.');
        $customerData = $I->getCustomerData();

        $customerPage->selectAssociateToWebsiteMainWebsite();
        $customerPage->selectGroupWholesale();
        $customerPage->enterPrefix($customerData['prefix']);
        $customerPage->enterFirstName($customerData['firstname']);
        $customerPage->enterMiddleName($customerData['middlename']);
        $customerPage->enterLastName($customerData['lastname']);
        $customerPage->enterSuffix($customerData['suffix']);
        $customerPage->enterEmailAddress($customerData['email']);
        $customerPage->enterDateOfBirth($customerData['dateOfBirth']);
        $customerPage->enterTaxVatNumber($customerData['taxVatNumber']);
        $customerPage->selectGenderFemale();
        $customerPage->selectSendWelcomeEmailFromDefaultStoreView();

        $customerPage->verifyAssociateToWebsiteMainWebsite();
        $customerPage->verifyGroupWholesale();
        $customerPage->verifyPrefix($customerData['prefix']);
        $customerPage->verifyFirstName($customerData['firstname']);
        $customerPage->verifyMiddleName($customerData['middlename']);
        $customerPage->verifyLastName($customerData['lastname']);
        $customerPage->verifySuffix($customerData['suffix']);
        $customerPage->verifyEmailAddress($customerData['email']);
        $customerPage->verifyDateOfBirth($customerData['dateOfBirth']);
        $customerPage->verifyTaxVatNumber($customerData['taxVatNumber']);
        $customerPage->verifyGenderFemale();
        $customerPage->verifySendWelcomeEmailFromDefaultStoreView();
    }

    /**
     * Allure annotations
     * @Title("Create a new Customer account using the REQUIRED fields only.")
     * @Description("Enter text into the REQUIRED fields, SAVE the content and VERIFY it on the Admin page.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminCustomerPage", value = "$adminCustomerPage")
     * @Parameter(name = "AdminCustomerGrid", value = "$adminCustomerGrid")
     *
     * Codeception annotations
     * @group add
     * @param AdminStep $I
     * @param AdminCustomerPage $adminCustomerPage
     * @param AdminCustomerGrid $adminCustomerGrid
     * @return void
     */
    public function createBasicCustomerAccountTest(
        AdminStep $I,
        AdminCustomerPage $adminCustomerPage,
        AdminCustomerGrid $adminCustomerGrid
    )
    {
        $I->wantTo('verify basic Customer creation in admin');
        $customer = $I->getCustomerData();

        $adminCustomerPage->enterFirstName($customer['firstname']);
        $adminCustomerPage->enterLastName($customer['lastname']);
        $adminCustomerPage->enterEmailAddress($customer['email']);
        $adminCustomerPage->selectAssociateToWebsiteMainWebsite();
        $adminCustomerPage->selectGroupGeneral();

        $adminCustomerPage->clickOnAdminSaveButton();
        $adminCustomerGrid->performSearchByKeyword($customer['email']);

        $adminCustomerGrid->clickOnActionLinkFor($customer['email']);
        $adminCustomerPage->clickOnAccountInformationLink();

        $adminCustomerPage->verifyFirstName($customer['firstname']);
        $adminCustomerPage->verifyLastName($customer['lastname']);
        $adminCustomerPage->verifyEmailAddress($customer['email']);
        $adminCustomerPage->verifyAssociateToWebsiteMainWebsite();
        $adminCustomerPage->verifyGroupGeneral();
    }

    /**
     * Allure annotations
     * @Title("Create a new Customer account using the REQUIRED fields with an Address.")
     * @Description("Enter text into the REQUIRED fields, SAVE the content and VERIFY it on the Admin page.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminCustomerPage", value = "$adminCustomerPage")
     * @Parameter(name = "AdminCustomerGrid", value = "$adminCustomerGrid")
     *
     * Codeception annotations
     * @group skip
     * @group add
     * @param AdminStep $I
     * @param AdminCustomerPage $adminCustomerPage
     * @param AdminCustomerGrid $adminCustomerGrid
     * @return void
     */
    public function createBasicCustomerAccountWithAddressTest(
        AdminStep $I,
        AdminCustomerPage $adminCustomerPage,
        AdminCustomerGrid $adminCustomerGrid
    )
    {
        $I->wantTo('verify basic Customer creation in admin');
        $customer = $I->getCustomerData();

        $adminCustomerPage->enterFirstName($customer['firstname']);
        $adminCustomerPage->enterLastName($customer['lastname']);
        $adminCustomerPage->enterEmailAddress($customer['email']);
        $adminCustomerPage->selectAssociateToWebsiteMainWebsite();
        $adminCustomerPage->selectGroupGeneral();
        
        $adminCustomerPage->clickOnAddressesLink();
        $adminCustomerPage->clickOnAddNewAddressButton();
        
        $adminCustomerPage->enterAddAddressPrefix($customer['prefix']);
        $adminCustomerPage->enterAddAddressFirstName($customer['firstname']);
        $adminCustomerPage->enterAddAddressMiddleName($customer['middlename']);
        $adminCustomerPage->enterAddAddressLastName($customer['lastname']);
        $adminCustomerPage->enterAddAddressSuffix($customer['suffix']);
        $adminCustomerPage->enterAddAddressCompany($customer['company']);
        $adminCustomerPage->enterAddAddressAddress1($customer['address']['address1']);
        $adminCustomerPage->enterAddAddressAddress2($customer['address']['address2']);
        $adminCustomerPage->enterAddAddressCity($customer['address']['city']);
        $adminCustomerPage->enterAddAddressCountry($customer['address']['country']);
        $adminCustomerPage->enterAddAddressStateProvince($customer['address']['state']);
        $adminCustomerPage->enterAddAddressZipPostalCode($customer['address']['zipCode']);
        $adminCustomerPage->enterAddAddressPhoneNumber($customer['phoneNumber']);
        $adminCustomerPage->enterAddAddressVatNumber($customer['taxVatNumber']);

        $adminCustomerPage->clickOnAdminSaveButton();
        $adminCustomerGrid->performSearchByKeyword($customer['email']);

        $adminCustomerGrid->clickOnActionLinkFor($customer['email']);
        $adminCustomerPage->clickOnAccountInformationLink();

        
    }
}