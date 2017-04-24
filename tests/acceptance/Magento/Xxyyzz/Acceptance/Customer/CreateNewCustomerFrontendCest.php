<?php
namespace Magento\Xxyyzz\Acceptance\Customer;

use Magento\Xxyyzz\AcceptanceTester;
use Magento\Xxyyzz\Page\Customer\StorefrontCustomerAccountCreatePage;
use Magento\Xxyyzz\Page\Customer\StorefrontCustomerAccountDashboardPage;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Model\SeverityLevel;
use Yandex\Allure\Adapter\Annotation\TestCaseId;

/**
 * Class SignInCustomerFrontendCest
 *
 * Allure annotations
 * @Features({"Customer"})
 * @Stories({"Create Customer via Storefront"})
 *
 * Codeception annotations
 * @group customer
 * @group add
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class CreateNewCustomerFrontendCest
{
    /**
     * @var array
     */
    protected $customer;

    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I)
    {
        $this->customer = $I->getCustomerApiDataWithPassword();
        $this->customer = array_merge($this->customer['customer'], $this->customer);
        unset($this->customer['customer']);
    }

    /**
     * Create customer.
     *
     * Allure annotations
     * @Title("Create a a basic Customer - Storefront")
     * @Description("Create a basic Customer via the Storefront.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "Admin", value = "$I")
     *
     * Codeception annotations
     * @param AcceptanceTester $I
     * @return void
     */
    public function createCustomerTest(AcceptanceTester $I)
    {
        $I->wantTo('create customer in frontend page.');
        StorefrontCustomerAccountCreatePage::of($I)->amOnCustomerAccountCreatePage();
        StorefrontCustomerAccountCreatePage::of($I)->fillFieldFirstName($this->customer['firstname']);
        StorefrontCustomerAccountCreatePage::of($I)->fillFieldLastName($this->customer['lastname']);
        StorefrontCustomerAccountCreatePage::of($I)->setNewsletterSubscribe(true);
        StorefrontCustomerAccountCreatePage::of($I)->fillFieldEmail($this->customer['email']);
        StorefrontCustomerAccountCreatePage::of($I)->fillFieldPassword($this->customer['password']);
        StorefrontCustomerAccountCreatePage::of($I)->fillFieldConfirmPassword($this->customer['password']);
        StorefrontCustomerAccountCreatePage::of($I)->clickCreateAccountButton();
        StorefrontCustomerAccountDashboardPage::of($I)->seeContactInformationName(
            $this->customer['firstname'] . ' ' .  $this->customer['lastname']
        );
        StorefrontCustomerAccountDashboardPage::of($I)->seeContactInformationEmail($this->customer['email']);
        StorefrontCustomerAccountDashboardPage::of($I)->seeNewsletterText('subscribed');
    }
}
