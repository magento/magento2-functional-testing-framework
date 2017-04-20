<?php
namespace Magento\Xxyyzz\Acceptance\User;

use Magento\Xxyyzz\Step\Backend\AdminStep;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Model\SeverityLevel;

/**
 * Class CannotAccessAdminAfterLoggingOutCest
 *
 * Allure annotations
 * @Features({"Admin Login"})
 * @Stories({"Prevent access after Logging Out"})
 *
 * Codeception annotations
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class CannotAccessAdminAfterLoggingOutCest
{
    /**
     * Allure annotations
     * @Title("YOu should NOT be able to access an Admin page after your Logged Out.")
     * @Description("Attempt to access an Admin page after Logging Out.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @return void
     */
    public function shouldNotBeAbleToAccessAdminAfterLogout(AdminStep $I)
    {
        $I->am('an Admin');
        $I->wantTo('make sure you cannot access Admin pages after logging out');
        
        $I->loginAsAdmin();
        $I->goToTheAdminLogoutPage();

        $I->goToRandomAdminPage();
        $I->shouldBeOnTheAdminLoginPage();

        $I->goToRandomAdminPage();
        $I->shouldBeOnTheAdminLoginPage();

        $I->goToRandomAdminPage();
        $I->shouldBeOnTheAdminLoginPage();
    }
}
