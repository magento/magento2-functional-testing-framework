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
 * @Stories({"Create new customer storefront"})
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
     * @Title("Create new customer storefront")
     * @Description("Create new customer storefront")
     * @TestCaseId("")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "AcceptanceTester", value = "$I")
     * @Parameter(name = "StorefrontCustomerAccountCreatePage", value = "$customerAccountCreatePage")
     * @Parameter(name = "StorefrontCustomerAccountDashboardPage", value = "$customerAccountDashboardPage")
     *
     * @param AcceptanceTester $I
     * @param StorefrontCustomerAccountCreatePage $customerAccountCreatePage
     * @param StorefrontCustomerAccountDashboardPage $customerAccountDashboardPage
     * @return void
     */
    public function createCustomerTest(
        AcceptanceTester $I,
        StorefrontCustomerAccountCreatePage $customerAccountCreatePage,
        StorefrontCustomerAccountDashboardPage $customerAccountDashboardPage
    ) {
        $I->wantTo('create customer in frontend page.');
        $customerAccountCreatePage->amOnCustomerAccountCreatePage();
        $customerAccountCreatePage->fillFieldFirstName($this->customer['firstname']);
        $customerAccountCreatePage->fillFieldLastName($this->customer['lastname']);
        $customerAccountCreatePage->setNewsletterSubscribe(true);
        $customerAccountCreatePage->fillFieldEmail($this->customer['email']);
        $customerAccountCreatePage->fillFieldPassword($this->customer['password']);
        $customerAccountCreatePage->fillFieldConfirmPassword($this->customer['password']);
        $customerAccountCreatePage->clickCreateAccountButton();
        $customerAccountDashboardPage->seeContactInformationName(
            $this->customer['firstname'] . ' ' .  $this->customer['lastname']
        );
        $customerAccountDashboardPage->seeContactInformationEmail($this->customer['email']);
        $customerAccountDashboardPage->seeNewsletterText('subscribed');
    }
}
