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
     * @Title("Sign in existing customer storefront")
     * @Description("Sign in existing customer storefront")
     * @TestCaseId("")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "AcceptanceTester", value = "$I")
     * @Parameter(name = "StorefrontCustomerAccountLoginPage", value = "$customerAccountLoginPage")
     * @Parameter(name = "StorefrontCustomerAccountDashboardPage", value = "$customerAccountDashboardPage")
     *
     * @param AcceptanceTester $I
     * @param StorefrontCustomerAccountLoginPage $customerAccountLoginPage
     * @param StorefrontCustomerAccountDashboardPage $customerAccountDashboardPage
     * @return void
     */
    public function createCustomerTest(
        AcceptanceTester $I,
        StorefrontCustomerAccountLoginPage $customerAccountLoginPage,
        StorefrontCustomerAccountDashboardPage $customerAccountDashboardPage
    ) {
        $I->wantTo('create customer in frontend page.');
        $customerAccountLoginPage->amOnCustomerAccountLoginPage();
        $customerAccountLoginPage->fillFieldCustomerEmail($this->customer['email']);
        $customerAccountLoginPage->fillFieldCustomerPassword($this->customer['password']);
        $customerAccountLoginPage->clickSignInButton();

        $customerAccountDashboardPage->seeContactInformationName(
            $this->customer['firstname'] . ' ' .  $this->customer['lastname']
        );
        $customerAccountDashboardPage->seeContactInformationEmail($this->customer['email']);
        $customerAccountDashboardPage->seeNewsletterText('subscribed to');
    }
}
