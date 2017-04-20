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
use Yandex\Allure\Adapter\Annotation\TestCaseId;

/**
 * Class LoginOnAdminLoginPageCest
 * 
 * Allure annotations
 * @Features({"Admin Login"})
 * @Stories({"Logging In"})
 *
 * Codeception annotations
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class LoginOnAdminLoginPageCest
{

    /**
     * Allure annotations
     * @Title("You should be able to Login to the Admin")
     * @Description("You should land on the Admin Dashboard after Logging In.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @TestCaseId("")
     * @Parameter(name = "AdminStep", value = "$I")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @return void
     */
    public function shouldBeAbleToLogin(AdminStep $I)
    {
        $I->am('an Admin');
        $I->wantTo('verify that I can login via the Admin Login page');
        
        $I->loginAsAdmin();
        $I->shouldBeOnTheAdminDashboardPage();
    }
}
