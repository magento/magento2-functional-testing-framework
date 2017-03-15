<?php
// @env firefox
// @env chrome
// @env phantomjs
use Step\Acceptance\Admin as AdminTester;

$I = new AdminTester($scenario);
$I->wantTo('verify that I can login via the Admin Login page');

$I->goToTheAdminLoginPage();
$I->loginAsAnExistingAdmin();
$I->shouldBeOnTheAdminDashboardPage();