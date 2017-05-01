<?php
namespace Magento\Xxyyzz\Acceptance\Customer;

use Magento\Xxyyzz\Page\Customer\AdminCustomerPage;
use Magento\Xxyyzz\Page\Customer\AdminCustomerGrid;
use Magento\Xxyyzz\Step\Backend\AdminStep;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Model\SeverityLevel;
use Yandex\Allure\Adapter\Annotation\TestCaseId;

/**
 * Class CreateCustomerCest
 *
 * Allure annotations
 * @Features({"Customer"})
 * @Stories({"Exercise all Customer fields", "Create a basic Customer", "Create a basic Customer with an Address"})
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
        AdminCustomerGrid $customerPageGrid
    )
    {
        $I->am('an Admin');
        $I->loginAsAdmin();
        $I->goToTheAdminAllCustomersGrid();
        $customerPageGrid->clickOnAddNewCustomerButton();
    }

    /**
     * Allure annotations
     * @Title("Enter text into every field on the ADD Customer page.")
     * @Description("Enter text into ALL fields on the ADD Customer page and verify the content of the fields.")
     * @Severity(level = SeverityLevel::NORMAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminCustomerPage", value = "$adminCustomerPage")
     *
     * Codeception annotations
     * @group fields
     * @param AdminStep $I
     * @param AdminCustomerPage $adminCustomerPage
     * @return void
     */
    public function verifyThatEachFieldOnTheCustomerAddPageWorks(
        AdminStep $I,
        AdminCustomerPage $adminCustomerPage
    )
    {
        $I->wantTo('verify that I can use all of the fields on the page.');
        $customerData = $I->getCustomerData();

        $adminCustomerPage->selectAssociateToWebsiteMainWebsite();
        $adminCustomerPage->selectGroupWholesale();
        $adminCustomerPage->enterPrefix($customerData['prefix']);
        $adminCustomerPage->enterFirstName($customerData['firstname']);
        $adminCustomerPage->enterMiddleName($customerData['middlename']);
        $adminCustomerPage->enterLastName($customerData['lastname']);
        $adminCustomerPage->enterSuffix($customerData['suffix']);
        $adminCustomerPage->enterEmailAddress($customerData['email']);
        $adminCustomerPage->enterDateOfBirth($customerData['dateOfBirth']);
        $adminCustomerPage->enterTaxVatNumber($customerData['taxVatNumber']);
        $adminCustomerPage->selectGenderFemale();
        $adminCustomerPage->selectSendWelcomeEmailFromDefaultStoreView();

        $adminCustomerPage->verifyAssociateToWebsiteMainWebsite();
        $adminCustomerPage->verifyGroupWholesale();
        $adminCustomerPage->verifyPrefix($customerData['prefix']);
        $adminCustomerPage->verifyFirstName($customerData['firstname']);
        $adminCustomerPage->verifyMiddleName($customerData['middlename']);
        $adminCustomerPage->verifyLastName($customerData['lastname']);
        $adminCustomerPage->verifySuffix($customerData['suffix']);
        $adminCustomerPage->verifyEmailAddress($customerData['email']);
        $adminCustomerPage->verifyDateOfBirth($customerData['dateOfBirth']);
        $adminCustomerPage->verifyTaxVatNumber($customerData['taxVatNumber']);
        $adminCustomerPage->verifyGenderFemale();
        $adminCustomerPage->verifySendWelcomeEmailFromDefaultStoreView();
    }

    /**
     * Allure annotations
     * @Title("Enter text into every field on the ADD ADDRESS area of the Customer page.")
     * @Description("Enter text into ALL fields on the ADD ADDRESS area on the Customer page and verify the content of the fields.")
     * @Severity(level = SeverityLevel::NORMAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminCustomerPage", value = "$adminCustomerPage")
     *
     * Codeception annotations
     * @group fields
     * @param AdminStep $I
     * @param AdminCustomerPage $adminCustomerPage
     * @return void
     */
    public function verifyThatEachFieldOnTheCustomerAddAddressAreaWorks(
        AdminStep $I,
        AdminCustomerPage $adminCustomerPage
    )
    {
        $I->wantTo('verify that I can use all of the fields in the Add Address area.');
        $customerData = $I->getCustomerData();

        $adminCustomerPage->clickOnAddressesLink();
        $adminCustomerPage->clickOnAddNewAddressButton();

        $adminCustomerPage->clickOnAddNewAddressDefaultBillingAddress();
        $adminCustomerPage->clickOnAddNewAddressDefaultShippingAddress();
        
        $adminCustomerPage->enterAddAddressPrefix($customerData['prefix']);
        $adminCustomerPage->enterAddAddressFirstName($customerData['firstname']);
        $adminCustomerPage->enterAddAddressMiddleName($customerData['middlename']);
        $adminCustomerPage->enterAddAddressLastName($customerData['lastname']);
        $adminCustomerPage->enterAddAddressSuffix($customerData['suffix']);
        $adminCustomerPage->enterAddAddressCompany($customerData['company']);
        $adminCustomerPage->enterAddAddressAddress1($customerData['address']['address1']);
        $adminCustomerPage->enterAddAddressAddress2($customerData['address']['address2']);
        $adminCustomerPage->enterAddAddressCity($customerData['address']['city']);
        $adminCustomerPage->enterAddAddressCountry($customerData['address']['country']);
        $adminCustomerPage->enterAddAddressState($customerData['address']['state']);
        $adminCustomerPage->enterAddAddressZipPostalCode($customerData['address']['zipCode']);
        $adminCustomerPage->enterAddAddressPhoneNumber($customerData['phoneNumber']);
        $adminCustomerPage->enterAddAddressVatNumber($customerData['taxVatNumber']);

        $adminCustomerPage->verifyAddAddressDefaultBillingAddress(true);
        $adminCustomerPage->verifyAddAddressDefaultShippingAddress(true);

        $adminCustomerPage->verifyAddAddressPrefix($customerData['prefix']);
        $adminCustomerPage->verifyAddAddressFirstName($customerData['firstname']);
        $adminCustomerPage->verifyAddAddressMiddleName($customerData['middlename']);
        $adminCustomerPage->verifyAddAddressLastName($customerData['lastname']);
        $adminCustomerPage->verifyAddAddressSuffix($customerData['suffix']);
        $adminCustomerPage->verifyAddAddressCompany($customerData['company']);
        $adminCustomerPage->verifyAddAddressAddress1($customerData['address']['address1']);
        $adminCustomerPage->verifyAddAddressAddress2($customerData['address']['address2']);
        $adminCustomerPage->verifyAddAddressCity($customerData['address']['city']);
        $adminCustomerPage->verifyAddAddressCountry($customerData['address']['country']);
        $adminCustomerPage->verifyAddAddressState($customerData['address']['state']);
        $adminCustomerPage->verifyAddAddressZipPostalCode($customerData['address']['zipCode']);
        $adminCustomerPage->verifyAddAddressPhoneNumber($customerData['phoneNumber']);
        $adminCustomerPage->verifyAddAddressVatNumber($customerData['taxVatNumber']);
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
        $customerData = $I->getCustomerData();

        $adminCustomerPage->enterFirstName($customerData['firstname']);
        $adminCustomerPage->enterLastName($customerData['lastname']);
        $adminCustomerPage->enterEmailAddress($customerData['email']);
        $adminCustomerPage->selectAssociateToWebsiteMainWebsite();
        $adminCustomerPage->selectGroupGeneral();

        $adminCustomerPage->clickOnSaveCustomerButton();

        $adminCustomerGrid->performSearchByKeyword($customerData['email']);
        $adminCustomerGrid->clickOnActionLinkFor($customerData['email']);

        $adminCustomerPage->clickOnAccountInformationLink();
        $adminCustomerPage->verifyFirstName($customerData['firstname']);
        $adminCustomerPage->verifyLastName($customerData['lastname']);
        $adminCustomerPage->verifyEmailAddress($customerData['email']);
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
        $customerData = $I->getCustomerData();
        
        $adminCustomerPage->clickOnAddressesLink();
        $adminCustomerPage->clickOnAddNewAddressButton();

        $adminCustomerPage->clickOnAddNewAddressDefaultBillingAddress();
        $adminCustomerPage->clickOnAddNewAddressDefaultShippingAddress();

        $adminCustomerPage->enterAddAddressPrefix($customerData['prefix']);
        $adminCustomerPage->enterAddAddressFirstName($customerData['firstname']);
        $adminCustomerPage->enterAddAddressMiddleName($customerData['middlename']);
        $adminCustomerPage->enterAddAddressLastName($customerData['lastname']);
        $adminCustomerPage->enterAddAddressSuffix($customerData['suffix']);
        $adminCustomerPage->enterAddAddressCompany($customerData['company']);
        $adminCustomerPage->enterAddAddressAddress1($customerData['address']['address1']);
        $adminCustomerPage->enterAddAddressAddress2($customerData['address']['address2']);
        $adminCustomerPage->enterAddAddressCity($customerData['address']['city']);
        $adminCustomerPage->enterAddAddressCountry($customerData['address']['country']);
        $adminCustomerPage->enterAddAddressState($customerData['address']['state']);
        $adminCustomerPage->enterAddAddressZipPostalCode($customerData['address']['zipCode']);
        $adminCustomerPage->enterAddAddressPhoneNumber($customerData['phoneNumber']);
        $adminCustomerPage->enterAddAddressVatNumber($customerData['taxVatNumber']);

        $adminCustomerPage->clickOnAccountInformationLink();

        $adminCustomerPage->enterFirstName($customerData['firstname']);
        $adminCustomerPage->enterLastName($customerData['lastname']);
        $adminCustomerPage->enterEmailAddress($customerData['email']);
        $adminCustomerPage->selectAssociateToWebsiteMainWebsite();
        $adminCustomerPage->selectGroupGeneral();

        $adminCustomerPage->clickOnSaveCustomerButton();

        $adminCustomerGrid->performSearchByKeyword($customerData['email']);
        $adminCustomerGrid->clickOnActionLinkFor($customerData['email']);

        $adminCustomerPage->clickOnAddressesLink();
        $adminCustomerPage->verifyAddAddressDefaultBillingAddress(true);
        $adminCustomerPage->verifyAddAddressDefaultShippingAddress(true);

        $adminCustomerPage->verifyAddAddressPrefix($customerData['prefix']);
        $adminCustomerPage->verifyAddAddressFirstName($customerData['firstname']);
        $adminCustomerPage->verifyAddAddressMiddleName($customerData['middlename']);
        $adminCustomerPage->verifyAddAddressLastName($customerData['lastname']);
        $adminCustomerPage->verifyAddAddressSuffix($customerData['suffix']);
        $adminCustomerPage->verifyAddAddressCompany($customerData['company']);
        $adminCustomerPage->verifyAddAddressAddress1($customerData['address']['address1']);
        $adminCustomerPage->verifyAddAddressAddress2($customerData['address']['address2']);
        $adminCustomerPage->verifyAddAddressCity($customerData['address']['city']);
        $adminCustomerPage->verifyAddAddressCountry($customerData['address']['country']);
        $adminCustomerPage->verifyAddAddressState($customerData['address']['state']);
        $adminCustomerPage->verifyAddAddressZipPostalCode($customerData['address']['zipCode']);
        $adminCustomerPage->verifyAddAddressPhoneNumber($customerData['phoneNumber']);
        $adminCustomerPage->verifyAddAddressVatNumber($customerData['taxVatNumber']);
    }
}