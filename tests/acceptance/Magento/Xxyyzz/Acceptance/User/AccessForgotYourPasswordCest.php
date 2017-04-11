<?php
namespace Magento\Xxyyzz\Acceptance\User;

use Magento\Xxyyzz\Page\Backend\Admin\AdminLogin;
use Magento\Xxyyzz\Step\Backend\AdminStep;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Annotation\TestCaseId;
use Yandex\Allure\Adapter\Annotation\Parameter;

/**
 * @Features("Admin Login")
 * @Stories("Forgot Your Password")
 * @TestCaseId("ZEP-1")
 * @group test
 */
class AccessForgotYourPasswordCest
{
    public function _before(AdminStep $I)
    {
        $I->goToTheAdminLoginPage();
    }

    /**
     * @env chrome
     * @env firefox
     * @env phantomjs
     * @Title("You should land on the Forgot Your Password page.")
     * @Severity("critical")
     * @Parameter("My Param")
     */
    public function shouldLandOnTheForgotYourPasswordPage(AdminStep $I, AdminLogin $adminLogin)
    {
        $I->wantTo('see if I can access the Forgot Your Password page');
        $adminLogin->clickOnForgotYourPassword();
        $I->shouldBeOnTheForgotYourPasswordPage();
        $adminLogin->shouldSeeTheForgotYourPasswordFields();
        $I->see('Password Help');
    }

    /**
     * @env chrome
     * @env firefox
     * @env phantomjs
     * @Title("You should land on the Login page after clicking on 'Back to Sign In'.")
     * @Severity("trivial")
     */
    public function shouldLandOnTheLoginPageWhenBackToSignInIsClicked(AdminStep $I, AdminLogin $adminLogin)
    {
        $I->wantTo('see if I can access the Login page from the Forgot Your Password page');
        $adminLogin->clickOnForgotYourPassword();
        $adminLogin->clickOnBackToSignIn();
        $I->shouldBeOnTheAdminLoginPage();
    }

    /**
     * @env chrome
     * @env firefox
     * @env phantomjs
     * @Title("You should land on the Login page after clicking on the Logo.")
     * @Severity("normal")
     */
    public function shouldLandOnTheLoginPageWhenTheLogoIsClicked(AdminStep $I, AdminLogin $adminLogin)
    {
        $I->wantTo('see if I can access the Login page by clicking on the Logo');
        $adminLogin->clickOnMagentoLogo();
        $I->shouldBeOnTheAdminLoginPage();

        $adminLogin->clickOnForgotYourPassword();
        $adminLogin->clickOnMagentoLogo();
        $I->shouldBeOnTheAdminLoginPage();
    }
}
