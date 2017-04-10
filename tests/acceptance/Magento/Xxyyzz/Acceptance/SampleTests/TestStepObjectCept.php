<?php
namespace Magento\Xxyyzz\Acceptance\SampleTests;

// @group skip
$I = new \Magento\Xxyyzz\Step\Backend\AdminStep(\Codeception\Scenario::$scenario);
$I->wantTo('demo the usage of StepObject in Cept');
$I->goToTheAdminLoginPage();
$I->loginAsAdmin();