<?php
namespace Magento\Xxyyzz\Acceptance\Customer;

use Magento\Xxyyzz\AcceptanceTester;
use Magento\Xxyyzz\Page\Customer\StorefrontCustomerAccountLoginPage;
use Magento\Xxyyzz\Page\Customer\StorefrontCustomerAccountDashboardPage;
use Magento\Xxyyzz\Step\Customer\Api\CustomerApiStep;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Model\SeverityLevel;

/**
 * Class SignInCustomerFrontendCest
 *
 * Allure annotations
 * @Features({"Customer"})
 * @Stories({"Sign in existing customer storefront"})
 *
 * Codeception annotations
 * @group customer
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class SignInCustomerFrontendCest
{
    /**
     * @var array
     */
    protected $customer;

    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I, CustomerApiStep $api)
    {
        $this->customer = $I->getCustomerApiDataWithPassword();
        $api->amAdminTokenAuthenticated();
        $this->customer = array_merge($this->customer, ['id' => $api->createCustomer($this->customer)]);
        $this->customer = array_merge($this->customer['customer'], $this->customer);
        unset($this->customer['customer']);
    }

    /**
     * Sign in existing customer.
     *
     * Allure annotations
     * @Title("Method Title: Sign in existing customer storefront")
     * @Description("Method Description: Sign in existing customer storefront")
     * @TestCaseId("")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "AcceptanceTester", value = "$I")
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function createCustomerTest(AcceptanceTester $I)
    {
        $I->wantTo('create customer in frontend page.');
        StorefrontCustomerAccountLoginPage::of($I)->amOnCustomerAccountLoginPage();
        StorefrontCustomerAccountLoginPage::of($I)->fillFieldCustomerEmail($this->customer['email']);
        StorefrontCustomerAccountLoginPage::of($I)->fillFieldCustomerPassword($this->customer['password']);
        StorefrontCustomerAccountLoginPage::of($I)->clickSignInButton();

        StorefrontCustomerAccountDashboardPage::of($I)->seeContactInformationName(
            $this->customer['firstname'] . ' ' .  $this->customer['lastname']
        );
        StorefrontCustomerAccountDashboardPage::of($I)->seeContactInformationEmail($this->customer['email']);
        StorefrontCustomerAccountDashboardPage::of($I)->seeNewsletterText('subscribed to');
    }
}
