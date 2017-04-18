<?php
namespace Magento\Xxyyzz\Acceptance\Customer;

use Magento\Xxyyzz\Page\Customer\Admin\CustomerPage;
use Magento\Xxyyzz\Step\Backend\AdminStep;
use Magento\Xxyyzz\Page\Content\Admin\AdminCMSPage;
use Magento\Xxyyzz\Page\AbstractFrontendPage;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Model\SeverityLevel;

/**
 * Class CreateCustomerCest
 *
 * Allure annotations
 * @Stories({"Customers - All Customers"})
 * @Features({"Customers"})
 *
 * Codeception annotations
 * @group customers
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class CreateCustomerCest
{
    public function _before(
        AdminStep $I,
        CustomerPage $customerPage
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
     * @Title("Enter text into every field on the Customer - Page.")
     * @Description("Enter text into ALL fields and verify the contents of the fields.")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "CustomerPage", value = "$customerPage")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param CustomerPage $customerPage
     * @return void
     */
    public function verifyThatEachFieldOnTheCustomerAddPageWorks(
        AdminStep $I,
        CustomerPage $customerPage
    )
    {
        $I->wantTo('verify that I can use all of the fields on the page.');
        $customerData = $I->getCustomerData();

        $customerPage->selectAssociateToWebsiteMainWebsite();
        $customerPage->selectGroupWholesale();
        $customerPage->enterPrefix($customerData['prefix']);
        $customerPage->enterFirstName($customerData['firstName']);
        $customerPage->enterMiddleName($customerData['middleName']);
        $customerPage->enterLastName($customerData['lastName']);
        $customerPage->enterSuffix($customerData['suffix']);
        $customerPage->enterEmailAddress($customerData['email']);
        $customerPage->enterDateOfBirth($customerData['dateOfBirth']);
        $customerPage->enterTaxVatNumber($customerData['taxVatNumber']);
        $customerPage->selectGenderFemale();
        $customerPage->selectSendWelcomeEmailFromDefaultStoreView();

        $customerPage->verifyAssociateToWebsiteMainWebsite();
        $customerPage->verifyGroupWholesale();
        $customerPage->verifyPrefix($customerData['prefix']);
        $customerPage->verifyFirstName($customerData['firstName']);
        $customerPage->verifyMiddleName($customerData['middleName']);
        $customerPage->verifyLastName($customerData['lastName']);
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
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminCMSPage", value = "$adminCMSPage")
     * @Parameter(name = "CustomerPage", value = "$customerPage")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param AdminCMSPage $adminCMSPage
     * @param CustomerPage $customerPage
     * @group banana
     * @return void
     */
    public function createCustomerAccountTest(
        AdminStep $I,
        CustomerPage $customerPage
    )
    {
        $I->wantTo('verify Customer account in admin');
        $customer = $I->getCustomerData();

        $customerPage->enterFirstName($customer['firstName']);
        $customerPage->enterLastName($customer['lastName']);
        $customerPage->enterEmailAddress($customer['email']);
        $customerPage->selectAssociateToWebsiteMainWebsite();
        $customerPage->selectGroupGeneral();
        
        $customerPage->clickOnAdminSaveAndContinueEdit();
        $customerPage->clickOnAccountInformationLink();

        $customerPage->verifyFirstName($customer['firstName']);
        $customerPage->verifyLastName($customer['lastName']);
        $customerPage->verifyEmailAddress($customer['email']);
        $customerPage->verifyAssociateToWebsiteMainWebsite();
        $customerPage->verifyGroupGeneral();
    }
}