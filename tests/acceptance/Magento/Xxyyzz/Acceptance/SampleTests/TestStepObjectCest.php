<?php
namespace Magento\Xxyyzz\Acceptance\SampleTests;

use Magento\Xxyyzz\Step\Backend\AdminStep;
use \Codeception\Scenario;

class AdminCest
{
    /**
     * @env chrome
     * @env firefox
     * @env phantomjs
     * @group skip
     * @param Scenario $scenario
     * @param AdminStep $I
     * @return void
     */
    public function amStepObject(Scenario $scenario, AdminStep $I)
    {
        $I->wantTo('demo the usage of StepObject in Cest');
        $I->loginAsAdmin();
    }
}