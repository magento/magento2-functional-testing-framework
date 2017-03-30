<?php
namespace Magento\Xxyyzz\Acceptance\SampleTests;

use Magento\Xxyyzz\Step\Backend\Admin;
use \Codeception\Scenario;

class AdminCest
{
    /**
     * @env chrome
     * @env firefox
     * @env phantomjs
     * @group skip
     * @param Scenario $scenario
     * @param Admin $I
     * @return void
     */
    public function amStepObject(Scenario $scenario, Admin $I)
    {
        $I->wantTo('demo the usage of StepObject in Cest');
        $I->goToTheAdminLoginPage();
        $I->loginAsAdmin();
    }
}