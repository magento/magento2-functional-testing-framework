<?php
namespace Magento\Xxyyzz\Page\Customer;

use Magento\Xxyyzz\Page\AbstractFrontendPage;

class StorefrontCustomerAccountCreatePage extends AbstractFrontendPage
{
    /**
     * Include url of current page.
     */
    public static $URL = '/customer/account/create/';

    /**
     * Declare UI map for new customer data.
     */
    public static $customerFirstName        = '#firstname';
    public static $customerLastName         = '#lastname';
    public static $newsletterSubscribe      = '#is_subscribed';
    public static $customerEmail            = '#email_address';
    public static $customerPassword         = '#password';
    public static $customerConfirmPassword  = '#password-confirmation';
    public static $createAccountSubmitButton= '.action.submit.primary';

    public function amOnCustomerAccountCreatePage()
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::$URL);
        $I->waitForPageLoad();
    }

    public function fillFieldFirstName($firstName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$customerFirstName, $firstName);
    }

    public function fillFieldLastName($lastName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$customerLastName, $lastName);
    }

    public function fillFieldEmail($email)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$customerEmail, $email);
    }

    public function fillFieldPassword($password)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$customerPassword, $password);
    }

    public function fillFieldConfirmPassword($password)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$customerConfirmPassword, $password);
    }

    public function setNewsletterSubscribe($checked)
    {
        $I = $this->acceptanceTester;
        try {
            $I->dontSeeCheckboxIsChecked(self::$newsletterSubscribe);
            if ($checked) {
                $I->click(self::$newsletterSubscribe);
            }
        } catch (\Exception $e) {
            $I->seeCheckboxIsChecked(self::$newsletterSubscribe);
            if ($checked) {
                $I->click(self::$newsletterSubscribe);
            }
        }
    }

    public function clickCreateAccountButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$createAccountSubmitButton);
        $I->waitForPageLoad();
        $I->seeInCurrentUrl('customer/account');
    }
}
