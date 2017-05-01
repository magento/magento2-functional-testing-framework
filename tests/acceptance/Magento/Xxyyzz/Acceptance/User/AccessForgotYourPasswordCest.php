<?php
namespace Magento\Xxyyzz\Acceptance\User;

use Magento\Xxyyzz\Page\Backend\AdminLogin;
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
 * Class AccessForgotYourPasswordCest
 *
 * Allure annotations
 * @Features("Admin Login")
 * @Stories("Forgot Your Password")
 *
 * Codeception annotations
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class AccessForgotYourPasswordCest
{
    public function _before(AdminStep $I)
    {
        $I->goToTheAdminLoginPage();
    }

    /**
     * Allure annotations
     * @Title("You should land on the Forgot Your Password page.")
     * @Description("You should be able to access the Forgot Your Password page.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminLogin", value = "$adminLogin")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param AdminLogin $adminLogin
     * @return void
     */
    public function shouldLandOnTheForgotYourPasswordPage(AdminStep $I, AdminLogin $adminLogin)
    {
        $I->am('an Admin');
        $I->wantTo('see if I can access the Forgot Your Password page');
        $adminLogin->clickOnForgotYourPassword();
        $I->shouldBeOnTheAdminForgotYourPasswordPage();
        $adminLogin->shouldSeeTheForgotYourPasswordFields();
        $I->see('Password Help');
    }

    /**
     * Allure annotations
     * @Title("You should be able to access the Login page from the Forgot Your Password page")
     * @Description("You should land on the Login page after clicking on 'Back to Sign In'.")
     * @Severity(level = SeverityLevel::TRIVIAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminLogin", value = "$adminLogin")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param AdminLogin $adminLogin
     * @return void
     */
    public function shouldLandOnTheLoginPageWhenBackToSignInIsClicked(AdminStep $I, AdminLogin $adminLogin)
    {
        $I->am('an Admin');
        $I->wantTo('see if I can access the Login page from the Forgot Your Password page');
        $adminLogin->clickOnForgotYourPassword();
        $adminLogin->clickOnBackToSignIn();
        $I->shouldBeOnTheAdminLoginPage();
    }

    /**
     * Allure annotations
     * @Title("You should be able to access the Login page from the Forgot Your Password page")
     * @Description("You should land on the Login page after clicking on the Logo.")
     * @Severity(level = SeverityLevel::TRIVIAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminLogin", value = "$adminLogin")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param AdminLogin $adminLogin
     * @return void
     */
    public function shouldLandOnTheLoginPageWhenTheLogoIsClicked(AdminStep $I, AdminLogin $adminLogin)
    {
        $I->am('an Admin');
        $I->wantTo('see if I can access the Login page by clicking on the Logo');
        $adminLogin->clickOnMagentoLogo();
        $I->shouldBeOnTheAdminLoginPage();

        $adminLogin->clickOnForgotYourPassword();
        $adminLogin->clickOnMagentoLogo();
        $I->shouldBeOnTheAdminLoginPage();
    }
}
