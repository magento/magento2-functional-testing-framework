<?php
namespace Magento\Xxyyzz\Page\Customer;

use Magento\Xxyyzz\Page\AbstractFrontendPage;

class StorefrontCustomerAccountDashboardPage extends AbstractFrontendPage
{
    /**
     * Include url of current page.
     */
    public static $URL = '/customer/account/';

    /**
     * Declare UI map for customer account dashboard page.
     */
    public static $contactInfomationText            = '.box.box-information p';
    public static $contactInfomationEditLink        = '.box.box-information .action.edit>span';
    public static $contactInfomationForgotPwdLink   = '.box.box-information .action.change-password';

    public static $newsletterText                   = '.box.box-newsletter p';
    public static $newsletterEditLink               = '.box.box-newsletter .action.edit>span';

    protected $contactInfomationName;
    protected $contactInfomationEmail;

    public function amOnCustomerAccountDashboardPage()
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::$URL);
        $I->waitForPageLoad();
        $this->setCustomerContactInformation();
    }

    public function seeContactInformationName($name)
    {
        $this->setCustomerContactInformation();
        $I = $this->acceptanceTester;
        $I->assertEquals($name, $this->contactInfomationName);
    }

    public function seeContactInformationEmail($email)
    {
        $this->setCustomerContactInformation();
        $I = $this->acceptanceTester;
        $I->assertEquals($email, $this->contactInfomationEmail);
    }

    public function seeNewsletterText($text)
    {
        $I = $this->acceptanceTester;
        $I->see($text, self::$newsletterText);
    }

    public function clickContactInformationEditLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contactInfomationEditLink);
        $I->waitForPageLoad();
        $I->seeInCurrentUrl('customer/account/edit');
    }

    public function clickContactInformationForgotPasswordLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$contactInfomationForgotPwdLink);
        $I->waitForPageLoad();
        $I->seeInCurrentUrl('customer/account/edit/changepass');
    }

    public function clickNewsletterEditLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$newsletterEditLink);
        $I->waitForPageLoad();
        $I->seeInCurrentUrl('newsletter/manage');
    }

    private function setCustomerContactInformation()
    {
        if(!isset($this->contactInfomationName) || !isset($this->contactInfomationEmail)) {
            $I = $this->acceptanceTester;
            $contacts = explode("\n", $I->grabTextFrom(self::$contactInfomationText));
            $this->contactInfomationName = $contacts[0];
            $this->contactInfomationEmail = $contacts[1];
        }
    }
}
